<?php
// /app/controllers/ReclamationsController.php

require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/../models/Reclamation.php');
require_once(__DIR__ . '/../models/Materiel.php');
require_once(__DIR__ . '/../models/Type.php');
require_once(__DIR__ . '/../../core/Model.php');

class ReclamationsController extends Controller {
    private $reclamationModel;
    private $typeModel;

    public function __construct() {
        parent::__construct();
        $this->reclamationModel = new Reclamation();
        $this->typeModel = new Type();
        
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

    /**
     * Affiche le formulaire de nouvelle réclamation - POUR TOUS LES UTILISATEURS
     */
    public function nouvelle() {
        // Récupérer les matériels de l'utilisateur (admin, support, agent)
        $userId = $_SESSION['id'];
        $userRole = $_SESSION['role'];
        $materiels = $this->reclamationModel->getMaterielsUtilisateur($userId, $userRole);

        // Messages de session
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        $form_data = $_SESSION['form_data'] ?? [];

        // Nettoyer la session
        unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_data']);

        // Traitement POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processNouvelleReclamation();

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
                $this->redirect('reclamations/suivi');
                exit;
            } else {
                $_SESSION['error_message'] = $result['message'];
                $_SESSION['form_data'] = $_POST;
                $this->redirect('reclamations/nouvelle');
                exit;
            }
        }
           
