<?php
// /app/controllers/EquipementsController.php

require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/../models/Equipement.php');
require_once(__DIR__ . '/../models/Salle.php');
require_once(__DIR__ . '/../models/Service.php');
require_once(__DIR__ . '/../models/User.php'); 
require_once(__DIR__ . '/../models/Type.php'); 

class EquipementsController extends Controller {
    private $equipementModel;
    private $salleModel;
    private $serviceModel;
    private $userModel;
    private $typeModel;
    


    public function __construct() {
        parent::__construct();
        $this->equipementModel = new Equipement();
        $this->salleModel = new Salle();
        $this->serviceModel = new Service();
        $this->userModel = new User();
        $this->typeModel = new Type();
        
        // Vérifier l'authentification pour toutes les méthodes
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

    /**
     * Affiche la liste des équipements de l'utilisateur
     */
    public function liste() {
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['id'];
        
        // Récupérer les équipements de l'utilisateur avec état réel
        $equipements = $this->equipementModel->getMesEquipements($userRole, $userId);
        
        // Préparer les données pour la vue
        $equipmentData = [];
        foreach ($equipements as $equip) {
            $equipmentData[$equip['id']] = $this->formatEquipmentData($equip);
        }
        
        // Afficher la vue
        $this->main('equipements/liste', [
            'equipements' => $equipements,
            'equipmentData' => $equipmentData,
            'page_title' => 'Mes Équipements',
            'additional_css' => ['equipements/liste.css'],
            'additional_js' => ['equipements/liste.js']
        ]);
    }

/**
 * Formate les données d'un équipement pour l'affichage avec état réel
 */
private function formatEquipmentData($equip) {
    // Déterminer l'icône selon le type
    $icon = $this->getEquipmentIcon($equip['type_nom']);
    
    // Déterminer l'état réel
    $status = $this->determinerEtatReel($equip);
    $libelleEtat = $this->getLibelleEtat($status);
    
    // Récupérer la dernière réclamation non résolue
    $derniereReclamation = $this->equipementModel->getDerniereReclamationNonResolue($equip['id']);
    
    // Récupérer les informations utilisateur (maintenant obligatoire)
    $userInfo = $this->equipementModel->getUserInfoForEquipment($equip['user_role'], $equip['id_user']);
    
    // Vérifier que les informations utilisateur existent
    if (!$userInfo) {
        // Utilisateur introuvable - log l'erreur et utilise des valeurs par défaut
        error_log("Utilisateur non trouvé pour l'équipement ID: " . $equip['id'] . " - Role: " . $equip['user_role'] . " - User ID: " . $equip['id_user']);
        $userInfo = [
            'nom' => 'Utilisateur',
            'prenom' => 'Inconnu',
            'service_nom' => 'Service inconnu',
            'salle_nom' => null,
            'salle_numero' => null
        ];
    }
    
    return [
        'title' => $equip['marque'] . ' ' . $equip['model'],
        'subtitle' => $equip['type_nom'],
        'icon' => $icon,
        'status' => $status,
        'libelleEtat' => $libelleEtat,
        'derniereReclamation' => $derniereReclamation,
        'userInfo' => $userInfo,
        'details' => [
            'brand' => $equip['marque'],
            'model' => $equip['model'],
            'type' => $equip['type_nom'],
            'state' => $libelleEtat,
            'startDate' => $equip['date_enregistrement'] ? date('d F Y', strtotime($equip['date_enregistrement'])) : 'Non spécifié',
            'warrantyEnd' => $equip['garantie_fin'] ? date('d F Y', strtotime($equip['garantie_fin'])) : 'Non spécifié',
            'usageTime' => $this->calculateUsageTime($equip['date_enregistrement']),
            'currentState' => $libelleEtat,
            'lastCheck' => $this->getDateDerniereVerification($equip),
            'code' => $equip['serial_number'],
            'category' => $equip['type_nom'],
            'priority' => $this->determinerPriorite($equip),
            'nbReclamations' => $equip['nb_reclamations'] ?? 0,
            'user' => $userInfo['nom'] . ' ' . $userInfo['prenom'],
            'department' => $userInfo['service_nom'],
            'location' => $userInfo['salle_nom'] ? $userInfo['salle_nom'] . ' (' . $userInfo['salle_numero'] . ')' : 'Non spécifié'
        ]
    ];
}

    /**
     * Retourne l'icône appropriée pour le type d'équipement
     */
    private function getEquipmentIcon($typeNom) {
        $type = strtolower($typeNom);
        
        if (strpos($type, 'portable') !== false) return 'laptop';
        if (strpos($type, 'bureau') !== false) return 'desktop';
        if (strpos($type, 'imprimante') !== false) return 'print';
        if (strpos($type, 'projecteur') !== false) return 'video';
        if (strpos($type, 'mobile') !== false) return 'mobile-alt';
        if (strpos($type, 'switch') !== false || strpos($type, 'routeur') !== false) return 'network-wired';
        if (strpos($type, 'serveur') !== false) return 'server';
        if (strpos($type, 'scanner') !== false) return 'scanner';
        if (strpos($type, 'tablette') !== false) return 'tablet-alt';
        
        return 'desktop';
    }

    /**
     * Retourne le libellé d'état en français
     */
    private function getLibelleEtat($status) {
        switch ($status) {
            case 'broken': return 'En panne';
            case 'warning': return 'Attention nécessaire';
            case 'working': return 'En fonctionnement';
            default: return 'État inconnu';
        }
    }

    /**
     * Détermine l'état réel de l'équipement
     */
    private function determinerEtatReel($equip) {
        // Si l'équipement a des réclamations en attente ou en cours
        if (isset($equip['etat_reel']) && in_array($equip['etat_reel'], ['en_attente', 'en_cours'])) {
            return 'broken';
        }
        
        // Vérifier la date de fin de garantie
        if ($equip['garantie_fin'] && strtotime($equip['garantie_fin']) < time()) {
            return 'warning';
        }
        
        // Vérifier la date de fin de service
        if ($equip['service_fin'] && strtotime($equip['service_fin']) < time()) {
            return 'warning';
        }
        
        return 'working';
    }

    /**
     * Détermine la priorité de l'équipement
     */
    private function determinerPriorite($equip) {
        $hasReclamations = $this->equipementModel->hasReclamationsEnCours($equip['id']);
        
        if ($hasReclamations) {
            return 'Élevée';
        }
        
        // Vérifier si la garantie expire bientôt (dans les 30 jours)
        if ($equip['garantie_fin'] && strtotime($equip['garantie_fin']) < strtotime('+30 days')) {
            return 'Moyenne';
        }
        
        return 'Normale';
    }

    /**
     * Récupère la date de la dernière vérification
     */
    private function getDateDerniereVerification($equip) {
        // Si l'équipement a une réclamation en cours, utiliser la date de réclamation
        if (isset($equip['etat_reel']) && in_array($equip['etat_reel'], ['en_attente', 'en_cours'])) {
            return $equip['derniere_reclamation'] ? date('d F Y', strtotime($equip['derniere_reclamation'])) : 'Date inconnue';
        }
        
        // Sinon, utiliser la date de début d'utilisation
        if ($equip['date_enregistrement']) {
            return date('d F Y', strtotime($equip['date_enregistrement']));
        }
        
        return 'Non vérifié';
    }

    /**
     * Calcule le temps d'utilisation
     */
    private function calculateUsageTime($startDate) {
        if (!$startDate) return 'Non spécifié';
        
        $start = new DateTime($startDate);
        $now = new DateTime();
        $interval = $start->diff($now);
        
        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;
        
        $result = [];
        if ($years > 0) $result[] = "$years an" . ($years > 1 ? 's' : '');
        if ($months > 0) $result[] = "$months mois";
        if ($days > 0) $result[] = "$days jour" . ($days > 1 ? 's' : '');
        
        return empty($result) ? 'Moins d\'un jour' : implode(', ', $result);
    }

    /**
     * Affiche les détails d'un équipement (méthode API pour AJAX)
     */
    public function details($id) {
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['id'];
        
        $equipement = $this->equipementModel->getDetailsComplets($id, $userRole, $userId);
        
        if (!$equipement) {
            http_response_code(404);
            echo json_encode(['error' => 'Équipement non trouvé']);
            return;
        }
        
        // Récupérer l'historique des réclamations
        $historiqueReclamations = $this->equipementModel->getHistoriqueReclamations($id);
        
        $response = $this->formatEquipmentData($equipement);
        $response['historiqueReclamations'] = $historiqueReclamations;
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    /**
 * Affiche le formulaire d'ajout d'équipement
 */
public function ajouter() {
    $this->checkRole(['admin']);
    
    // Récupérer les types avec information personnelle/non personnelle
    $typesPersonnels = $this->typeModel->getTypesPersonnels();
    $typesNonPersonnels = $this->typeModel->getTypesNonPersonnels();
    $services = $this->serviceModel->getAll();
    $salles = $this->salleModel->getAll();
    
    $roles = [
        'admin' => 'Administrateur',
        'agent' => 'Agent',
        'support' => 'Support'
    ];
    
    $success_message = $_SESSION['success_message'] ?? '';
    $error_message = $_SESSION['error_message'] ?? '';
    $form_data = $_SESSION['form_data'] ?? [];
    
    unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_data']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = $this->processAddEquipment();
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
            $this->redirect('equipements/ajouter');
        } else {
            $_SESSION['error_message'] = $result['message'];
            $_SESSION['form_data'] = $_POST;
            $this->redirect('equipements/ajouter');
        }
        return;
    }
    
    if (isset($_GET['ajax']) && $_GET['ajax'] === 'users' && isset($_GET['role'])) {
        $users = $this->equipementModel->getUsersByRole($_GET['role']);
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    }
    
    // Afficher le formulaire
    $this->main('equipements/ajouter', [
        'typesPersonnels' => $typesPersonnels,
        'typesNonPersonnels' => $typesNonPersonnels,
        'services' => $services,
        'salles' => $salles,
        'roles' => $roles,
        'success_message' => $success_message,
        'error_message' => $error_message,
        'form_data' => $form_data,
        'page_title' => 'Ajouter Un Équipement',
        'additional_css' => ['equipements/ajouter.css'],
        'additional_js' => ['equipements/ajouter.js']
    ]);
}


/**
 * Traite l'ajout d'un équipement
 */
private function processAddEquipment() {
    $errors = [];
    
    // Champs obligatoires de base
    $requiredFields = ['serialNumber', 'marque', 'modele', 'type'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ " . $this->getFieldLabel($field) . " est obligatoire.";
        }
    }
    
