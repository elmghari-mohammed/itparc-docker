<?php
// /app/models/Profil.php

require_once(__DIR__ . '/../../core/Model.php');

class Profil extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Met à jour les informations personnelles de l'utilisateur
     */
    public function updateInformations($role, $id, $data) {
        $setClause = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $setClause = implode(', ', $setClause);
        $sql = "UPDATE $role SET $setClause WHERE matricul = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Valide le format du numéro de téléphone marocain
     */
    public function validatePhoneNumber($phone) {
        // Expression régulière pour valider les formats marocains
        $regex = '/^(0[5-7][0-9]{8}|(\+212)[5-7][0-9]{8})$/';
        return preg_match($regex, str_replace(' ', '', $phone));
    }
    
    /**
     * Vérifie si le mot de passe actuel est correct
     */
    public function verifyCurrentPassword($role, $id, $password) {
        $hashed = hash('sha256', $password);
        $sql = "SELECT password FROM $role WHERE matricul = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['password'] === $hashed;
    }
    
    /**
     * Met à jour le mot de passe
     */
    public function updatePassword($role, $id, $newPassword) {
        $hashed = hash('sha256', $newPassword);
        $sql = "UPDATE $role SET password = :password WHERE matricul = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['password' => $hashed, 'id' => $id]);
    }
    
    /**
     * Valide la complexité du mot de passe
     */
    public function validatePasswordStrength($password) {
        // Au moins 8 caractères, une majuscule, une minuscule et un chiffre
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }
}
?>