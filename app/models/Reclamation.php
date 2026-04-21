<?php
// /app/models/Reclamation.php

require_once(__DIR__ . '/../../core/Model.php');

class Reclamation extends Model {
    public function __construct() {
        parent::__construct('reclamation');
        $this->setPrimaryKey('id');
    }

    public function countReclamationsLibres() {
        $sql = "SELECT COUNT(*) FROM reclamation 
                WHERE support_matricul IS NULL 
                AND statut IN ('en_attente', 'en_cours')";
        
        return $this->fetchColumn($sql);
    }

    /**
     * Génère le prochain ID de réclamation
     */
    public function generateNextId($digits = 4) {
        // Cherche le plus grand numéro existant dans les IDs commençant par REC
        $sql = "SELECT MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)) AS max_num FROM reclamation WHERE id LIKE 'REC%'";
        $row = $this->fetchOne($sql);

        // Calcul du prochain numéro
        $num = ($row && $row['max_num']) ? (intval($row['max_num']) + 1) : 1;

        // Si le nombre de chiffres est dépassé, on incrémente le nombre de chiffres
        $maxAllowed = pow(10, $digits) - 1;
        if ($num > $maxAllowed) {
            $digits++; // Augmente le nombre de chiffres
        }

        // Formate l'ID avec le nombre de chiffres voulu
        $id_nomber = str_pad($num, $digits, '0', STR_PAD_LEFT);
        return 'REC' . $id_nomber;
    }

    /**
     * Vérifie si un ID existe déjà
     */
    public function exists($id) {
        $result = $this->fetchOne("SELECT id FROM reclamation WHERE id = :id", ['id' => $id]);
        return !empty($result);
    }

    /**
     * Crée une nouvelle réclamation
     */
    public function createReclamation($data) {
        // Générer un ID unique
        $newId = $this->generateNextId();
        while ($this->exists($newId)) {
            $newId = $this->generateNextId();
        }
        
        // CORRECTION: Ajouter l'ID généré aux données
        $data['id'] = $newId;
        
        // CORRECTION: Convertir la date au format correct pour MySQL
        if (isset($data['date_reclamation']) && is_string($data['date_reclamation'])) {
            $data['date_reclamation'] = date('Y-m-d H:i:s', strtotime($data['date_reclamation']));
        }
        
        // CORRECTION: S'assurer que les valeurs NULL sont correctement gérées
        $nullableFields = ['date_resolution', 'support_matricul', 'motif_support', 'type'];
        foreach ($nullableFields as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }
        
        return parent::create($data);
    }

