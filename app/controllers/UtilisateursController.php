<?php
// /app/controllers/UtilisateursController.php

require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/../models/Utilisateur.php');
require_once(__DIR__ . '/../helpers/AuthHelper.php');

class UtilisateursController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new Utilisateur();
    }
    
    public function gerer() {
        // Vérifier les permissions (seul l'admin peut gérer les utilisateurs)
        $this->checkRole(['admin']);
        
        $userModel = new Utilisateur();
        
        // Traitement des actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $user_id = $_POST['user_id'] ?? null;
            $user_role = $_POST['user_role'] ?? null;
            
            switch ($action) {
                case 'block':
                    $this->blockUser($userModel, $user_id, $user_role);
                    break;
                    
                case 'delete':
                    $this->deleteUser($userModel, $user_id, $user_role);
                    break;
                    
                case 'edit':
                    $this->editUser($userModel, $user_id, $user_role, $_POST);
                    break;
            }
            
            // Redirection pour éviter la resoumission du formulaire
            $this->redirect('utilisateurs/gerer');
        }
        
        // Récupérer les utilisateurs et services
        $users = $userModel->getAllUsers();
        $services = $userModel->getServices();

        // Récupérer l'ID de l'admin connecté
        $current_admin_id = $_SESSION['id'] ?? null;
        
        // Récupérer les messages de session
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        
        // Nettoyer les messages après les avoir récupérés
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        // Afficher la vue
        $this->main('utilisateurs/gerer', [
            'users' => $users,
            'services' => $services,
            'current_admin_id' => $current_admin_id,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'page_title' => 'Gérer les utilisateurs',
            'additional_css' => ['utilisateurs/gerer.css'],
            'additional_js' => ['utilisateurs/gerer.js']
        ]);
    }
    
    /**
     * Bloquer/débloquer un utilisateur
     */
    private function blockUser($userModel, $user_id, $user_role) {
        try {
            if (empty($user_id) || empty($user_role)) {
                throw new Exception("Données utilisateur manquantes");
            }

            // Vérification de sécurité : empêcher un admin de bloquer son propre compte
            $current_admin_id = $_SESSION['id'];
            
            // Conversion en string pour assurer une comparaison correcte
            if (strval($current_admin_id) === strval($user_id) && $user_role === 'Administrateur') {
                throw new Exception("Vous ne pouvez pas bloquer votre propre compte");
            }
            
            // Vérifier que l'utilisateur existe
            if (!$userModel->userExists($user_id, $user_role)) {
                throw new Exception("Utilisateur non trouvé");
            }
            
            // Récupérer les informations de l'utilisateur
            $userInfo = $userModel->getUserStatus($user_id, $user_role);
            if (!$userInfo) {
                throw new Exception("Impossible de récupérer les informations de l'utilisateur");
            }
            
            $currentStatus = $userInfo['bloque'];
            $userName = trim($userInfo['prenom']) . ' ' . trim($userInfo['nom']);
            
            $success = $userModel->toggleBlockStatus($user_id, $user_role);
            
            if ($success) {
                // Déterminer le message spécifique
                if ($currentStatus) {
                    $_SESSION['success_message'] = "$user_role $userName débloqué avec succès";
                } else {
                    $_SESSION['success_message'] = "$user_role $userName bloqué avec succès";
                }
            } else {
                throw new Exception("Erreur lors de la modification du statut");
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors du changement de statut : ' . $e->getMessage();
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    private function deleteUser($userModel, $user_id, $user_role) {
        try {

            error_log("Tentative de suppression - user_id: $user_id, user_role: $user_role");
            if (empty($user_id) || empty($user_role)) {
                throw new Exception("Données utilisateur manquantes");
            }

            // Vérification de sécurité : empêcher un admin de supprimer son propre compte
            $current_admin_id = $_SESSION['id'];
            
            // Conversion en string pour assurer une comparaison correcte
            if (strval($current_admin_id) === strval($user_id) && $user_role === 'Administrateur') {
                throw new Exception("Vous ne pouvez pas supprimer votre propre compte");
            }
            
            // Vérifier que l'utilisateur existe
            if (!$userModel->userExists($user_id, $user_role)) {
                throw new Exception("Utilisateur non trouvé");
            }
            $userInfo = $userModel->getUserStatus($user_id, $user_role);

            
            $success = $userModel->deleteUser($user_id, $user_role);
            
            if ($success) {
                $userName = trim($userInfo['prenom']) . ' ' . trim($userInfo['nom']);
                $_SESSION['success_message'] = "$user_role $userName supprimé avec succès";
            } else {
                throw new Exception("Erreur lors de la suppression");
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }
    }
    
    
    /**
     * Modifier un utilisateur
     */
/**
 * Modifier un utilisateur
 */
private function editUser($userModel, $user_id, $user_role, $data) {
    try {
        if (empty($user_id) || empty($user_role)) {
            throw new Exception("Données utilisateur manquantes");
        }
        
        // Validation des données
        $validationErrors = $this->validateUserData($data);
        
        if (!empty($validationErrors)) {
            $_SESSION['error_message'] = implode(', ', $validationErrors);
            return;
        }
        
        // Vérifier que l'utilisateur existe
        if (!$userModel->userExists($user_id, $user_role)) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        // Vérifier si l'email a changé et s'il est déjà utilisé
        $currentUserInfo = $userModel->getUserInfo($user_id, $user_role);
        $currentEmail = $currentUserInfo['email'] ?? '';
        $newEmail = trim($data['email']);
        
        if ($currentEmail !== $newEmail) {
            // Vérifier si le nouvel email est déjà utilisé par un autre utilisateur
            if ($userModel->emailExists($newEmail)) {
                throw new Exception("Cet email est déjà utilisé par un autre utilisateur");
            }
        }
        
        // Préparer les données pour la mise à jour
        $updateData = [
            'nom' => trim($data['nom']),
            'prenom' => trim($data['prenom']),
            'email' => $newEmail, // Ajout de l'email
            'role' => $user_role, // Le rôle ne change pas
            'service_id' => (int)$data['service_id']
        ];
        
        // Ajouter le mot de passe s'il est fourni
        if (!empty($data['password'])) {
            if ($data['password'] !== $data['password_confirm']) {
                throw new Exception("Les mots de passe ne correspondent pas");
            }
            
            if (strlen($data['password']) < 6) {
                throw new Exception("Le mot de passe doit contenir au moins 6 caractères");
            }
            
            $updateData['password'] = $data['password'];
        }
        
        // Mise à jour
        $success = $userModel->updateUser($user_id, $updateData);
        
        if ($success) {
            $userName = trim($data['prenom']) . ' ' . trim($data['nom']);
            $_SESSION['success_message'] = "Utilisateur $userName modifié avec succès";
        } else {
            throw new Exception("Erreur lors de la modification");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erreur lors de la modification : ' . $e->getMessage();
    }
}

/**
 * Valider les données utilisateur
 */
private function validateUserData($data) {
    $errors = [];
    
    if (empty(trim($data['nom']))) {
        $errors[] = "Le nom est requis";
    }
    
    if (empty(trim($data['prenom']))) {
        $errors[] = "Le prénom est requis";
    }
    
    if (empty(trim($data['email']))) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    if (empty($data['service_id']) || !is_numeric($data['service_id'])) {
        $errors[] = "Le service est requis";
    }
    
    // Validation du mot de passe si fourni
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        }
        
        if ($data['password'] !== ($data['password_confirm'] ?? '')) {
            $errors[] = "La confirmation du mot de passe ne correspond pas";
        }
    }
    
    return $errors;
}
    /**
     * Affiche et traite le formulaire d'ajout d'utilisateur
     */
    public function ajouter() {
        $this->checkRole(['admin']);
        
        // Récupérer les données pour les listes déroulantes
        $services = $this->userModel->getAllServices();
        $salles = $this->userModel->getAvailableSalles();
        
        // Récupérer les messages de session s'ils existent
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        $form_data = $_SESSION['form_data'] ?? [];
        
        // Nettoyer les messages de session après les avoir récupérés
        unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_data']);
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processAddUser();
            
            if ($result['success']) {
                // Stocker le message de succès en session
                $_SESSION['success_message'] = $result['message'];
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('utilisateurs/ajouter');
            } else {
                // Stocker les données du formulaire et l'erreur en session
                $_SESSION['error_message'] = $result['message'];
                $_SESSION['form_data'] = $_POST;
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('utilisateurs/ajouter');
            }
            return;
        }
        
        // Afficher le formulaire avec les données de session
        $this->main('utilisateurs/ajouter', [
            'services' => $services,
            'salles' => $salles,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'form_data' => $form_data,
            'page_title' => 'Ajouter Un Utilisateur',
            'additional_css' => ['utilisateurs/ajouter.css'],
            'additional_js' => ['utilisateurs/ajouter.js']
        ]);
    }
    
    /**
     * Traite l'ajout d'un utilisateur
     */
    private function processAddUser() {
        // Récupérer et valider les données du formulaire
        $userType = $this->getPostData('userType');
        $nom = $this->getPostData('nom');
        $prenom = $this->getPostData('prenom');
        $email = $this->getPostData('email');
        $password = $this->getPostData('password');
        $confirmPassword = $this->getPostData('confirmPassword');
        $numero = $this->getPostData('numero');
        $service_id = $this->getPostData('service_id');
        $salle_id = $this->getPostData('salle_id');
        
        // Validation des champs requis
        if (empty($userType) || empty($nom) || empty($email) || empty($password) || empty($service_id)) {
            return ['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.'];
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'L\'adresse email n\'est pas valide.'];
        }
        
        // Vérifier si l'email existe déjà
        if (AuthHelper::findUserByEmail($email) !== null) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé par un autre utilisateur.'];
        }
        
        // Validation du mot de passe
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères.'];
        }
        
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Les mots de passe ne correspondent pas.'];
        }
        
        // Validation du numéro de téléphone si fourni
        if (!empty($numero) && !preg_match('/^\+?[0-9]{10,15}$/', $numero)) {
            return ['success' => false, 'message' => 'Le numéro de téléphone n\'est pas valide.'];
        }
        
        // Préparer les données pour l'insertion
        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password,
            'numero' => $numero,
            'service_id' => $service_id,
            'salle_id' => $salle_id
        ];
        
        // Dans la méthode processAddUser(), mettre à jour la validation
        $allowedTypes = ['agent', 'support']; // Supprimer 'technicien'

        // Dans la méthode createUser(), adapter les champs spécifiques
        if ($userType === 'agent') {
            $userData['tache'] = $this->getPostData('tache');
            $userData['post'] = $this->getPostData('post');
        } elseif ($userType === 'support') {
            $userData['numero'] = $this->getPostData('numero');
        }
        
        try {
            // Créer l'utilisateur
            $result = $this->userModel->createUser($userType, $userData);
            
            if ($result) {
                return ['success' => true, 'message' => 'Utilisateur créé avec succès!'];
            } else {
                return ['success' => false, 'message' => 'Une erreur s\'est produite lors de la création de l\'utilisateur.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
}
?>