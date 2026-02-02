<?php
// /app/views/layouts/main.php

header('Content-Type: text/html; charset=utf-8');

// Configuration de la page
$page_title = $page_title ?? 'Tableau de bord';
$additional_css = $additional_css ?? [];
$additional_js = $additional_js ?? [];
$currentPage = $currentPage ?? 'accueil';

// Récupérer les notifications pour l'utilisateur connecté
$notificationData = ['notifications' => [], 'unreadCount' => 0];
$reclamationLibreCount = 0; // Initialiser le compteur

if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    require_once(__DIR__ . '/../../controllers/NotificationController.php');
    $notificationController = new NotificationController();
    $notificationData = $notificationController->getNotificationsForNavbar();
    
    // Créer une instance du modèle pour utiliser dans la navbar
    require_once(__DIR__ . '/../../models/Notification.php');
    
    // Récupérer le nombre de réclamations libres (uniquement pour admin et support)
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'support') {
        require_once(__DIR__ . '/../../models/Reclamation.php');
        $reclamationModel = new Reclamation();
        $reclamationLibreCount = $reclamationModel->countReclamationsLibres();
    }
}

// Passer les données de notification à la navbar
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
    
    <!--  CORRECTION UTF-8 - CHARGER EN PREMIER  -->
    <script src="<?= BASE_URL ?>public/js/utf8-fix.js"></script>
    
    <!-- CSS de base -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/sidebar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/dashboard.css">
    
    <!-- CSS additionnels spécifiques à la page -->
    <?php foreach ($additional_css as $css): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>public/css/<?= $css ?>">
    <?php endforeach; ?>
</head>
<body data-current-page="<?= $currentPage ?>">
    <!-- Notifications -->
    <?php include(__DIR__ . '/notification.php'); ?>
    <!-- Navbar -->
    <?php include(__DIR__ . '/navbar.php'); ?>

    
    <div class="main-content">
        <!-- Sidebar -->
        <?php include(__DIR__ . '/sidebar.php'); ?>
        
        <!-- Sidebar Toggle Circle -->
        <div class="sidebar-toggle-circle" id="sidebarToggleCircle">
            <i class="fas fa-chevron-right"></i>
        </div>


        
        <!-- Contenu principal -->
        <div class="dashboard">
            <?php include($viewPath); ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <?php include(__DIR__ . '/confirmation.php'); ?>

    <!-- Scripts de base -->
    <script src="<?= BASE_URL ?>public/js/sidebar.js"></script>
    <script src="<?= BASE_URL ?>public/js/dashboard.js"></script>
    
    <!-- Scripts additionnels spécifiques à la page -->
    <?php foreach ($additional_js as $js): ?>
        <script src="<?= BASE_URL ?>public/js/<?= $js ?>"></script>
    <?php endforeach; ?>
</body>
</html>