    /**
     * Récupère les réclamations de l'utilisateur
     */
    public function getMesReclamations($userId) {
        $sql = "SELECT r.*, m.marque, m.model, CONCAT(m.marque, ' ', m.model) as materiel_info 
                FROM reclamation r 
                LEFT JOIN materiel m ON r.materiel_id = m.id 
                WHERE r.id_user = :user_id 
                ORDER BY r.date_reclamation DESC";
        
        return $this->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Récupère les détails d'une réclamation
     */
    public function getDetails($reclamationId, $userId = null) {
        $sql = "SELECT r.*, m.marque, m.model, CONCAT(m.marque, ' ', m.model) as materiel_info 
                FROM reclamation r 
                LEFT JOIN materiel m ON r.materiel_id = m.id 
                WHERE r.id = :id";
        
        $params = ['id' => $reclamationId];
        
        if ($userId) {
            $sql .= " AND r.id_user = :user_id";
            $params['user_id'] = $userId;
        }
        
        return $this->fetchOne($sql, $params);
    }

    /**
     * Récupère les réclamations en attente d'assignation (pour les admins)
     */
    public function getReclamationsLibre() {
        $sql = "SELECT r.*, m.serial_number, m.model, m.marque, 
                       CONCAT(u.nom, ' ', u.prenom) as demandeur_nom
                FROM reclamation r
                LEFT JOIN materiel m ON r.materiel_id = m.id
                LEFT JOIN agent u ON r.id_user = u.matricul AND r.user_role = 'agent'
                WHERE r.support_matricul IS NULL AND r.statut = 'en_attente' Or r.statut = 'en_cours'
                ORDER BY r.date_reclamation DESC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Récupère les réclamations assignées à un support
     */
    public function getReclamationsSupport($supportId) {
        $sql = "SELECT r.*, m.serial_number, m.model, m.marque, 
                    t.nom as type_equipement,
                    CONCAT(u.nom, ' ', u.prenom) as demandeur_nom,
                    s.nom as salle_nom,
                    CASE 
                        WHEN r.support_matricul IS NULL THEN 'libre'
                        ELSE r.statut 
                    END as statut_affichage
                FROM reclamation r
                LEFT JOIN materiel m ON r.materiel_id = m.id
                LEFT JOIN type t ON m.type_id = t.id
                LEFT JOIN agent u ON r.id_user = u.matricul AND r.user_role = 'agent'
                LEFT JOIN admin a ON r.id_user = a.matricul AND r.user_role = 'admin'
                LEFT JOIN support sp ON r.id_user = sp.matricul AND r.user_role = 'support'
                LEFT JOIN salle s ON COALESCE(u.salle_id, a.salle_id, sp.salle_id) = s.id
                WHERE (r.support_matricul = :support_id) 
                OR (r.support_matricul IS NULL AND (r.statut = 'en_attente' OR r.statut = 'en_cours'))
                ORDER BY 
                    CASE WHEN r.support_matricul IS NULL THEN 0 ELSE 1 END,
                    r.date_reclamation DESC";
        
        return $this->fetchAll($sql, ['support_id' => $supportId]);
    }

    /**
     * Prendre une réclamation libre (assigner au support)
     */
    public function prendreReclamation($reclamationId, $supportId) {
        $sql = "UPDATE reclamation 
                SET support_matricul = :support_id, 
                    statut = 'en_cours',
                    motif_support = 'Pris en charge par le support'
                WHERE id = :reclamation_id 
                AND support_matricul IS NULL 
                AND (statut = 'en_attente' OR statut = 'en_cours')";
        
        return $this->query($sql, [
            'support_id' => $supportId,
            'reclamation_id' => $reclamationId
        ]);
    }
    
    /**
     * Récupère tous les supports disponibles
     */
    public function getAllSupports() {
        $sql = "SELECT matricul, nom, prenom, 
                       (SELECT COUNT(*) FROM reclamation WHERE support_matricul = support.matricul AND statut = 'en_cours') as nb_assignations
                FROM support 
                ORDER BY nom, prenom";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Assigner une réclamation à un support
     */
    public function assignerReclamation($reclamationId, $supportId, $assignateurId) {
        $sql = "UPDATE reclamation 
                SET support_matricul = ?, motif_support = 'Assigné par le système', statut = 'en_cours'
                WHERE id = ? AND statut = 'en_attente'";
        
        return $this->query($sql, [$supportId, $reclamationId]);
    }
    
    /**
     * Récupère les détails d'une réclamation
     */
    public function getReclamationDetails($id) {
        $sql = "SELECT r.*, m.serial_number, m.model, m.marque,
                       CONCAT(u.nom, ' ', u.prenom) as demandeur_nom,
                       s.nom as support_nom, s.prenom as support_prenom
                FROM reclamation r
                LEFT JOIN materiel m ON r.materiel_id = m.id
                LEFT JOIN agent u ON r.id_user = u.matricul AND r.user_role = 'agent'
                LEFT JOIN support s ON r.support_matricul = s.matricul
                WHERE r.id = ?";
        
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Met à jour le statut d'une réclamation avec type
     */
    public function updateStatut($reclamationId, $statut, $motifSupport = null, $supportId = null, $type = null) {
        $data = [
            'statut' => $statut,
            'date_resolution' => ($statut === 'resolu' || $statut === 'ferme') ? date('Y-m-d H:i:s') : null
        ];
        
        if ($motifSupport !== null) {
            $data['motif_support'] = $motifSupport;
        }
        
        if ($type !== null) {
            $data['type'] = $type;
        }
        
        // Si le support prend en charge la réclamation, l'assigner
        if ($supportId && $statut === 'en_cours') {
            $data['support_matricul'] = $supportId;
        }
        
        return $this->update($reclamationId, $data);
    }

    /**
     * Récupère les matériels de n'importe quel type d'utilisateur
     */
    public function getMaterielsUtilisateur($userId, $userRole) {
        $sql = "SELECT m.id, m.marque, m.model, CONCAT(m.marque, ' ', m.model) as nom_complet
                FROM materiel m
                WHERE m.id_user = :user_id AND m.user_role = :user_role
                ORDER BY m.marque";
        
        return $this->fetchAll($sql, [
            'user_id' => $userId,
            'user_role' => $userRole
        ]);
    }

    /**
     * Récupère les détails complets d'une réclamation pour le traitement
     */
    public function getReclamationPourTraitement($reclamationId, $supportId = null) {
        $sql = "SELECT r.*, m.serial_number, m.model, m.marque, m.detail,
                       CONCAT(u.nom, ' ', u.prenom) as demandeur_nom,
                       u.email as demandeur_email,
                       sal.nom as salle_nom, sal.numero as salle_numero,
                       serv.nom as service_nom
                FROM reclamation r
                LEFT JOIN materiel m ON r.materiel_id = m.id
                LEFT JOIN agent u ON r.id_user = u.matricul AND r.user_role = 'agent'
                LEFT JOIN salle sal ON u.salle_id = sal.id
                LEFT JOIN service serv ON u.service_id = serv.id
                WHERE r.id = :reclamation_id";
        
        $params = ['reclamation_id' => $reclamationId];
        
        if ($supportId) {
            $sql .= " AND (r.support_matricul = :support_id OR r.support_matricul IS NULL)";
            $params['support_id'] = $supportId;
        }
        
        return $this->fetchOne($sql, $params);
    }

    public function getStatuts() {
        return [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'resolu' => 'Résolu',
            'ferme' => 'Fermé'
        ];
    }

    // Convertir un statut
    public function getLibelleStatut($statut) {
        $statuts = $this->getStatuts();
        return $statuts[$statut] ?? $statut;
    }

    /**
     * Récupère les types de réclamation
     */
    public function getTypesReclamation() {
        return [
            'hardware' => 'Hardware',
            'software' => 'Software'
        ];
    }

    /** super */
    // Ajouter ces méthodes dans la classe Reclamation (après les méthodes existantes)

/**
 * Récupère toutes les réclamations pour la supervision admin
 */
// Dans Reclamation.php - Ajoutez cette méthode

/**
 * Récupère toutes les réclamations pour la supervision admin avec le nom complet du support
 */
public function getReclamationsPourSupervision() {
    $sql = "SELECT r.*, 
                   m.marque, m.model, m.serial_number,
                   t.nom as type_equipement,
                   CONCAT(u.nom, ' ', u.prenom) as demandeur_nom,
                   u.email as demandeur_email,
                   s.nom as salle_nom, s.numero as salle_numero,
                   serv.nom as service_nom,
                   sup.nom as support_nom, 
                   sup.prenom as support_prenom,
                   CONCAT(sup.prenom, ' ', sup.nom) as support_assignee,  -- Ajout du nom complet
                   CASE 
                       WHEN r.support_matricul IS NULL THEN 'non_assignee'
                       ELSE r.support_matricul 
                   END as support_filter
            FROM reclamation r
            LEFT JOIN materiel m ON r.materiel_id = m.id
            LEFT JOIN type t ON m.type_id = t.id
            LEFT JOIN agent u ON r.id_user = u.matricul AND r.user_role = 'agent'
            LEFT JOIN admin a ON r.id_user = a.matricul AND r.user_role = 'admin'
            LEFT JOIN support sp ON r.id_user = sp.matricul AND r.user_role = 'support'
            LEFT JOIN salle s ON COALESCE(u.salle_id, a.salle_id, sp.salle_id) = s.id
            LEFT JOIN service serv ON COALESCE(u.service_id, a.service_id, sp.service_id) = serv.id
            LEFT JOIN support sup ON r.support_matricul = sup.matricul
            ORDER BY r.date_reclamation DESC";
    
    return $this->fetchAll($sql);
}

/**
 * Met à jour une réclamation avec les données de supervision admin
 */
public function updateReclamationSupervision($reclamationId, $data) {
    // Si le statut change à "en_attente" ou "en_cours", mettre date_resolution à null
    if (isset($data['statut']) && in_array($data['statut'], ['en_attente', 'en_cours'])) {
        $data['date_resolution'] = null;
    }
    
    // Si le statut change à "resolu" ou "ferme" et date_resolution n'est pas définie
    if (isset($data['statut']) && in_array($data['statut'], ['resolu', 'ferme']) && empty($data['date_resolution'])) {
        $data['date_resolution'] = date('Y-m-d H:i:s');
    }
    
    return $this->update($reclamationId, $data);
}

/**
 * Récupère tous les supports disponibles pour l'assignation
 */
public function getSupportsPourAssignation() {
    $sql = "SELECT matricul, nom, prenom, email 
            FROM support 
            ORDER BY nom, prenom";
    
    return $this->fetchAll($sql);
}
// Dans Reclamation.php
public function getSupportFullName($supportMatricul, $supportNom, $supportPrenom) {
    if (empty($supportMatricul)) {
        return 'Non assignée';
    }
    
    $fullName = trim(($supportPrenom ?? '') . ' ' . ($supportNom ?? ''));
    return !empty($fullName) ? $fullName : $supportMatricul;
}


}
?>