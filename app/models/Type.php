<?php
require_once(__DIR__ . '/../../core/Model.php');

class Type extends Model {
    public function __construct() {
        parent::__construct('type');
        $this->setPrimaryKey('id');
    }

    public function getAll() {
        return $this->fetchAll("SELECT * FROM type ORDER BY nom");
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM type WHERE id = ?", [$id]);
    }

    public function add($nom, $description, $est_personnel = false) {
        if ($this->isExists('type', 'nom', $nom)) {
            throw new Exception("Un type avec ce nom existe déjà.");
        }
        
        $sql = "INSERT INTO type (nom, description, est_personnel) VALUES (?, ?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $description, $est_personnel]);
    }

    public function updateType($id, $nom, $description, $est_personnel = false) {
        if ($this->isExists('type', 'nom', $nom, $id)) {
            throw new Exception("Un autre type avec ce nom existe déjà.");
        }
        
        $sql = "UPDATE type SET nom = ?, description = ?, est_personnel = ? WHERE id = ?";
        return $this->prepareAndExecute($sql, [$nom, $description, $est_personnel, $id]);
    }

    public function deleteType($id) {
        $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        
        if (!$defaultType) {
            $this->add('Pas encore décidé', 'Type par défaut pour les équipements non classés', false);
            $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        }
        
        $sql = "UPDATE materiel SET type_id = ? WHERE type_id = ?";
        $this->prepareAndExecute($sql, [$defaultType, $id]);
        
        if ($id != $defaultType) {
            $sql = "DELETE FROM type WHERE id = ?";
            return $this->prepareAndExecute($sql, [$id]);
        }
        
        return false;
    }

    public function isUsed($id) {
        $count = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE type_id = ?", [$id]);
        return $count > 0;
    }

    public function countEquipements($type_id) {
        return $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE type_id = ?", [$type_id]);
    }

    public function isDefault($id) {
        $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        return $id == $defaultType;
    }

    public function isTypePersonnel($typeId) {
        $type = $this->getById($typeId);
        return $type && isset($type['est_personnel']) && $type['est_personnel'] == 1;
    }

    public function getTypesPersonnels() {
        return $this->fetchAll("SELECT * FROM type WHERE est_personnel = 1 AND nom != 'Pas encore décidé' ORDER BY nom");
    }

    public function getTypesNonPersonnels() {
        return $this->fetchAll("SELECT * FROM type WHERE est_personnel = 0 AND nom != 'Pas encore décidé' ORDER BY nom");
    }
    
    public function getTypesForFilters() {
        return $this->fetchAll("SELECT id, nom FROM type WHERE nom != 'Pas encore décidé' ORDER BY nom");
    }


    public function getUsageTypes() {
        return [
            '0' => 'Usage Commun',
            '1' => 'Usage Personnel'
        ];
    }

    public function getUsageLabel($est_personnel) {
        $types = $this->getUsageTypes();
        return $types[$est_personnel] ?? 'Inconnu';
    }
}
?>