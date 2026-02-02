<?php
// /app/models/Equipement.php

require_once(__DIR__ . '/../../core/Model.php');

class Equipement extends Model {
    public function __construct() {
        parent::__construct('materiel');
        $this->setPrimaryKey('id');
    }

    /**
     * Récupère les équipements assignés à l'utilisateur connecté avec leur état réel
     */
    public function getMesEquipements($userRole, $userId) {
        $sql = "SELECT m.*, t.nom as type_nom, 
                (SELECT r.statut FROM reclamation r 
                 WHERE r.materiel_id = m.id 
                 AND r.statut IN ('en_attente', 'en_cours') 
                 ORDER BY r.date_reclamation DESC 
                 LIMIT 1) as etat_reel,
                (SELECT COUNT(*) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as nb_reclamations,
                (SELECT MAX(r.date_reclamation) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as derniere_reclamation
                FROM materiel m 
                LEFT JOIN type t ON m.type_id = t.id 
                WHERE m.user_role = :user_role AND m.id_user = :user_id
                ORDER BY m.date_enregistrement DESC";

        return $this->fetchAll($sql, [
            'user_role' => $userRole,
            'user_id' => $userId
        ]);
    }

    /**
     * Récupère les détails complets d'un équipement avec son état réel
     */
    public function getDetailsComplets($equipementId, $userRole = null, $userId = null) {
        $sql = "SELECT m.*, t.nom as type_nom,
                (SELECT r.statut FROM reclamation r 
                 WHERE r.materiel_id = m.id 
                 AND r.statut IN ('en_attente', 'en_cours') 
                 ORDER BY r.date_reclamation DESC 
                 LIMIT 1) as etat_reel,
                (SELECT COUNT(*) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as nb_reclamations,
                (SELECT MAX(r.date_reclamation) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as derniere_reclamation
                FROM materiel m 
                LEFT JOIN type t ON m.type_id = t.id 
                WHERE m.id = :id";

        $params = ['id' => $equipementId];

        // Si userRole et userId sont fournis, vérifier que l'équipement appartient à l'utilisateur
        if ($userRole && $userId) {
            $sql .= " AND m.user_role = :user_role AND m.id_user = :user_id";
            $params['user_role'] = $userRole;
            $params['user_id'] = $userId;
        }

        return $this->fetchOne($sql, $params);
    }

    /**
     * Récupère les informations utilisateur pour un équipement
     */
    public function getUserInfoForEquipment($userRole, $userId) {
        $tableMap = [
            'admin' => 'admin',
            'agent' => 'agent',
            'support' => 'support'
        ];

        if (!isset($tableMap[$userRole])) {
            return null;
        }

        $table = $tableMap[$userRole];
        $sql = "SELECT u.nom, u.prenom, u.email, 
                       s.nom as service_nom, 
                       sal.nom as salle_nom, 
                       sal.numero as salle_numero
                FROM $table u 
                LEFT JOIN service s ON u.service_id = s.id 
                LEFT JOIN salle sal ON u.salle_id = sal.id 
                WHERE u.matricul = :user_id";

        return $this->fetchOne($sql, ['user_id' => $userId]);
    }

    /**
     * Vérifie si l'équipement a des réclamations en cours
     */
    public function hasReclamationsEnCours($equipementId) {
        $sql = "SELECT COUNT(*) as count 
                FROM reclamation 
                WHERE materiel_id = :equipement_id 
                AND statut IN ('en_attente', 'en_cours')";

        $result = $this->fetchOne($sql, ['equipement_id' => $equipementId]);
        return $result['count'] > 0;
    }

    /**
     * Récupère la dernière réclamation non résolue
     */
    public function getDerniereReclamationNonResolue($equipementId) {
        $sql = "SELECT r.*, 
                       s.nom as support_nom, 
                       s.prenom as support_prenom
                FROM reclamation r 
                LEFT JOIN support s ON r.support_matricul = s.matricul 
                WHERE r.materiel_id = :equipement_id 
                AND r.statut IN ('en_attente', 'en_cours')
                ORDER BY r.date_reclamation DESC 
                LIMIT 1";

        return $this->fetchOne($sql, ['equipement_id' => $equipementId]);
    }

    /**
     * Récupère l'historique des réclamations pour un équipement
     */
    public function getHistoriqueReclamations($equipementId) {
        $sql = "SELECT r.*, 
                       s.nom as support_nom, 
                       s.prenom as support_prenom 
                FROM reclamation r 
                LEFT JOIN support s ON r.support_matricul = s.matricul 
                WHERE r.materiel_id = :equipement_id 
                ORDER BY r.date_reclamation DESC 
                LIMIT 5";

        return $this->fetchAll($sql, ['equipement_id' => $equipementId]);
    }

    /**
     * Récupère les utilisateurs par rôle
     */
    public function getUsersByRole($role) {
        $tableMap = [
            'admin' => 'admin',
            'agent' => 'agent',
            'support' => 'support'
        ];

        if (!isset($tableMap[$role])) {
            return [];
        }

        $table = $tableMap[$role];
        $sql = "SELECT 
                    u.matricul as id, 
                    u.nom, 
                    u.prenom, 
                    u.email,
                    s.nom as service_nom,
                    sal.nom as salle_nom,
                    sal.numero as salle_numero
                FROM $table u 
                LEFT JOIN service s ON u.service_id = s.id 
                LEFT JOIN salle sal ON u.salle_id = sal.id 
                ORDER BY u.nom, u.prenom";

        return $this->fetchAll($sql);
    }

    /**
     * Vérifie si un numéro de série existe déjà
     */
    public function checkSerialNumber($serialNumber, $excludeId = null) {
        $sql = "SELECT id FROM materiel WHERE serial_number = :serial";
        $params = ['serial' => $serialNumber];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->fetchOne($sql, $params);
        return !empty($result);
    }


/**
 * Crée une notification
 */
public function createNotification($data) {
    // Vérifier que les champs obligatoires sont présents
    if (empty($data['user_id']) || empty($data['user_role'])) {
        error_log("Erreur: user_id ou user_role manquant pour la notification");
        return false;
    }

    $sql = "INSERT INTO notification (id, user_role, user_id, sujet, message, lien, type, type_id, creator_role, creator_id) 
            VALUES (:id, :user_role, :user_id, :sujet, :message, :lien, :type, :type_id, :creator_role, :creator_id)";

    return $this->query($sql, $data);
}

    /**
     * Récupère tous les types d'équipements
     */
    public function getAllTypes() {
        return $this->fetchAll("SELECT * FROM type WHERE nom != 'Pas encore décidé' ORDER BY nom");
    }

    /**
     * Récupère tous les équipements (pour admin)
     */
    public function getTousEquipements() {
        $sql = "SELECT m.*, t.nom as type_nom,
                (SELECT r.statut FROM reclamation r 
                 WHERE r.materiel_id = m.id 
                 AND r.statut IN ('en_attente', 'en_cours') 
                 ORDER BY r.date_reclamation DESC 
                 LIMIT 1) as etat_reel,
                (SELECT COUNT(*) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as nb_reclamations,
                (SELECT MAX(r.date_reclamation) FROM reclamation r 
                 WHERE r.materiel_id = m.id) as derniere_reclamation
                FROM materiel m 
                LEFT JOIN type t ON m.type_id = t.id 
                ORDER BY m.id DESC";

        return $this->fetchAll($sql);
    }

    /**
     * Récupère tous les équipements avec informations complètes pour la gestion
     */
    /**
     * Récupère tous les équipements avec informations complètes pour la gestion
     */
    /**
     * Récupère tous les équipements avec informations complètes pour la gestion
     */
    public function getTousEquipementsComplets() {
        $sql = "SELECT 
            m.*, 
            t.nom as type_nom,
            t.est_personnel,
            -- Informations utilisateur (pour équipements personnels)
            u_admin.nom as admin_nom,
            u_admin.prenom as admin_prenom,
            u_agent.nom as agent_nom, 
            u_agent.prenom as agent_prenom,
            u_support.nom as support_nom,
            u_support.prenom as support_prenom,
            -- Informations service/salle (pour équipements communs)
            s_equip.nom as service_nom_equip,
            sal_equip.nom as salle_nom_equip,
            sal_equip.numero as salle_numero_equip,
            -- Informations service/salle via utilisateur (pour équipements personnels)
            s_user.nom as service_nom_user,
            sal_user.nom as salle_nom_user,
            sal_user.numero as salle_numero_user,
            -- Vérification simple du statut
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM reclamation r 
                    WHERE r.materiel_id = m.id 
                    AND r.statut IN ('en_attente', 'en_cours')
                ) THEN 'en_panne'
                ELSE 'fonctionnel'
            END as statut_simple
        FROM materiel m 
        LEFT JOIN type t ON m.type_id = t.id 
        -- Jointures pour les utilisateurs (équipements personnels)
        LEFT JOIN admin u_admin ON (m.user_role = 'admin' AND m.id_user = u_admin.matricul)
        LEFT JOIN agent u_agent ON (m.user_role = 'agent' AND m.id_user = u_agent.matricul)
        LEFT JOIN support u_support ON (m.user_role = 'support' AND m.id_user = u_support.matricul)
        -- Jointures pour le service/salle via utilisateur
        LEFT JOIN service s_user ON (
            (m.user_role = 'admin' AND u_admin.service_id = s_user.id) OR
            (m.user_role = 'agent' AND u_agent.service_id = s_user.id) OR
            (m.user_role = 'support' AND u_support.service_id = s_user.id)
        )
        LEFT JOIN salle sal_user ON (
            (m.user_role = 'admin' AND u_admin.salle_id = sal_user.id) OR
            (m.user_role = 'agent' AND u_agent.salle_id = sal_user.id) OR
            (m.user_role = 'support' AND u_support.salle_id = sal_user.id)
        )
        -- Jointures pour le service/salle direct (équipements communs)
        LEFT JOIN service s_equip ON m.service_id = s_equip.id
        LEFT JOIN salle sal_equip ON m.salle_id = sal_equip.id
        ORDER BY m.id DESC";

        $equipements = $this->fetchAll($sql);

        // Formater les données pour avoir un format cohérent
        return array_map(function($equip) {
            // Déterminer le nom complet de l'utilisateur et les informations de localisation
            $user_nom = '';
            $user_prenom = '';
            $service_nom = '';
            $salle_nom = '';
            $salle_numero = '';
            $affectation_type = '';

            if ($equip['est_personnel'] && $equip['user_role'] && $equip['id_user']) {
                // Équipement personnel - assigné à un utilisateur
                $affectation_type = 'personnel';

                if ($equip['user_role'] === 'admin' && $equip['admin_nom']) {
                    $user_nom = $equip['admin_nom'];
                    $user_prenom = $equip['admin_prenom'];
                    $service_nom = $equip['service_nom_user'];
                    $salle_nom = $equip['salle_nom_user'];
                    $salle_numero = $equip['salle_numero_user'];
                } elseif ($equip['user_role'] === 'agent' && $equip['agent_nom']) {
                    $user_nom = $equip['agent_nom'];
                    $user_prenom = $equip['agent_prenom'];
                    $service_nom = $equip['service_nom_user'];
                    $salle_nom = $equip['salle_nom_user'];
                    $salle_numero = $equip['salle_numero_user'];
                } elseif ($equip['user_role'] === 'support' && $equip['support_nom']) {
                    $user_nom = $equip['support_nom'];
                    $user_prenom = $equip['support_prenom'];
                    $service_nom = $equip['service_nom_user'];
                    $salle_nom = $equip['salle_nom_user'];
                    $salle_numero = $equip['salle_numero_user'];
                }
            } else {
                // Équipement commun - assigné à une salle/service
                $affectation_type = 'commun';
                $user_nom = 'Commun';
                $user_prenom = '';
                $service_nom = $equip['service_nom_equip'];
                $salle_nom = $equip['salle_nom_equip'];
                $salle_numero = $equip['salle_numero_equip'];
            }

            // Déterminer le statut (simple)
            $status = $equip['statut_simple']; // 'fonctionnel' ou 'en_panne'
            $status_text = $status === 'en_panne' ? 'En panne' : 'Fonctionnel';
            $status_class = $status === 'en_panne' ? 'status-danger' : 'status-good';

            $equip['user_nom'] = $user_nom;
            $equip['user_prenom'] = $user_prenom;
            $equip['user_role'] = $affectation_type === 'personnel' ? $equip['user_role'] : 'Commun';
            $equip['service_nom'] = $service_nom ?: 'Non assigné';
            $equip['salle_nom'] = $salle_nom ?: 'Non assignée';
            $equip['salle_numero'] = $salle_numero ?: '';
            $equip['status'] = $status;
            $equip['status_text'] = $status_text;
            $equip['status_class'] = $status_class;
            $equip['affectation_type'] = $affectation_type;
            $equip['date_debut'] = $equip['date_enregistrement']; // Alias pour la vue

            return $equip;
        }, $equipements);
    }

    /**
     * Récupère l'historique d'un équipement
     */
    public function getHistoriqueEquipement($equipementId) {
        $historique = [];

        // Récupérer la date d'enregistrement
        $equipement = $this->getById($equipementId);
        if ($equipement && $equipement['date_enregistrement']) {
            $historique[] = [
                'date' => $equipement['date_enregistrement'],
                'action' => 'Ajout',
                'description' => 'Équipement ajouté au parc informatique',
                'utilisateur' => 'Système'
            ];
        }

        // Récupérer les réclamations
        $sql = "SELECT r.*, 
                       s.nom as support_nom, 
                       s.prenom as support_prenom
                FROM reclamation r 
                LEFT JOIN support s ON r.support_matricul = s.matricul 
                WHERE r.materiel_id = :equipement_id 
                ORDER BY r.date_reclamation DESC";

        $reclamations = $this->fetchAll($sql, ['equipement_id' => $equipementId]);

        foreach ($reclamations as $reclamation) {
            $support = $reclamation['support_nom'] ?
                "{$reclamation['support_nom']} {$reclamation['support_prenom']}" : "Non assigné";

            $historique[] = [
                'date' => $reclamation['date_reclamation'],
                'action' => 'Réclamation',
                'description' => $reclamation['motif'],
                'utilisateur' => $support,
                'statut' => $reclamation['statut']
            ];

            if ($reclamation['date_resolution']) {
                $historique[] = [
                    'date' => $reclamation['date_resolution'],
                    'action' => 'Résolution',
                    'description' => $reclamation['motif_support'] ?: 'Réclamation résolue',
                    'utilisateur' => $support,
                    'statut' => 'résolu'
                ];
            }
        }

        // Trier par date
        usort($historique, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $historique;
    }

    /**
     * Récupère les statistiques des équipements
     */
    public function getStatistiques() {
        $sql = "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN user_role IS NOT NULL AND id_user IS NOT NULL THEN 1 END) as assignes,
                COUNT(CASE WHEN user_role IS NULL OR id_user IS NULL THEN 1 END) as disponibles,
                COUNT(CASE WHEN garantie_fin < CURDATE() THEN 1 END) as garantie_expiree,
                COUNT(CASE WHEN service_fin < CURDATE() THEN 1 END) as service_expire
                FROM materiel";

        return $this->fetchOne($sql);
    }

    /**
     * Récupère les équipements par type
     */
    public function getEquipementsParType() {
        $sql = "SELECT t.nom, COUNT(m.id) as count
                FROM type t
                LEFT JOIN materiel m ON t.id = m.type_id
                GROUP BY t.id, t.nom
                ORDER BY count DESC";

        return $this->fetchAll($sql);
    }

    /**
 * Met à jour un équipement
 */
public function update($id, $data) {
    $setParts = [];
    $params = ['id' => $id];

    foreach ($data as $key => $value) {
        $setParts[] = "$key = :$key";
        $params[$key] = $value;
    }

    $sql = "UPDATE materiel SET " . implode(', ', $setParts) . " WHERE id = :id";
    return $this->query($sql, $params);
}

/**
 * Supprime un équipement
 */
public function deleteMateriel($id) {
    $sql = "DELETE FROM materiel WHERE id = :id";
    return $this->query($sql, ['id' => $id]);
}

/**
 * Récupère un équipement par son ID
 */
public function getById($id) {
    return $this->fetchOne("SELECT * FROM materiel WHERE id = :id", ['id' => $id]);
}
}
?>