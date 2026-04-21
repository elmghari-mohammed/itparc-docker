<?php
// /app/models/Notification.php
require_once(__DIR__ . '/../../core/Model.php');

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère les notifications non lues pour un utilisateur
     */
    public function getUnreadNotifications($userId, $userRole) {
        $sql = "SELECT n.*, 
                       DATE_FORMAT(n.date_creation, '%d %b, %H:%i') as formatted_time,
                       TIMESTAMPDIFF(HOUR, n.date_creation, NOW()) as hours_ago
                FROM notification n 
                WHERE n.user_id = :user_id 
                AND n.user_role = :user_role 
                AND n.est_lue = FALSE 
                ORDER BY n.date_creation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_role' => $userRole
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère le nombre de notifications non lues pour un utilisateur
     */
    public function getUnreadCount($userId, $userRole) {
        $sql = "SELECT COUNT(*) as count 
                FROM notification 
                WHERE user_id = :user_id 
                AND user_role = :user_role 
                AND est_lue = FALSE";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_role' => $userRole
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    /**
     * Marque une notification comme lue
     */
    public function markAsRead($notificationId, $userId, $userRole) {
        $sql = "UPDATE notification 
                SET est_lue = TRUE 
                WHERE id = :id 
                AND user_id = :user_id 
                AND user_role = :user_role";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $notificationId,
            'user_id' => $userId,
            'user_role' => $userRole
        ]);
    }
    
    /**
     * Marque toutes les notifications comme lues pour un utilisateur
     */
    public function markAllAsRead($userId, $userRole) {
        $sql = "UPDATE notification 
                SET est_lue = TRUE 
                WHERE user_id = :user_id 
                AND user_role = :user_role 
                AND est_lue = FALSE";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'user_role' => $userRole
        ]);
    }
    
    /**
     * Détermine l'icône et la classe CSS en fonction du type de notification
     */
    public function getNotificationIcon($type) {
        $icons = [
            'demande' => ['icon' => 'fas fa-file-alt', 'class' => 'icon-info'],
            'reclamation' => ['icon' => 'fas fa-exclamation-circle', 'class' => 'icon-warning'],
            'validation' => ['icon' => 'fas fa-check-circle', 'class' => 'icon-success'],
            'refus' => ['icon' => 'fas fa-times-circle', 'class' => 'icon-danger'],
            'materiel' => ['icon' => 'fas fa-laptop', 'class' => 'icon-material'],
            'maintenance' => ['icon' => 'fas fa-tools', 'class' => 'icon-maintenance'],
            'system' => ['icon' => 'fas fa-cog', 'class' => 'icon-system'],
            'default' => ['icon' => 'fas fa-bell', 'class' => 'icon-info']
        ];
        
        return $icons[$type] ?? $icons['default'];
    }
}
?>