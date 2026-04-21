<?php
require_once(__DIR__ . '/../../core/Model.php');

class Infrastructure extends Model {
    
    public function __construct() {
        parent::__construct();
        // Définir une table par défaut (même si elle n'est pas utilisée)
    }
    
    // Méthode polyvalente pour vérifier l'existence d'une valeur
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
    
    // Services
    public function getServices() {
        return $this->fetchAll("SELECT * FROM service ORDER BY nom");
    }
    
    public function addService($nom, $description) {
        // Vérifier si le service existe déjà
        if ($this->isExists('service', 'nom', $nom)) {
            throw new Exception("Un service avec ce nom existe déjà.");
        }
        
        $sql = "INSERT INTO service (nom, description) VALUES (?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $description]);
    }
    
    public function updateService($id, $nom, $description) {
        // Vérifier si un autre service a déjà ce nom
        if ($this->isExists('service', 'nom', $nom, $id)) {
            throw new Exception("Un autre service avec ce nom existe déjà.");
        }
        
        $sql = "UPDATE service SET nom = ?, description = ? WHERE id = ?";
        return $this->prepareAndExecute($sql, [$nom, $description, $id]);
    }
    
    public function deleteService($id) {
        // Récupérer le service par défaut "Pas encore décidé"
        $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        
        if (!$defaultService) {
            // Créer le service par défaut s'il n'existe pas
            $this->addService('Pas encore décidé', 'Service par défaut pour les éléments non assignés');
            $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        }
        
        // Mettre à jour les références vers le service par défaut
        $tablesUtilisateurs = ['agent', 'technicien', 'admin', 'support'];
        foreach ($tablesUtilisateurs as $table) {
            $sql = "UPDATE $table SET service_id = ? WHERE service_id = ?";
            $this->prepareAndExecute($sql, [$defaultService, $id]);
        }
        
        // Mettre à jour les références dans la table salle
        $sql = "UPDATE salle SET service_id = ? WHERE service_id = ?";
        $this->prepareAndExecute($sql, [$defaultService, $id]);
        
        // Mettre à jour les références dans la table materiel
        $sql = "UPDATE materiel SET service_id = ? WHERE service_id = ?";
        $this->prepareAndExecute($sql, [$defaultService, $id]);
        
        // Maintenant supprimer le service (sauf s'il s'agit du service par défaut)
        if ($id != $defaultService) {
            $sql = "DELETE FROM service WHERE id = ?";
            return $this->prepareAndExecute($sql, [$id]);
        }
        
        return false; // Ne pas supprimer le service par défaut
    }
    
    public function isServiceUsed($id) {
        $countAgents = $this->fetchColumn("SELECT COUNT(*) FROM agent WHERE service_id = ?", [$id]);
        $countTechniciens = $this->fetchColumn("SELECT COUNT(*) FROM technicien WHERE service_id = ?", [$id]);
        $countSupports = $this->fetchColumn("SELECT COUNT(*) FROM support WHERE service_id = ?", [$id]);
        $countAdmins = $this->fetchColumn("SELECT COUNT(*) FROM admin WHERE service_id = ?", [$id]);
        $countMateriel = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE service_id = ?", [$id]);
        $countSalles = $this->fetchColumn("SELECT COUNT(*) FROM salle WHERE service_id = ?", [$id]);
        
        return ($countAgents + $countTechniciens + $countSupports + $countAdmins + $countMateriel + $countSalles) > 0;
    }
    
    // Types d'équipements
    public function getTypes() {
        return $this->fetchAll("SELECT * FROM type ORDER BY nom");
    }
    
    public function addType($nom, $description) {
        // Vérifier si le type existe déjà
        if ($this->isExists('type', 'nom', $nom)) {
            throw new Exception("Un type avec ce nom existe déjà.");
        }
        
        $sql = "INSERT INTO type (nom, description) VALUES (?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $description]);
    }
    
    public function updateType($id, $nom, $description) {
        // Vérifier si un autre type a déjà ce nom
        if ($this->isExists('type', 'nom', $nom, $id)) {
            throw new Exception("Un autre type avec ce nom existe déjà.");
        }
        
        $sql = "UPDATE type SET nom = ?, description = ? WHERE id = ?";
        return $this->prepareAndExecute($sql, [$nom, $description, $id]);
    }
    