    // Vérifier si le numéro de série existe déjà
    $existing = $this->equipementModel->checkSerialNumber($_POST['serialNumber']);
    if ($existing) {
        $errors[] = "Ce numéro de série existe déjà.";
    }
    
    // Vérifier le type et les champs requis selon le type
    $typeId = $_POST['type'];
    $isTypePersonnel = $this->typeModel->isTypePersonnel($typeId);
    
    if ($isTypePersonnel) {
        // Pour les types personnels, user_role et user_id sont obligatoires
        if (empty($_POST['user_role']) || empty($_POST['user_id'])) {
            $errors[] = "Pour un équipement personnel, l'assignation à un utilisateur est obligatoire.";
        } else {
            // Vérifier que l'utilisateur existe
            $userExists = $this->userModel->userExists($_POST['user_role'], $_POST['user_id']);
            if (!$userExists) {
                $errors[] = "L'utilisateur sélectionné n'existe pas.";
            }
        }
    } else {
        // Pour les types non personnels, service et salle sont obligatoires
        if (empty($_POST['service_id']) || empty($_POST['salle_id'])) {
            $errors[] = "Pour un équipement non personnel, l'assignation à un service et une salle est obligatoire.";
        }
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }
    
    // Préparer les données pour l'insertion
    $data = [
        'serial_number' => trim($_POST['serialNumber']),
        'marque' => trim($_POST['marque']),
        'model' => trim($_POST['modele']),
        'type_id' => $typeId,
        'garantie_fin' => !empty($_POST['garantieFin']) ? $_POST['garantieFin'] : null,
        'service_fin' => !empty($_POST['serviceFin']) ? $_POST['serviceFin'] : null,
        'detail' => !empty($_POST['details']) ? trim($_POST['details']) : '',
        'date_enregistrement' => date('Y-m-d'),

    ];
    