        $this->main('reclamations/nouvelle', [
            'materiels' => $materiels,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'form_data' => $form_data,
            'page_title' => 'Nouvelle Réclamation'
        ]);
    }

    /**
     * Traite la création d'une nouvelle réclamation
     */
    private function processNouvelleReclamation() {
        error_log("POST data: " . print_r($_POST, true));
        $errors = [];
        
        // Champs obligatoires
        $requiredFields = ['materiel_id', 'motif'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Le champ " . ucfirst($field) . " est obligatoire.";
            }
        }
        
        // Vérifier que le matériel appartient bien à l'utilisateur
        $userId = $_SESSION['id'];
        $userRole = $_SESSION['role'];
        $materielId = $_POST['materiel_id'];
        $userMateriels = $this->reclamationModel->getMaterielsUtilisateur($userId, $userRole);
        $materielValide = false;
        
        foreach ($userMateriels as $materiel) {
            if ($materiel['id'] == $materielId) {
                $materielValide = true;
                break;
            }
        }
        
        if (!$materielValide) {
            $errors[] = "Le matériel sélectionné n'est pas valide.";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors)];
        }
        
        // Préparer les données
        $data = [
            'materiel_id' => $materielId,
            'motif' => $_POST['motif'],
            'user_role' => $userRole,
            'id_user' => $userId,
            'statut' => 'en_attente',
            'date_reclamation' => date('Y-m-d H:i:s'),
            'date_resolution' => null,
            'support_matricul' => null,
            'motif_support' => null,
            'type' => $_POST['type'] ?? null
        ];
        
        // Insérer la réclamation
        $success = $this->reclamationModel->createReclamation($data);
        
        if ($success) {
            return ['success' => true, 'message' => 'Réclamation créée avec succès!'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la création de la réclamation.'];
        }
    }

    /**
     * Affiche et traite les réclamations pour le support
     */
    public function traiter() {
        // Vérifier que l'utilisateur est un support
        if ($_SESSION['role'] !== 'support') {
            $_SESSION['error_message'] = 'Accès réservé au support';
            $this->redirect('dashboard');
            return;
        }
        
        $supportId = $_SESSION['id'];
        
        // Récupérer les réclamations pour le support (assignées + libres)
        $reclamations = $this->reclamationModel->getReclamationsSupport($supportId);
        
        // Récupérer les statuts disponibles
        $statuts = $this->reclamationModel->getStatuts();

        // Récupérer les types d'équipements dynamiques
        $typesEquipement = $this->typeModel->getTypesForFilters();
        
        // Traitement du formulaire de mise à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_status'])) {
                // Mise à jour du statut
                $result = $this->processUpdateStatus();
            } elseif (isset($_POST['action']) && $_POST['action'] === 'prendre') {
                // Prise d'une réclamation libre
                $result = $this->processPrendreReclamation();
            }
            
            if (isset($result)) {
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = $result['message'];
                }
                
                // Rediriger pour éviter la resoumission
                $this->redirect('reclamations/traiter');
                return;
            }
        }
        
        // Récupérer les messages de session
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        
        // Nettoyer les messages
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->main('reclamations/traiter', [
            'reclamations' => $reclamations,
            'statuts' => $statuts,
            'typesEquipement' => $typesEquipement,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'page_title' => 'Traiter les Réclamations'
        ]);
    }

    /**
     * Traite la prise d'une réclamation libre par le support
     */
    private function processPrendreReclamation() {
        $reclamationId = $this->getPostData('reclamation_id');
        $supportId = $_SESSION['id'];
        
        if (empty($reclamationId)) {
            return ['success' => false, 'message' => 'ID de réclamation manquant.'];
        }
        
        // Vérifier que la réclamation est libre (sans support)
        $reclamationDetails = $this->reclamationModel->getReclamationDetails($reclamationId);
        
        if (!$reclamationDetails) {
            return ['success' => false, 'message' => 'Réclamation non trouvée.'];
        }
        
        if ($reclamationDetails['support_matricul'] !== null) {
            return ['success' => false, 'message' => 'Cette réclamation est déjà assignée.'];
        }
        
        if ($reclamationDetails['statut'] !== 'en_attente' ) {
            return ['success' => false, 'message' => 'Seules les réclamations en attente peuvent être prises.'];
        }
        
        // Prendre la réclamation
        $success = $this->reclamationModel->prendreReclamation($reclamationId, $supportId);
        
        if ($success) {
            // Créer une notification pour l'utilisateur concerné
            $this->createNotification(
                $reclamationDetails['user_role'],
                $reclamationDetails['id_user'],
                'Réclamation prise en charge',
                "Votre réclamation #{$reclamationId} a été prise en charge par le support.",
                'reclamation',
                $reclamationId,
                $_SESSION['role'],
                $_SESSION['id']
            );
            
            return ['success' => true, 'message' => 'Réclamation prise en charge avec succès!'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la prise en charge.'];
        }
    }

    /**
     * Traite la mise à jour du statut d'une réclamation par le support
     */
    private function processUpdateStatus() {
        $errors = [];
        
        // Validation des champs
        $requiredFields = ['reclamation_id', 'statut'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Le champ " . ucfirst($field) . " est obligatoire.";
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors)];
        }
        
        $reclamationId = $this->getPostData('reclamation_id');
        $statut = $this->getPostData('statut');
        $motifSupport = $this->getPostData('motif_support', '');
        $type = $this->getPostData('type', '');
        $supportId = $_SESSION['id'];
        
        // Vérifier que la réclamation est accessible par le support
        $reclamationDetails = $this->reclamationModel->getReclamationPourTraitement($reclamationId, $supportId);
        
        if (!$reclamationDetails) {
            return ['success' => false, 'message' => 'Réclamation non trouvée ou non accessible.'];
        }
        
        // Si la réclamation est libre et le support veut la prendre, l'assigner d'abord
        if ($reclamationDetails['support_matricul'] === null && $statut === 'en_cours') {
            $this->reclamationModel->prendreReclamation($reclamationId, $supportId);
        }
        
        // Mettre à jour le statut avec le type
        $success = $this->reclamationModel->updateStatut($reclamationId, $statut, $motifSupport, $supportId, $type);
        
        if ($success) {
            // Créer une notification pour l'utilisateur concerné
            $this->createNotification(
                $reclamationDetails['user_role'],
                $reclamationDetails['id_user'],
                'Statut de réclamation mis à jour',
                "Votre réclamation #{$reclamationId} a été marquée comme '{$statut}'.",
                'reclamation',
                $reclamationId,
                $_SESSION['role'],
                $_SESSION['id']
            );
            
            return ['success' => true, 'message' => 'Réclamation mise à jour avec succès!'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    /**
     * Affiche le suivi des réclamations (historique + suivi)
     */
 public function suivi() {
    // Récupérer l'ID de l'utilisateur connecté depuis la session
    $userId = $_SESSION['id'] ?? null;
    $userRole = $_SESSION['role'] ?? null;
    
    if (!$userId) {
        $_SESSION['error_message'] = 'Utilisateur non identifié';
        $this->redirect('login');
        return;
    }

    // Récupérer les réclamations de l'utilisateur
    $reclamations = $this->reclamationModel->getMesReclamations($userId);
    
    // Récupérer les statuts disponibles (clés uniquement pour le filtre)
    $statuts = $this->reclamationModel->getStatuts();
    
    // Formater les données pour la vue (garder l'affichage en libellé)
    $formattedReclamations = [];
    foreach ($reclamations as $reclamation) {
        $formattedReclamations[] = [
            'id' => $reclamation['id'],
            'materiel_info' => $reclamation['materiel_info'],
            'date_reclamation' => $reclamation['date_reclamation'],
            'statut' => $reclamation['statut'], // Garder la clé pour le filtre
            'statut_libelle' => $this->reclamationModel->getLibelleStatut($reclamation['statut']), // Libellé pour l'affichage
            'motif' => $reclamation['motif'],
            'motif_support' => $reclamation['motif_support'] ?? null
        ];
    }

    // Récupérer les messages de session s'ils existent
    $success_message = $_SESSION['success_message'] ?? '';
    $error_message = $_SESSION['error_message'] ?? '';
    
    // Nettoyer les messages de session après les avoir récupérés
    unset($_SESSION['success_message'], $_SESSION['error_message']);
    
    $this->main('reclamations/suivi', [
        'reclamations' => $formattedReclamations,
        'statuts' => $statuts, // Passer les statuts pour les filtres
        'success_message' => $success_message,
        'error_message' => $error_message,
        'page_title' => 'Suivi des Réclamations'
    ]);
}


    public function assignees() {
        // Vérifier que l'utilisateur est admin
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error_message'] = 'Accès réservé aux administrateurs';
            $this->redirect('dashboard');
            return;
        }
        
        // Récupérer les réclamations en attente
        $reclamations = $this->reclamationModel->getReclamationsLibre();
        
        // Récupérer les supports disponibles
        $supports = $this->reclamationModel->getAllSupports();
        
        // Traitement de l'assignation si formulaire soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assigner'])) {
            $reclamationId = $this->getPostData('reclamation_id');
            $supportId = $this->getPostData('support_id');
            
            if (!empty($reclamationId) && !empty($supportId)) {
                $result = $this->reclamationModel->assignerReclamation(
                    $reclamationId, 
                    $supportId,
                    $_SESSION['id'] // ID de l'admin qui assigne
                );
                
                if ($result) {
                    // Créer une notification pour le support
                    $this->createNotification(
                        'support',
                        $supportId,
                        'Nouvelle réclamation assignée',
                        'Une nouvelle réclamation vous a été assignée.',
                        'reclamation',
                        $reclamationId,
                        $_SESSION['role'],
                        $_SESSION['id']
                    );
                    
                    // Message de succès
                    $_SESSION['success_message'] = "Réclamation assignée avec succès!";
                    $this->redirect('reclamations/assignees');
                } else {
                    $_SESSION['error_message'] = "Erreur lors de l'assignation de la réclamation.";
                }
            } else {
                $_SESSION['error_message'] = "Veuillez sélectionner un support.";
            }
        }
        
        // Récupérer les messages de session
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        
        // Nettoyer les messages de session
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        // Afficher la vue
        $this->main('reclamations/assignees', [
            'reclamations' => $reclamations,
            'supports' => $supports,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'page_title' => 'Réclamations à Assigner'
        ]);
    }
    
    /**
     * Méthode utilitaire pour créer des notifications
     */
    private function createNotification($userRole, $userId, $sujet, $message, $type, $typeId, $creatorRole, $creatorId) {
        $notificationModel = new Model('notification');
        $notificationId = uniqid('notif_', true);
        
        $data = [
            'id' => $notificationId,
            'user_role' => $userRole,
            'user_id' => $userId,
            'sujet' => $sujet,
            'message' => $message,
            'lien' => '',
            'type' => $type,
            'type_id' => $typeId,
            'creator_role' => $creatorRole,
            'creator_id' => $creatorId,
            'est_lue' => 0
        ];
        
        return $notificationModel->create($data);
    }
    /**     suprvise */
    // Ajouter cette méthode dans la classe ReclamationsController

