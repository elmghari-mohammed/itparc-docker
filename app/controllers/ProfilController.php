<?php
// /app/controllers/ProfilController.php

require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/AuthController.php');
require_once(__DIR__ . '/../models/Profil.php');   

class ProfilController extends Controller {
    private $profilModel;
    
    public function __construct() {
        parent::__construct();
        $this->profilModel = new Profil();
        $this->checkRole(['admin', 'support', 'technicien', 'agent']);
    }
    
    /**
     * Affiche la page de modification des informations personnelles
     */
    public function informations() {
        $role = $_SESSION['role'];
        $id = $_SESSION['id'];
        
        // Récupérer les informations de l'utilisateur
        $sql = "SELECT * FROM $role WHERE matricul = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer les messages de session s'ils existent
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        $form_data = $_SESSION['form_data'] ?? [];
        
        // Nettoyer les messages de session après les avoir récupérés
        unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_data']);
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processInformationsUpdate();
            
            if ($result['success']) {
                // Stocker le message de succès en session
                $_SESSION['success_message'] = $result['message'];
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('profil/informations');
            } else {
                // Stocker les données du formulaire et l'erreur en session
                $_SESSION['error_message'] = $result['message'];
                $_SESSION['form_data'] = $_POST;
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('profil/informations');
            }
            return;
        }
        
        // Afficher le formulaire avec les données de session ou les données utilisateur
        $form_data = !empty($form_data) ? $form_data : $user;
        
        $this->main('profil/informations', [
            'user' => $user,
            'success_message' => $success_message,
            'error_message' => $error_message,
            'form_data' => $form_data,
            'page_title' => 'Modifier les informations personnelles',
            'additional_css' => ['profil/informations.css'],
            'additional_js' => ['profil/informations.js']
        ]);
    }

    /**
     * Traite la mise à jour des informations personnelles
     */
    private function processInformationsUpdate() {
        $role = $_SESSION['role'];
        $id = $_SESSION['id'];
        
        $nom = $this->getPostData('nom');
        $prenom = $this->getPostData('prenom');
        $telephone = $this->getPostData('telephone');
        
        // Validation des champs obligatoires
        if (empty($nom) || empty($prenom)) {
            return ['success' => false, 'message' => "Le nom et le prénom sont obligatoires"];
        }
        
        // Validation du numéro de téléphone si fourni
        if (!empty($telephone) && !$this->profilModel->validatePhoneNumber($telephone)) {
            return ['success' => false, 'message' => "Le format du numéro de téléphone est invalide"];
        }
        
        // Préparer les données à mettre à jour
        $data = [
            'nom' => $nom,
            'prenom' => $prenom
        ];
        
        // Ajouter le téléphone uniquement pour les rôles qui l'ont
        if ($role !== 'agent' && !empty($telephone)) {
            $data['numero'] = $telephone;
        }
        
        // Mise à jour des informations
        if ($this->profilModel->updateInformations($role, $id, $data)) {
            return [
                'success' => true, 
                'message' => "Informations mises à jour avec succès"
            ];
        } else {
            return ['success' => false, 'message' => "Une erreur s'est produite lors de la mise à jour"];
        }
    }
    
    /**
     * Affiche et traite la page de modification du mot de passe
     */
    public function motDePasse() {
        $role = $_SESSION['role'];
        $id = $_SESSION['id'];
        
        // Récupérer les messages de session s'ils existent
        $success_message = $_SESSION['success_message'] ?? '';
        $error_message = $_SESSION['error_message'] ?? '';
        $form_data = $_SESSION['form_data'] ?? [];
        
        // Nettoyer les messages de session après les avoir récupérés
        unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_data']);
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processPasswordChange();
            
            if ($result['success']) {
                // Stocker le message de succès en session
                $_SESSION['success_message'] = $result['message'];
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('profil/mot-de-passe');
            } else {
                // Stocker les données du formulaire et l'erreur en session
                $_SESSION['error_message'] = $result['message'];
                $_SESSION['form_data'] = $_POST;
                // Rediriger pour éviter la resoumission du formulaire
                $this->redirect('profil/mot-de-passe');
            }
            return;
        }
        
        // Afficher le formulaire avec les données de session
        $this->main('profil/motDePasse', [
            'success_message' => $success_message,
            'error_message' => $error_message,
            'form_data' => $form_data,
            'page_title' => 'Changer le mot de passe',
            'additional_css' => ['profil/motDePasse.css'],
            'additional_js' => ['profil/motDePasse.js']
        ]);
    }
    
    /**
     * Traite le changement de mot de passe
     */
    private function processPasswordChange() {
        $role = $_SESSION['role'];
        $id = $_SESSION['id'];
        
        $currentPassword = $this->getPostData('current_password');
        $newPassword = $this->getPostData('new_password');
        $confirmPassword = $this->getPostData('confirm_password');
        
        // Validation des champs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['success' => false, 'message' => "Tous les champs sont obligatoires"];
        }
        
        // Vérification de la correspondance des nouveaux mots de passe
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => "Les nouveaux mots de passe ne correspondent pas"];
        }
        
        // Validation de la complexité du mot de passe
        if (!$this->profilModel->validatePasswordStrength($newPassword)) {
            return ['success' => false, 'message' => "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre"];
        }
        
        // Vérification du mot de passe actuel
        if (!$this->profilModel->verifyCurrentPassword($role, $id, $currentPassword)) {
            return ['success' => false, 'message' => "Le mot de passe actuel est incorrect"];
        }
        
        // Mise à jour du mot de passe
        if ($this->profilModel->updatePassword($role, $id, $newPassword)) {
            return [
                'success' => true, 
                'message' => "Mot de passe modifié avec succès. Vous allez être déconnecté dans 3 secondes."
            ];

        } else {
            return ['success' => false, 'message' => "Une erreur s'est produite lors de la modification du mot de passe"];
        }
    }
}
?>