    // Ajouter les champs selon le type
    if ($isTypePersonnel) {
        $data['user_role'] = $_POST['user_role'];
        $data['id_user'] = $_POST['user_id'];
        $data['service_id'] = null;
        $data['salle_id'] = null;
    } else {
        $data['user_role'] = null;
        $data['id_user'] = null;
        $data['service_id'] = $_POST['service_id'];
        $data['salle_id'] = $_POST['salle_id'];
    }
    
    // Insérer l'équipement
    $success = $this->equipementModel->create($data);
    
    if ($success) {
        $equipementId = $this->equipementModel->lastInsertId();
        
        // Créer une notification si c'est un équipement personnel
        if ($isTypePersonnel) {
            $this->createEquipmentNotification($equipementId, $data);
        }
        
        return ['success' => true, 'message' => 'Équipement ajouté avec succès!'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'équipement.'];
    }
}

/**
 * Crée une notification pour l'utilisateur
 */
private function createEquipmentNotification($equipementId, $equipementData) {
    $equipement = $this->equipementModel->getById($equipementId);
    
    if ($equipement && !empty($equipementData['user_role']) && !empty($equipementData['id_user'])) {
        $notificationId = 'NOTIF_EQUIP_' . uniqid();
        $sujet = 'Nouvel équipement assigné';
        $message = "Un nouvel équipement {$equipement['marque']} {$equipement['model']} (SN: {$equipement['serial_number']}) vous a été assigné.";
        $lien = '/equipements/liste';
        
        $notificationData = [
            'id' => $notificationId,
            'user_role' => $equipementData['user_role'],
            'user_id' => $equipementData['id_user'], // Utiliser id_user au lieu de user_id
            'sujet' => $sujet,
            'message' => $message,
            'lien' => $lien,
            'type' => 'equipement',
            'type_id' => $equipementId,
            'creator_role' => $_SESSION['role'],
            'creator_id' => $_SESSION['id']
        ];
        
        $this->equipementModel->createNotification($notificationData);
    }
}