/**
 * Affiche et traite la supervision des réclamations pour l'admin
 */
public function superviser() {
    // Vérifier que l'utilisateur est admin
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = 'Accès réservé aux administrateurs';
        $this->redirect('dashboard');
        return;
    }
    
    // Récupérer toutes les réclamations pour la supervision AVEC le nom du support
    $reclamations = $this->reclamationModel->getReclamationsPourSupervision();
    
    // Récupérer les supports disponibles
    $supports = $this->reclamationModel->getSupportsPourAssignation();
    
    // Récupérer les statuts disponibles
    $statuts = $this->reclamationModel->getStatuts();
    
    // Récupérer les types d'équipements
    $typesEquipement = $this->typeModel->getTypesForFilters();
    
    // Messages de session
    $success_message = $_SESSION['success_message'] ?? '';
    $error_message = $_SESSION['error_message'] ?? '';
    
    // Nettoyer la session
    unset($_SESSION['success_message'], $_SESSION['error_message']);
    
    // Traitement POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = $this->processSupervisionReclamation();
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
        
        $this->redirect('reclamations/superviser');
        return;
    }
    
    $this->main('reclamations/superviser', [
        'reclamations' => $reclamations,
        'supports' => $supports,
        'statuts' => $statuts,
        'typesEquipement' => $typesEquipement,
        'success_message' => $success_message,
        'error_message' => $error_message,
        'page_title' => 'Supervision des Réclamations'
    ]);
}

