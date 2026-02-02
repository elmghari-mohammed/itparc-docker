<?php
require_once(__DIR__ . '/../../core/Model.php');

class Salle extends Model {
    public function __construct() {
        parent::__construct('salle');
        $this->setPrimaryKey('id');
    }

    public function getAll() {
        return $this->fetchAll("
            SELECT s.*, sv.nom as service_nom 
            FROM salle s 
            LEFT JOIN service sv ON s.service_id = sv.id 
            ORDER BY s.nom
        ");
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM salle WHERE id = ?", [$id]);
    }

    public function add($nom, $numero, $service_id, $capacite) {
        if ($this->isExists('salle', 'nom', $nom) || $this->isExists('salle', 'numero', $numero)) {
            throw new Exception("Une salle avec ce nom ou numéro existe déjà.");
        }
        
        $sql = "INSERT INTO salle (nom, numero, service_id, capacite) VALUES (?, ?, ?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $numero, $service_id, $capacite]);
    }

    public function updateSalle($id, $nom, $numero, $service_id, $capacite) {
        if ($this->isExists('salle', 'nom', $nom, $id)) {
            throw new Exception("Une autre salle avec ce nom existe déjà.");
        }
        
        if ($this->isExists('salle', 'numero', $numero, $id)) {
            throw new Exception("Une autre salle avec ce numéro existe déjà.");
        }
        
        $sql = "UPDATE salle SET nom = ?, numero = ?, service_id = ?, capacite = ? WHERE id = ?";
        return $this->prepareAndExecute($sql, [$nom, $numero, $service_id, $capacite, $id]);
    }

    public function deleteSalle($id) {
        $tablesUtilisateurs = ['agent', 'technicien', 'admin', 'support'];
        foreach ($tablesUtilisateurs as $table) {
            $sql = "UPDATE $table SET salle_id = NULL WHERE salle_id = ?";
            $this->prepareAndExecute($sql, [$id]);
        }
        
        $sql = "UPDATE materiel SET salle_id = NULL WHERE salle_id = ?";
        $this->prepareAndExecute($sql, [$id]);
        
        $sql = "DELETE FROM salle WHERE id = ?";
        return $this->prepareAndExecute($sql, [$id]);
    }

    public function isUsed($id) {
        $countAgents = $this->fetchColumn("SELECT COUNT(*) FROM agent WHERE salle_id = ?", [$id]);
        $countTechniciens = $this->fetchColumn("SELECT COUNT(*) FROM technicien WHERE salle_id = ?", [$id]);
        $countSupports = $this->fetchColumn("SELECT COUNT(*) FROM support WHERE salle_id = ?", [$id]);
        $countAdmins = $this->fetchColumn("SELECT COUNT(*) FROM admin WHERE salle_id = ?", [$id]);
        $countMateriel = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE salle_id = ?", [$id]);
        
        return ($countAgents + $countTechniciens + $countSupports + $countAdmins + $countMateriel) > 0;
    }

    public function countEquipements($salle_id) {
        return $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE salle_id = ?", [$salle_id]);
    }

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
}
?>