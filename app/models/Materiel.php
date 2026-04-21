<?php
// /app/models/Materiel.php

require_once(__DIR__ . '/../../core/Model.php');

class Materiel extends Model {
    public function __construct() {
        parent::__construct('materiel');
        $this->setPrimaryKey('id');
    }

    /**
     * Récupère les matériels assignés à un utilisateur - OPTIMIZED VERSION
     */
    public function getByUser($userRole, $userId) {
        try {
            set_time_limit(15); // 15 seconds max for this query
            
            // Optimized query with proper indexing
            $sql = "SELECT id, CONCAT(marque, ' ', model, ' (', serial_number, ')') as nom_complet 
                    FROM materiel 
                    WHERE user_role = :user_role AND id_user = :user_id 
                    ORDER BY marque, model
                    LIMIT 50"; // Added limit to prevent large result sets
            
            return $this->fetchAll($sql, [
                'user_role' => $userRole,
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            error_log("Error fetching user materials: " . $e->getMessage());
            return [];
        }
    }
}
?>