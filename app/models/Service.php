<?php
require_once(__DIR__ . '/../../core/Model.php');

class Service extends Model {
    public function __construct() {
        parent::__construct('service');
        $this->setPrimaryKey('id');
    }

    public function getAll() {
        return $this->fetchAll("SELECT * FROM service ORDER BY nom");
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM service WHERE id = ?", [$id]);
    }

    public function add($nom, $description) {
        if ($this->isExists('service', 'nom', $nom)) {
            throw new Exception("Un service avec ce nom existe déjà.");
        }
        
        $sql = "INSERT INTO service (nom, description) VALUES (?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $description]);
    }

    public function updateService($id, $nom, $description) {
        if ($this->isExists('service', 'nom', $nom, $id)) {
            throw new Exception("Un autre service avec ce nom existe déjà.");
        }
        
        $sql = "UPDATE service SET nom = ?, description = ? WHERE id = ?";
        return $this->prepareAndExecute($sql, [$nom, $description, $id]);
    }

    public function deleteService($id) {
        $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        
        if (!$defaultService) {
            $this->add('Pas encore décidé', 'Service par défaut pour les éléments non assignés');
            $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        }
        
        // Mettre à jour les références vers le service par défaut
        $tablesUtilisateurs = ['agent', 'technicien', 'admin', 'support'];
        foreach ($tablesUtilisateurs as $table) {
            $sql = "UPDATE $table SET service_id = ? WHERE service_id = ?";
            $this->prepareAndExecute($sql, [$defaultService, $id]);
        }
        
        $sql = "UPDATE salle SET service_id = ? WHERE service_id = ?";
        $this->prepareAndExecute($sql, [$defaultService, $id]);
        
        $sql = "UPDATE materiel SET service_id = ? WHERE service_id = ?";
        $this->prepareAndExecute($sql, [$defaultService, $id]);
        
        if ($id != $defaultService) {
            $sql = "DELETE FROM service WHERE id = ?";
            return $this->prepareAndExecute($sql, [$id]);
        }
        
        return false;
    }

    public function isUsed($id) {
        $countAgents = $this->fetchColumn("SELECT COUNT(*) FROM agent WHERE service_id = ?", [$id]);
        $countTechniciens = $this->fetchColumn("SELECT COUNT(*) FROM technicien WHERE service_id = ?", [$id]);
        $countSupports = $this->fetchColumn("SELECT COUNT(*) FROM support WHERE service_id = ?", [$id]);
        $countAdmins = $this->fetchColumn("SELECT COUNT(*) FROM admin WHERE service_id = ?", [$id]);
        $countMateriel = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE service_id = ?", [$id]);
        $countSalles = $this->fetchColumn("SELECT COUNT(*) FROM salle WHERE service_id = ?", [$id]);
        
        return ($countAgents + $countTechniciens + $countSupports + $countAdmins + $countMateriel + $countSalles) > 0;
    }

    public function isDefault($id) {
        $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        return $id == $defaultService;
    }

    public function getForSelect() {
        return $this->fetchAll("SELECT id, nom FROM service ORDER BY nom");
    }
}
?>