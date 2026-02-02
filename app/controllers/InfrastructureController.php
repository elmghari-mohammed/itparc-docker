<?php
require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/../models/Service.php');
require_once(__DIR__ . '/../models/Type.php');
require_once(__DIR__ . '/../models/Salle.php');

class InfrastructureController extends Controller {
    
    public function gerer() {
        $this->checkRole(['admin']);
        
        $serviceModel = new Service();
        $typeModel = new Type();
        $salleModel = new Salle();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? null;
            $type = $_POST['type'] ?? null;
            
            switch ($action) {
                case 'add':
                    $this->addElement($serviceModel, $typeModel, $salleModel, $type, $_POST);
                    break;
                    
                case 'edit':
                    $this->editElement($serviceModel, $typeModel, $salleModel, $type, $id, $_POST);
                    break;
                    
                case 'delete':
                    $this->deleteElement($serviceModel, $typeModel, $salleModel, $type, $id);
                    break;
            }
            
            $this->redirect('infrastructure');
        }
        
        $services = $serviceModel->getAll();
        $types = $typeModel->getAll();
        $salles = $salleModel->getAll();
        
        $equipementsParType = [];
        $usageTypes = $typeModel->getUsageTypes();
        
        foreach ($types as $type) {
            $equipementsParType[$type['id']] = $typeModel->countEquipements($type['id']);
        }
        
        $defaultServiceId = $serviceModel->fetchColumn("SELECT id FROM service WHERE nom = 'Pas encore décidé'");
        $defaultTypeId = $typeModel->fetchColumn("SELECT id FROM type WHERE nom = 'Pas encore décidé'");

        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->main('infrastructure/gerer', [
            'services' => $services,
            'types' => $types,
            'salles' => $salles,
            'equipementsParType' => $equipementsParType,
            'usageTypes' => $usageTypes,
            'defaultServiceId' => $defaultServiceId,
            'defaultTypeId' => $defaultTypeId,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'page_title' => 'Gestion de l\'Infrastructure',
            'additional_css' => ['infrastructure/gerer.css'],
            'additional_js' => ['infrastructure/gerer.js']
        ]);
    }
    
    private function addElement($serviceModel, $typeModel, $salleModel, $type, $data) {
        try {
            $result = false;
            
            switch ($type) {
                case 'service':
                    $result = $serviceModel->add($data['nom'], $data['description'] ?? '');
                    break;
                    
                case 'type':
                    $est_personnel = isset($data['est_personnel']) ? (bool)$data['est_personnel'] : false;
                    $result = $typeModel->add($data['nom'], $data['description'] ?? '', $est_personnel);
                    break;
                    
                case 'salle':
                    $result = $salleModel->add($data['nom'], $data['numero'], $data['service_id'], $data['capacite']);
                    break;
            }
            
            if ($result) {
                $_SESSION['success_message'] = ucfirst($type) . " ajouté avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'ajout du " . $type . ".";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erreur: " . $e->getMessage();
        }
    }
    
    private function editElement($serviceModel, $typeModel, $salleModel, $type, $id, $data) {
        try {
            $result = false;
            
            if ($type === 'service' && $serviceModel->isDefault($id)) {
                $_SESSION['error_message'] = "Impossible de modifier le service par défaut 'Pas encore décidé'.";
                return;
            }
            
            if ($type === 'type' && $typeModel->isDefault($id)) {
                $_SESSION['error_message'] = "Impossible de modifier le type par défaut 'Pas encore décidé'.";
                return;
            }
            
            switch ($type) {
                case 'service':
                    $result = $serviceModel->updateService($id, $data['nom'], $data['description'] ?? '');
                    break;
                    
                case 'type':
                    $est_personnel = isset($data['est_personnel']) ? (bool)$data['est_personnel'] : false;
                    $result = $typeModel->updateType($id, $data['nom'], $data['description'] ?? '', $est_personnel);
                    break;
                    
                case 'salle':
                    $result = $salleModel->updateSalle($id, $data['nom'], $data['numero'], $data['service_id'], $data['capacite']);
                    break;
            }
            
            if ($result) {
                $_SESSION['success_message'] = ucfirst($type) . " modifié avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la modification du " . $type . ".";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erreur: " . $e->getMessage();
        }
    }
    
    private function deleteElement($serviceModel, $typeModel, $salleModel, $type, $id) {
        try {
            if ($type === 'service' && $serviceModel->isDefault($id)) {
                $_SESSION['error_message'] = "Impossible de supprimer le service par défaut 'Pas encore décidé'.";
                return;
            }
            
            if ($type === 'type' && $typeModel->isDefault($id)) {
                $_SESSION['error_message'] = "Impossible de supprimer le type par défaut 'Pas encore décidé'.";
                return;
            }
            
            $result = false;
            
            switch ($type) {
                case 'service':
                    $result = $serviceModel->deleteService($id);
                    break;
                    
                case 'type':
                    $result = $typeModel->deleteType($id);
                    break;
                    
                case 'salle':
                    $result = $salleModel->deleteSalle($id);
                    break;
            }
            
            if ($result) {
                $_SESSION['success_message'] = ucfirst($type) . " supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression du " . $type . ".";
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $_SESSION['error_message'] = "Impossible de supprimer car cet élément est utilisé ailleurs dans le système.";
            } else {
                $_SESSION['error_message'] = "Erreur: " . $e->getMessage();
            }
        }
    }
}
?>