<?php
// /app/models/Utilisateur.php

require_once(__DIR__ . '/../../core/Model.php');

class Utilisateur extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Récupère tous les utilisateurs avec leurs informations
     */
    public function getAllUsers() {
        $sql = "SELECT 
                    a.matricul as id, 
                    a.nom, 
                    a.prenom, 
                    a.email, 
                    'Agent' as type,
                    s.nom as service,
                    s.id as service_id,
                    a.bloque as is_blocked,
                    a.date_de_debut as created,
                    IF(a.bloque = 0, 'Actif', 'Suspendu') as status
                FROM agent a
                LEFT JOIN service s ON a.service_id = s.id
                UNION ALL
                SELECT 
                    sp.matricul as id, 
                    sp.nom, 
                    sp.prenom, 
                    sp.email, 
                    'Support' as type,
                    s.nom as service,
                    s.id as service_id,
                    sp.bloque as is_blocked,
                    sp.date_de_debut as created,
                    IF(sp.bloque = 0, 'Actif', 'Suspendu') as status
                FROM support sp
                LEFT JOIN service s ON sp.service_id = s.id
                UNION ALL
                SELECT 
                    ad.matricul as id, 
                    ad.nom, 
                    ad.prenom, 
                    ad.email, 
                    'Administrateur' as type,
                    s.nom as service,
                    s.id as service_id,
                    ad.bloque as is_blocked,
                    ad.date_de_debut as created,
                    IF(ad.bloque = 0, 'Actif', 'Suspendu') as status
                FROM admin ad
                LEFT JOIN service s ON ad.service_id = s.id
                ORDER BY created DESC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Met à jour un utilisateur
     */

public function updateUser($id, $data) {
    $table = $this->getTableByRole($data['role']);
    
    $updateData = [
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'email' => $data['email'], // Ajout de l'email
        'service_id' => $data['service_id']
    ];
    
    // Si un mot de passe est fourni, l'ajouter aux données à mettre à jour
    if (!empty($data['password'])) {
        $updateData['password'] = hash('sha256', $data['password']);
    }
    
    // Construire la requête UPDATE manuellement
    $setClause = [];
    $params = ['id' => $id];
    
    foreach ($updateData as $field => $value) {
        $setClause[] = "$field = :$field";
        $params[$field] = $value;
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE matricul = :id";
    
    try {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erreur updateUser: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Bascule le statut de blocage d'un utilisateur
     */
    public function toggleBlockStatus($id, $role) {
        $table = $this->getTableByRole($role);
        
        try {
            // Récupérer le statut actuel
            $stmt = $this->db->prepare("SELECT bloque FROM $table WHERE matricul = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false;
            }
            
            $currentStatus = (int)$result['bloque'];
            $newStatus = $currentStatus ? 0 : 1;
            
            // Mettre à jour le statut
            $stmt = $this->db->prepare("UPDATE $table SET bloque = :status WHERE matricul = :id");
            return $stmt->execute(['status' => $newStatus, 'id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Erreur toggleBlockStatus: " . $e->getMessage());
            return false;
        }
    }

    public function getUserStatus($id, $role) {
        $table = $this->getTableByRole($role);
        
        try {
            $stmt = $this->db->prepare("SELECT bloque, nom, prenom FROM $table WHERE matricul = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserStatus: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un utilisateur en libérant les contraintes (version simple)
     */
    public function deleteUser($id, $role) {
        $table = $this->getTableByRole($role);
        
        try {
            // Désactiver les contraintes FK
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Supprimer l'utilisateur
            $stmt = $this->db->prepare("DELETE FROM $table WHERE matricul = :id");
            $result = $stmt->execute(['id' => $id]);
            
            // Réactiver les contraintes FK
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            return $result;
            
        } catch (Exception $e) {
            // Réactiver les contraintes FK en cas d'erreur
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
            error_log("Erreur deleteUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les services disponibles
     */
    public function getServices() {
        return $this->fetchAll("SELECT * FROM service ORDER BY nom");
    }

    /**
     * Obtient le nom de la table selon le rôle
     */
    private function getTableByRole($role) {
        $normalizedRole = strtolower(trim($role));
        
        $roleToTable = [
            'agent' => 'agent',
            'support' => 'support',
            'administrateur' => 'admin',
            'admin' => 'admin',
        ];

        $table = $roleToTable[$normalizedRole] ?? 'agent';
        error_log("Table déterminée pour rôle '$role' (normalisé: '$normalizedRole'): $table");
        
        return $table;
    }
    
    /**
     * Vérifie si un utilisateur existe
     */
    public function userExists($id, $role) {
        $table = $this->getTableByRole($role);
        
        error_log("Vérification existence - ID: $id, Rôle: $role, Table: $table");
        
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM $table WHERE matricul = :id");
            $stmt->execute(['id' => $id]);
            $count = $stmt->fetchColumn();
            
            error_log("Résultat query: $count lignes trouvées");
            
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Erreur userExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un nouvel utilisateur selon son type
     */
    public function createUser($type, $data) {
        $allowedTypes = ['agent', 'support']; 
        
        if (!in_array($type, $allowedTypes)) {
            throw new Exception("Type d'utilisateur non valide");
        }
        
        // Définir la table à utiliser
        $this->setTable($type);
        
        // Préparer les données spécifiques selon le type
        $userData = [
            'matricul' => $this->generateMatricule($type),
            'nom' => $data['nom'],
            'prenom' => $data['prenom'] ?? '',
            'email' => $data['email'],
            'password' => hash('sha256', $data['password']),
            'service_id' => $data['service_id'],
            'salle_id' => !empty($data['salle_id']) ? $data['salle_id'] : null,
            'date_de_debut' => date('Y-m-d'),
            'N_T' => 0,
            'nombre_tentative' => 0,
            'bloque' => 0
        ];
        
        // Ajouter les champs spécifiques selon le type
        switch ($type) {
            case 'agent':
                $userData['tache'] = $data['tache'] ?? 'À définir';
                $userData['post'] = $data['post'] ?? 'Agent';
                break;
            case 'support':
                $userData['numero'] = $data['numero'] ?? '';
                break;
        }
        
        // Utiliser la méthode create() de la classe Model
        return $this->create($userData);
    }
    
    /**
     * Génère un matricule unique
     */
    private function generateMatricule($role) {
        // Définir le préfixe selon le rôle
        $prefixes = [
            'agent' => 'AGT',
            'support' => 'SUP'
        ];
        
        $prefix = $prefixes[$role] ?? 'AGT'; // Par défaut AGT
        
        // Déterminer la table à utiliser
        $table = $this->getTableByRole($role);
        
        // Cherche le plus grand numéro existant dans les IDs commençant par le préfixe
        $sql = "SELECT MAX(CAST(SUBSTRING(matricul, 4) AS UNSIGNED)) AS max_num 
                FROM $table 
                WHERE matricul LIKE '$prefix%'";
        $row = $this->fetchOne($sql);

        // Calcul du prochain numéro
        $num = ($row && $row['max_num']) ? (intval($row['max_num']) + 1) : 1;

        // Si le nombre de chiffres est dépassé, on incrémente le nombre de chiffres
        $digits = 4; // Nombre de chiffres par défaut
        $maxAllowed = pow(10, $digits) - 1;
        
        if ($num > $maxAllowed) {
            $digits++; // Augmente le nombre de chiffres
        }

        // Formate le matricule avec le nombre de chiffres voulu
        $id_number = str_pad($num, $digits, '0', STR_PAD_LEFT);
        $newMatricule = $prefix . $id_number;
        
        // Vérifier que le matricule n'existe pas déjà (sécurité)
        while ($this->matriculeExists($newMatricule, $table)) {
            $num++;
            $id_number = str_pad($num, $digits, '0', STR_PAD_LEFT);
            $newMatricule = $prefix . $id_number;
        }
        
        return $newMatricule;
    }

    /**
     * Vérifie si un matricule existe déjà dans une table
     */
    private function matriculeExists($matricule, $table) {
        $result = $this->fetchOne("SELECT matricul FROM $table WHERE matricul = :matricul", ['matricul' => $matricule]);
        return !empty($result);
    }
    
    /**
     * Récupère tous les services disponibles
     */
    public function getAllServices() {
        return $this->fetchAll("SELECT * FROM service ORDER BY nom");
    }
    
    /**
     * Récupère les salles disponibles (avec capacité non atteinte)
     */
    public function getAvailableSalles() {
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM agent WHERE salle_id = s.id) + 
                       (SELECT COUNT(*) FROM support WHERE salle_id = s.id) + 
                       (SELECT COUNT(*) FROM admin WHERE salle_id = s.id) as occupants
                FROM salle s
                HAVING occupants < s.capacite OR s.capacite = 0
                ORDER BY s.nom";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM (
                    SELECT email FROM agent 
                    UNION SELECT email FROM support 
                    UNION SELECT email FROM admin
                ) as all_emails 
                WHERE email = ?";
        
        $count = $this->fetchColumn($sql, [$email]);
        return $count > 0;
    }

    /**
 * Récupère les informations complètes d'un utilisateur
 */
public function getUserInfo($id, $role) {
    $table = $this->getTableByRole($role);
    
    try {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE matricul = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getUserInfo: " . $e->getMessage());
        return false;
    }
}
}
?>