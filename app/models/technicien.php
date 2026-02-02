<?php
// /app/models/Technicien.php

require_once(__DIR__ . '/../../core/Model.php');

class Technicien extends Model {
    public function __construct() {
        parent::__construct('technicien');
        $this->setPrimaryKey('matricul');
    }

    /**
     * Récupère tous les techniciens actifs
     */
    public function getAll() {
        $sql = "SELECT matricul, nom, prenom 
                FROM technicien 
                WHERE bloque = FALSE 
                ORDER BY nom, prenom";
        
        return $this->fetchAll($sql);
    }
}
?>