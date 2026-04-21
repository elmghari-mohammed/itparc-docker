<?php
// /app/views/layouts/main.php

header('Content-Type: text/html; charset=utf-8');

// Configuration de la page
$page_title = $page_title ?? 'Tableau de bord';
$additional_css = $additional_css ?? [];
$additional_js = $additional_js ?? [];
$currentPage = $currentPage ?? 'accueil';

// Logique de notification (inchangée pour ne pas casser le PHP)
$notificationData = ['notifications' => [], 'unreadCount' => 0];
$reclamationLibreCount = 0; 

if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    require_once(__DIR__ . '/../../controllers/NotificationController.php');
    $notificationController = new NotificationController();
    $notificationData = $notificationController->getNotificationsForNavbar();
    require_once(__DIR__ . '/../../models/Notification.php');
    
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'support') {
        require_once(__DIR__ . '/../../models/Reclamation.php');
        $reclamationModel = new Reclamation();
        $reclamationLibreCount = $reclamationModel->countReclamationsLibres();
    }
}

$notifications = $notificationData['notifications'];
$unreadCount = $notificationData['unreadCount'];
$notificationModel = new Notification(); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <script src="/public/js/utf8-fix.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/navbar.css">
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="/public/css/dashboard.css">
    
    <?php foreach ($additional_css as $css): ?>
        <link rel="stylesheet" href="/public/css/<?= $css ?>">
    <?php endforeach; ?>
</head>
<body data-current-page="<?= $currentPage ?>">

    <?php include(__DIR__ . '/notification.php'); ?>
    <?php include(__DIR__ . '/navbar.php'); ?>

    <div class="main-content">
        <?php include(__DIR__ . '/sidebar.php'); ?>
        
        <div class="sidebar-toggle-circle" id="sidebarToggleCircle">
            <i class="fas fa-chevron-right"></i>
        </div>

        <div class="dashboard">
            <?php include($viewPath); ?>
        </div>
    </div>

    <?php include(__DIR__ . '/confirmation.php'); ?>

    <script src="/public/js/sidebar.js"></script>
    <script src="/public/js/dashboard.js"></script>
    
    <?php foreach ($additional_js as $js): ?>
        <script src="/public/js/<?= $js ?>"></script>
    <?php endforeach; ?>
</body>
</html>