/**
 * Gerer la liste de tous les équipements (pour admin)
 */
    /**
     * Gerer la liste de tous les équipements (pour admin)
     */
    public function gerer() {
        // Vérifier les permissions (seuls les admins peuvent voir tous les équipements)
        $this->checkRole(['admin']);

        // Traitement des actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $equipement_id = $_POST['equipement_id'] ?? null;

            switch ($action) {
                case 'delete':
                    $this->deleteEquipment($equipement_id);
                    break;

                case 'edit':
                    $this->editEquipment($equipement_id, $_POST);
                    break;
            }

            // Redirection pour éviter la resoumission du formulaire
            $this->redirect('equipements/gerer');
        }

        // Récupérer tous les équipements avec les informations complètes
        $materiels = $this->equipementModel->getTousEquipementsComplets(); // Utiliser getTousEquipementsComplets() au lieu de getTousEquipements()

        // Récupérer les types et services pour les filtres - CORRECTION ICI
        $types = $this->typeModel->getAll(); // Utiliser le modèle Type
        $services = $this->serviceModel->getAll(); // Utiliser le modèle Service

        // Récupérer les messages de session
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';

        // Nettoyer les messages après les avoir récupérés
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        // Afficher la vue
        $this->main('equipements/gerer', [
            'materiels' => $materiels,
            'types' => $types,
            'services' => $services,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'page_title' => 'Gestion des Équipements'
        ]);
    }