/**
 * Traite la mise à jour d'une réclamation par l'admin
 */
private function processSupervisionReclamation() {
    $errors = [];
    
    // Validation des champs obligatoires
    $requiredFields = ['reclamation_id', 'statut', 'support_id'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ " . ucfirst(str_replace('_', ' ', $field)) . " est obligatoire.";
        }
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }
    
    $reclamationId = $this->getPostData('reclamation_id');
    $statut = $this->getPostData('statut');
    $supportId = $this->getPostData('support_id');
    $type = $this->getPostData('type', '');
    $notesAdmin = $this->getPostData('notes_admin', '');
    
    // Préparer les données de mise à jour
    $data = [
        'statut' => $statut,
        'type' => $type
    ];
    
    // Gérer l'assignation du support
    if ($supportId === '0') {
        // Aucun support (libre)
        $data['support_matricul'] = null;
    } else {
        // Assigner au support sélectionné
        $data['support_matricul'] = $supportId;
    }
    
    // Ajouter les notes admin si présentes
    if (!empty($notesAdmin)) {
        $data['motif_support'] = $notesAdmin;
    }
    
    // Mettre à jour la réclamation
    $success = $this->reclamationModel->updateReclamationSupervision($reclamationId, $data);
    
    if ($success) {
        // Créer une notification si le support a changé
        if ($supportId !== '0') {
            $reclamationDetails = $this->reclamationModel->getReclamationDetails($reclamationId);
            if ($reclamationDetails) {
                $this->createNotification(
                    'support',
                    $supportId,
                    'Réclamation assignée',
                    "Une réclamation vous a été assignée par l'administrateur.",
                    'reclamation',
                    $reclamationId,
                    $_SESSION['role'],
                    $_SESSION['id']
                );
            }
        }
        
        return ['success' => true, 'message' => 'Réclamation mise à jour avec succès!'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la réclamation.'];
    }
}


}
?>