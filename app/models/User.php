<?php
// /app/models/User.php

require_once(__DIR__ . '/../../core/Model.php');

class User extends Model {
    public function __construct() {
        parent::__construct('users'); // Table par défaut
    }

    /**
     * Vérifie si un utilisateur existe dans une table spécifique
     */
    public function userExists($role, $userId) {
        $tableMap = [
            'admin' => 'admin',
            'agent' => 'agent',
            'support' => 'support'
        ];
        
        if (!isset($tableMap[$role])) {
            return false;
        }
        
        $table = $tableMap[$role];
        $sql = "SELECT COUNT(*) as count FROM $table WHERE matricul = :user_id";
        $result = $this->fetchOne($sql, ['user_id' => $userId]);
        
        return $result['count'] > 0;
    }

    /**
     * Récupère les détails d'un utilisateur
     */
    public function getUserDetails($role, $userId) {
        $tableMap = [
            'admin' => 'admin',
            'agent' => 'agent',
            'support' => 'support'
        ];
        
        if (!isset($tableMap[$role])) {
            return null;
        }
        
        $table = $tableMap[$role];
        $sql = "SELECT u.*, s.nom as service_nom, sal.nom as salle_nom, sal.numero as salle_numero
                FROM $table u 
                LEFT JOIN service s ON u.service_id = s.id 
                LEFT JOIN salle sal ON u.salle_id = sal.id 
                WHERE u.matricul = :user_id";
        
        return $this->fetchOne($sql, ['user_id' => $userId]);
    }

    /**
     * Récupère tous les utilisateurs d'un rôle spécifique
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
}
?>