/**
 * Supprime un équipement
 */
private function deleteEquipment($equipementId) {
    try {
        // Vérifier si l'équipement existe
        $equipement = $this->equipementModel->getById($equipementId);
        if (!$equipement) {
            $_SESSION['error_message'] = 'Équipement non trouvé.';
            return;
        }
        
        // Vérifier si l'équipement a des réclamations en cours
        $hasReclamations = $this->equipementModel->hasReclamationsEnCours($equipementId);
        if ($hasReclamations) {
            $_SESSION['error_message'] = 'Impossible de supprimer cet équipement car il a des réclamations en cours.';
            return;
        }
        
        // Supprimer l'équipement
        $success = $this->equipementModel->deleteMateriel($equipementId);
        
        if ($success) {
            $_SESSION['success_message'] = 'Équipement supprimé avec succès.';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la suppression de l\'équipement.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erreur: ' . $e->getMessage();
    }
}

/**
 * Modifie un équipement
 */
private function editEquipment($equipementId, $data) {
    try {
        // Vérifier si l'équipement existe
        $equipement = $this->equipementModel->getById($equipementId);
        if (!$equipement) {
            $_SESSION['error_message'] = 'Équipement non trouvé.';
            return;
        }
        
        // Préparer les données de mise à jour
        $updateData = [
            'marque' => trim($data['marque'] ?? $equipement['marque']),
            'model' => trim($data['model'] ?? $equipement['model']),
            'type_id' => $data['type_id'] ?? $equipement['type_id'],
            'garantie_fin' => !empty($data['garantie_fin']) ? $data['garantie_fin'] : null,
            'service_fin' => !empty($data['service_fin']) ? $data['service_fin'] : null,
            'detail' => trim($data['detail'] ?? $equipement['detail']),
            'date_enregistrement' => !empty($data['date_enregistrement']) ? $data['date_enregistrement'] : $equipement['date_enregistrement']
        ];
        
        // Mettre à jour l'équipement
        $success = $this->equipementModel->update($equipementId, $updateData);
        
        if ($success) {
            $_SESSION['success_message'] = 'Équipement modifié avec succès.';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la modification de l\'équipement.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erreur: ' . $e->getMessage();
    }
}

/**
 * Réassigne un équipement
 */
private function reassignEquipment($equipementId, $data) {
    try {
        // Vérifier si l'équipement existe
        $equipement = $this->equipementModel->getById($equipementId);
        if (!$equipement) {
            $_SESSION['error_message'] = 'Équipement non trouvé.';
            return;
        }
        
        // Vérifier que le nouvel utilisateur existe
        $newUserRole = $data['new_user_role'] ?? null;
        $newUserId = $data['new_user_id'] ?? null;
        
        if (!$newUserRole || !$newUserId) {
            $_SESSION['error_message'] = 'Veuillez sélectionner un utilisateur valide.';
            return;
        }
        
        $userExists = $this->userModel->userExists($newUserRole, $newUserId);
        if (!$userExists) {
            $_SESSION['error_message'] = 'L\'utilisateur sélectionné n\'existe pas.';
            return;
        }
        
        // Préparer les données de mise à jour
        $updateData = [
            'user_role' => $newUserRole,
            'id_user' => $newUserId,
            'date_enregistrement' => date('Y-m-d') // Date de réassignation
        ];
        
        // Mettre à jour l'équipement
        $success = $this->equipementModel->update($equipementId, $updateData);
        
        if ($success) {
            // Créer une notification pour le nouvel utilisateur
            $this->createReassignmentNotification($equipementId, $updateData);
            
            $_SESSION['success_message'] = 'Équipement réassigné avec succès.';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la réassignation de l\'équipement.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erreur: ' . $e->getMessage();
    }
}

/**
 * Crée une notification pour la réassignation
 */
private function createReassignmentNotification($equipementId, $equipementData) {
    $equipement = $this->equipementModel->getById($equipementId);
    
    if ($equipement) {
        $notificationId = 'NOTIF_REASSIGN_' . uniqid();
        $sujet = 'Équipement réassigné';
        $message = "L'équipement {$equipement['marque']} {$equipement['model']} (SN: {$equipement['serial_number']}) vous a été assigné.";
        $lien = '/equipements/liste';
        
        $notificationData = [
            'id' => $notificationId,
            'user_role' => $equipementData['user_role'],
            'user_id' => $equipementData['id_user'],
            'sujet' => $sujet,
            'message' => $message,
            'lien' => $lien,
            'type' => 'equipement',
            'type_id' => $equipementId,
            'creator_role' => $_SESSION['role'],
            'creator_id' => $_SESSION['id']
        ];
        
        $this->equipementModel->createNotification($notificationData);
    }
}


/**
 * Formate les données d'équipement pour l'affichage dans la gestion
 */
private function formatEquipmentForManagement($equip) {
    // Déterminer le statut selon la logique métier
    $status = 'disponible';
    $statusText = 'Disponible';
    $statusClass = 'status-warning';
    
    // Si l'équipement a des réclamations en attente ou en cours
    if (isset($equip['etat_reel']) && in_array($equip['etat_reel'], ['en_attente', 'en_cours'])) {
        $status = 'en_panne';
        $statusText = 'En panne';
        $statusClass = 'status-danger';
    }
    // Si l'équipement est assigné à un utilisateur
    else if ($equip['user_role'] && $equip['id_user'] && $equip['user_role'] !== 'Non assigné') {
        $status = 'en_service';
        $statusText = 'En service';
        $statusClass = 'status-good';
    }
    // Si non assigné
    else {
        $status = 'disponible';
        $statusText = 'Disponible';
        $statusClass = 'status-warning';
    }
    
    return [
        'id' => $equip['id'],
        'serial_number' => $equip['serial_number'],
        'marque' => $equip['marque'],
        'model' => $equip['model'],
        'type_nom' => $equip['type_nom'],
        'user_nom' => $equip['user_nom'],
        'user_prenom' => $equip['user_prenom'],
        'user_role' => $equip['user_role'],
        'service_nom' => $equip['service_nom'],
        'salle_nom' => $equip['salle_nom'],
        'salle_numero' => $equip['salle_numero'],
        'date_enregistrement' => $equip['date_enregistrement'],
        'garantie_fin' => $equip['garantie_fin'],
        'detail' => $equip['detail'],
        'type_id' => $equip['type_id'],
        'status' => $status,
        'status_text' => $statusText,
        'status_class' => $statusClass,
        'etat_reel' => $equip['etat_reel'] ?? null,
        'nb_reclamations' => $equip['nb_reclamations'] ?? 0,
        'full_user_name' => $equip['user_nom'] && $equip['user_prenom'] ? 
            $equip['user_prenom'] . ' ' . $equip['user_nom'] : 'Non assigné'
    ];
}

    /**
     * Formate les données pour "tous les équipements"
     */
    private function formatEquipmentDataTous($equip) {
        // Déterminer l'icône selon le type
        $icon = $this->getEquipmentIcon($equip['type_nom']);
        
        // Déterminer l'état réel
        $status = $this->determinerEtatReelTous($equip);
        $libelleEtat = $this->getLibelleEtatTous($status);
        
        // Déterminer l'affectation
        $affectation = $this->determinerAffectation($equip);
        
        // Récupérer l'historique
        $historique = $this->equipementModel->getHistoriqueEquipement($equip['id']);
        
        // Récupérer les informations utilisateur
        $userInfo = $this->equipementModel->getUserInfoForEquipment($equip['user_role'], $equip['id_user']);
        
        return [
            'title' => $equip['marque'] . ' ' . $equip['model'],
            'subtitle' => $equip['type_nom'],
            'icon' => $icon,
            'status' => $status,
            'libelleEtat' => $libelleEtat,
            'affectation' => $affectation,
            'historique' => $historique,
            'userInfo' => $userInfo,
            'details' => [
                'brand' => $equip['marque'],
                'model' => $equip['model'],
                'type' => $equip['type_nom'],
                'state' => $libelleEtat,
                'startDate' => $equip['date_enregistrement'] ? date('d F Y', strtotime($equip['date_enregistrement'])) : 'Non spécifié',
                'warrantyEnd' => $equip['garantie_fin'] ? date('d F Y', strtotime($equip['garantie_fin'])) : 'Non spécifié',
                'serviceEnd' => $equip['service_fin'] ? date('d F Y', strtotime($equip['service_fin'])) : 'Non spécifié',
                'usageTime' => $this->calculateUsageTime($equip['date_enregistrement']),
                'currentState' => $libelleEtat,
                'lastCheck' => $this->getDateDerniereVerification($equip),
                'code' => $equip['serial_number'],
                'category' => $equip['type_nom'],
                'priority' => $this->determinerPriorite($equip),
                'nbReclamations' => $equip['nb_reclamations'] ?? 0,
                'user' => $userInfo ? $userInfo['nom'] . ' ' . $userInfo['prenom'] : 'Non assigné',
                'department' => $userInfo['service_nom'] ?? 'Non spécifié',
                'location' => $userInfo['salle_nom'] ? $userInfo['salle_nom'] . ' (' . $userInfo['salle_numero'] . ')' : 'Non spécifié',
                'assignmentStatus' => $affectation
            ]
        ];
    }

    /**
     * Détermine l'état pour tous les équipements
     */
    private function determinerEtatReelTous($equip) {
        // Si l'équipement a des réclamations en attente ou en cours
        if (isset($equip['etat_reel']) && in_array($equip['etat_reel'], ['en_attente', 'en_cours'])) {
            return 'broken';
        }
        
        // Vérifier la date de fin de service
        if ($equip['service_fin'] && strtotime($equip['service_fin']) < time()) {
            return 'expired';
        }
        
        // Vérifier la date de fin de garantie
        if ($equip['garantie_fin'] && strtotime($equip['garantie_fin']) < time()) {
            return 'warning';
        }
        
        // Si non assigné
        if (!$equip['user_role'] || !$equip['id_user']) {
            return 'available';
        }
        
        return 'working';
    }

    /**
     * Retourne le libellé d'état pour tous les équipements
     */
    private function getLibelleEtatTous($status) {
        switch ($status) {
            case 'broken': return 'En panne';
            case 'warning': return 'Attention nécessaire';
            case 'working': return 'En fonctionnement';
            case 'expired': return 'Service expiré';
            case 'available': return 'Disponible';
            default: return 'État inconnu';
        }
    }

    /**
     * Détermine l'affectation de l'équipement
     */
    private function determinerAffectation($equip) {
        if (!$equip['user_role'] || !$equip['id_user']) {
            return 'Non assigné';
        }
        
        $userInfo = $this->equipementModel->getUserInfoForEquipment($equip['user_role'], $equip['id_user']);
        if (!$userInfo) {
            return 'Utilisateur inconnu';
        }
        
        $userName = $userInfo['nom'] . ' ' . $userInfo['prenom'];
        $department = $userInfo['service_nom'] ?? 'Non spécifié';
        $location = $userInfo['salle_nom'] ? $userInfo['salle_nom'] . ' (' . $userInfo['salle_numero'] . ')' : 'Non spécifié';
        
        return "$userName - $department ($location)";
    }

    /**
     * Utilitaires
     */
    private function getFieldLabel($field) {
        $labels = [
            'serialNumber' => 'numéro de série',
            'marque' => 'marque',
            'modele' => 'modèle',
            'type' => 'type',
            'user_role' => 'rôle utilisateur',
            'user_id' => 'utilisateur'
        ];
        return $labels[$field] ?? $field;
    }
    
    protected function checkRole($allowedRoles) {
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            $_SESSION['error_message'] = 'Accès non autorisé.';
            $this->redirect('dashboard');
        }
    }
}
?>