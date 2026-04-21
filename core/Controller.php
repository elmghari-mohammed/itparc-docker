<?php
// /core/Controller.php

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../app/models/Database.php');
require_once(__DIR__ . '/../app/helpers/RouteHelper.php');

class Controller {
    protected $db;
    protected $currentPage;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->currentPage = RouterHelper::getCurrentPage();
        $this->startSessionIfNeeded();
        
        if ($this->currentPage === null) {
            $this->notFound();
        }
    }
    
    protected function main($view, $data = []) {
        // Ajouter les données communes
        $data['currentPage'] = $this->currentPage;
        $data['session'] = $_SESSION ?? [];
        $data['baseUrl'] = RouterHelper::getBasePath();
        
        // Extraire les variables
        extract($data);
        
        // Inclure directement le layout avec la vue intégrée
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vue non trouvée: " . $view);
        }
        
        // Inclure le layout principal qui contiendra la vue
        include(__DIR__ . '/../app/views/layouts/main.php');
    }
    
    protected function redirect($url) {
        header('Location: ' . RouterHelper::getFullUrl($url));
        exit;
    }
    
    protected function isAuthenticated() {
        return isset($_SESSION['role']) && isset($_SESSION['id']);
    }
    
    protected function checkRole($allowedRoles) {
        if (!$this->isAuthenticated() || !in_array($_SESSION['role'], $allowedRoles)) {
            $this->redirect('login');
        }
    }
    
    protected function startSessionIfNeeded() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }
    
    protected function notFound() {
        http_response_code(404);
        echo "404 Not Found - Page non trouvée";
        exit;
    }
    
    protected function getPostData($field, $default = '') {
        return isset($_POST[$field]) ? trim(htmlspecialchars($_POST[$field])) : $default;
    }

    
}
?>