    public function deleteType($id) {
        // Récupérer le type par défaut "Pas encore décidé"
        $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        
        if (!$defaultType) {
            // Créer le type par défaut s'il n'existe pas
            $this->addType('Pas encore décidé', 'Type par défaut pour les équipements non classés');
            $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        }
        
        // Mettre à jour les références dans la table materiel vers le type par défaut
        $sql = "UPDATE materiel SET type_id = ? WHERE type_id = ?";
        $this->prepareAndExecute($sql, [$defaultType, $id]);
        
        // Maintenant supprimer le type (sauf s'il s'agit du type par défaut)
        if ($id != $defaultType) {
            $sql = "DELETE FROM type WHERE id = ?";
            return $this->prepareAndExecute($sql, [$id]);
        }
        
        return false; // Ne pas supprimer le type par défaut
    }
    
    public function isTypeUsed($id) {
        $count = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE type_id = ?", [$id]);
        return $count > 0;
    }
    
    public function countEquipementsByType($type_id) {
        return $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE type_id = ?", [$type_id]);
    }
    
    // Salles
    public function getSalles() {
        return $this->fetchAll("
            SELECT s.*, sv.nom as service_nom 
            FROM salle s 
            LEFT JOIN service sv ON s.service_id = sv.id 
            ORDER BY s.nom
        ");
    }
    
    public function addSalle($nom, $numero, $service_id, $capacite) {
        // Vérifier si la salle existe déjà (nom + numéro)
        if ($this->isExists('salle', 'nom', $nom) || $this->isExists('salle', 'numero', $numero)) {
            throw new Exception("Une salle avec ce nom ou numéro existe déjà.");
        }
        
        $sql = "INSERT INTO salle (nom, numero, service_id, capacite) VALUES (?, ?, ?, ?)";
        return $this->prepareAndExecute($sql, [$nom, $numero, $service_id, $capacite]);
    }
    
    public function updateSalle($id, $nom, $numero, $service_id, $capacite) {
        // Vérifier si une autre salle a déjà ce nom ou numéro
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
        // Mettre à jour les références vers NULL
        $tablesUtilisateurs = ['agent', 'technicien', 'admin', 'support'];
        foreach ($tablesUtilisateurs as $table) {
            $sql = "UPDATE $table SET salle_id = NULL WHERE salle_id = ?";
            $this->prepareAndExecute($sql, [$id]);
        }
        
        // Mettre à jour les références dans la table materiel vers NULL
        $sql = "UPDATE materiel SET salle_id = NULL WHERE salle_id = ?";
        $this->prepareAndExecute($sql, [$id]);
        
        // Maintenant supprimer la salle
        $sql = "DELETE FROM salle WHERE id = ?";
        return $this->prepareAndExecute($sql, [$id]);
    }
    
    public function isSalleUsed($id) {
        $countAgents = $this->fetchColumn("SELECT COUNT(*) FROM agent WHERE salle_id = ?", [$id]);
        $countTechniciens = $this->fetchColumn("SELECT COUNT(*) FROM technicien WHERE salle_id = ?", [$id]);
        $countSupports = $this->fetchColumn("SELECT COUNT(*) FROM support WHERE salle_id = ?", [$id]);
        $countAdmins = $this->fetchColumn("SELECT COUNT(*) FROM admin WHERE salle_id = ?", [$id]);
        $countMateriel = $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE salle_id = ?", [$id]);
        
        return ($countAgents + $countTechniciens + $countSupports + $countAdmins + $countMateriel) > 0;
    }
    
    public function countEquipementsBySalle($salle_id) {
        return $this->fetchColumn("SELECT COUNT(*) FROM materiel WHERE salle_id = ?", [$salle_id]);
    }
    
    // Pour les formulaires
    public function getServicesForSelect() {
        return $this->fetchAll("SELECT id, nom FROM service ORDER BY nom");
    }
    
    // Vérifier si un service est le service par défaut
    public function isDefaultService($id) {
        $defaultService = $this->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        return $id == $defaultService;
    }
    
    // Vérifier si un type est le type par défaut
    public function isDefaultType($id) {
        $defaultType = $this->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");
        return $id == $defaultType;
    }
}
?>