<?php
// /app/controllers/NotificationController.php
require_once(__DIR__ . '/../../core/Controller.php');
require_once(__DIR__ . '/../models/Notification.php');

class NotificationController extends Controller {
    private $notificationModel;
    
    public function __construct() {
        parent::__construct();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Récupère les notifications pour l'affichage dans la navbar
     * Cette méthode est appelée depuis le layout principal
     */
    public function getNotificationsForNavbar() {
        if (!$this->isAuthenticated()) {
            return [
                'notifications' => [],
                'unreadCount' => 0
            ];
        }
        
        $userId = $_SESSION['id'];
        $userRole = $_SESSION['role'];
        
        $notifications = $this->notificationModel->getUnreadNotifications($userId, $userRole);
        $unreadCount = $this->notificationModel->getUnreadCount($userId, $userRole);
        
        return [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ];
    }
    
    /**
     * Marque une notification comme lue (appelé via AJAX ou formulaire)
     */
    public function markAsRead($notificationId) {
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }
        
        $userId = $_SESSION['id'];
        $userRole = $_SESSION['role'];
        
        $result = $this->notificationModel->markAsRead($notificationId, $userId, $userRole);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }
    
    /**
     * Marque toutes les notifications comme lues
     */
    public function markAllAsRead() {
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }
        
        $userId = $_SESSION['id'];
        $userRole = $_SESSION['role'];
        
        try {
            $result = $this->notificationModel->markAllAsRead($userId, $userRole);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
            exit;
        }
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>