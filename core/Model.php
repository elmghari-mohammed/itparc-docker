<?php
// /core/Model.php

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../app/models/Database.php');

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct($table = null) {
        $this->db = Database::getInstance();
        
        // Définition de la table
        if ($table !== null) {
            $this->table = $table;
        }
        // Pas de déduction automatique pour éviter les problèmes
    }
    
    /**
     * Vérifie si la table est définie avant d'exécuter les opérations CRUD
     */
    private function checkTable() {
        if (empty($this->table)) {
            throw new Exception("Table name is not defined for " . get_class($this));
        }
    }

    /**
     * Vérifie si une valeur existe dans une colonne spécifique
     * Utile pour les validations d'unicité
     */
    public function isExists($table, $column, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
        $params = [$value];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $count = $this->fetchColumn($sql, $params);
        return $count > 0;
    }
    
    /**
     * Récupère tous les enregistrements
     */
    public function getAll() {
        $this->checkTable();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un enregistrement par son ID
     */
    public function getById($id) {
        $this->checkTable();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouvel enregistrement
     */
    public function create($data) {
        $this->checkTable();
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Met à jour un enregistrement
     */
    public function update($id, $data) {
        $this->checkTable();
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    
    /**
     * Supprime un enregistrement
     */
    public function delete($table, $id) {
        $primaryKey = 'id';
        $sql = "DELETE FROM $table WHERE $primaryKey = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
}
    
    
    /**
     * Compte le nombre total d'enregistrements
     */
    public function count() {
        $this->checkTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Recherche avec des conditions personnalisées
     */
    public function where($conditions, $params = []) {
        $this->checkTable();
        $whereClause = [];
        foreach ($conditions as $column => $value) {
            $whereClause[] = "$column = :$column";
            $params[$column] = $value;
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Exécute une requête SQL personnalisée
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Récupère le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Définit le nom de la table
     */
    protected function setTable($tableName) {
        $this->table = $tableName;
    }
    
    /**
     * Définit la clé primaire
     */
    protected function setPrimaryKey($primaryKey) {
        $this->primaryKey = $primaryKey;
    }
    
    /**
     * Préparer et exécuter une requête (méthode utilitaire)
     */
    protected function prepareAndExecute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Récupère une seule valeur (pour les COUNT() etc.)
     */
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->prepareAndExecute($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Récupère une seule ligne
     */
    protected function fetchOne($sql, $params = []) {
        $stmt = $this->prepareAndExecute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère plusieurs lignes
     */
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->prepareAndExecute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>