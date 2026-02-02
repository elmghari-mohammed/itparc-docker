<?php
// sidebar.php amélioré avec navigation et gestion des rôles

// Vérification d'authentification
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$currentPage = $currentPage ?? 'accueil';
$role = $_SESSION['role'];
$reclamationLibreCount = $reclamationLibreCount ?? 0; // Utiliser la variable passée depuis main.php
?>

<div class="sidebar" id="sidebar">
    <div class="menu-section">
        <a href="<?= BASE_URL ?>dashboard" class="menu-item <?= ($currentPage == 'accueil') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> 
            <span>Accueil</span>
        </a>
        
        <!-- Options communes à tous les rôles -->
        <div class="menu-title">
            <i class="fas fa-desktop"></i>
            <span>Mes Équipements</span>
        </div>
        <a href="<?= BASE_URL ?>equipements/liste" class="menu-item submenu <?= ($currentPage == 'equipements-liste') ? 'active' : '' ?>">
            <i class="fas fa-list"></i> 
            <span>Liste des équipements</span>
        </a>
        <?php if ($role === 'admin'): ?>
        <a href="<?= BASE_URL ?>equipements/gerer" class="menu-item submenu <?= ($currentPage == 'equipements-gerer') ? 'active' : '' ?>">
            <i class="fas fa-server"></i> 
            <span>Gerer Les Équipements</span>
        </a>
        <a href="<?= BASE_URL ?>equipements/ajouter" class="menu-item submenu <?= ($currentPage == 'equipements-ajouter') ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> 
            <span>Ajouter Un Équipement</span>
        </a>
        <?php endif; ?>
        
        <!-- Réclamations -->
        <div class="menu-title">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Mes Réclamations</span>
        </div>
        <a href="<?= BASE_URL ?>reclamations/nouvelle" class="menu-item submenu <?= ($currentPage == 'nouvelle-reclamation') ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> 
            <span>Nouvelle réclamation</span>
        </a>
        <a href="<?= BASE_URL ?>reclamations/suivi" class="menu-item submenu <?= ($currentPage == 'statut-reclamations') ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> 
            <span>Suivi réclamations</span>
        </a>
        
        <?php if ($role === 'support'): ?>
        <a href="<?= BASE_URL ?>reclamations/traiter" class="menu-item submenu <?= ($currentPage == 'reclamations-traiter') ? 'active' : '' ?>">
            <i class="fas fa-bullseye"></i> 
            <span>Traiter Les Réclamations</span>
            <?php if ($reclamationLibreCount > 0): ?>
            <span class="badge"><?= $reclamationLibreCount ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <a href="<?= BASE_URL ?>reclamations/superviser" class="menu-item submenu <?= ($currentPage == 'reclamations-superviser') ? 'active' : '' ?>">
            <i class="fas fa-exclamation-circle"></i> 
            <span>Gestion Réclamations</span>
            <?php if ($reclamationLibreCount > 0): ?>
            <span class="badge"><?= $reclamationLibreCount ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
        
        <!--option admin -->
        <?php if ($role === 'admin'): ?>
        <div class="menu-title">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
        </div>
        <a href="<?= BASE_URL ?>utilisateurs/gerer" class="menu-item <?= ($currentPage == 'utilisateurs-gerer') ? 'active' : '' ?>">
            <i class="fas fa-user-cog"></i> 
            <span>Gérer les utilisateurs</span>
        </a>
        <a href="<?= BASE_URL ?>utilisateurs/ajouter" class="menu-item <?= ($currentPage == 'utilisateurs-ajouter') ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> 
            <span>Ajoute un utilisateur</span>
        </a>

        <div class="menu-title">
            <i class="fas fa-server "></i>
            <span>Infrastructure</span>
        </div>
        <a href="<?= BASE_URL ?>infrastructure" class="menu-item <?= ($currentPage == 'infrastructure-gerer') ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i> 
            <span>gerer l'Infrastructure</span>
        </a>
        <?php endif; ?>
    </div>
</div>