<!-- /app/views/layouts/navbar.php -->
<style>
/* Styles spécifiques pour la navbar et les notifications */
:root {
    --light: #f8fafc;
    --light-secondary: #f1f5f9;
    --primary: #3b82f6;
    --primary-light: #60a5fa;
    --primary-dark: #2563eb;
    --secondary: #64748b;
    --accent: #8b5cf6;
    --accent-light: #a78bfa;
    --dark: #1e293b;
    --dark-light: #334155;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --white: #ffffff;
    --border: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar {
    width: 100%;
    max-width: 100%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 60px; /* Réduit de 70px à 60px */
    padding: 0 14px; /* Réduit de 16px à 14px */
    position: relative;
}

.navbar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px; /* Réduit de 4px à 3px */
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
}

.logo {
    color: #1e293b;
    font-weight: 700;
    font-size: 1.5rem; /* Réduit de 1.75rem à 1.5rem */
    display: flex;
    align-items: center;
    height: 100%;
}

.logo span {
    color: #3b82f6;
}

.nav-content {
    display: flex;
    align-items: center;
    gap: 18px; /* Réduit de 24px à 18px */
    height: 100%;
    position: relative;
}

.user-welcome {
    display: flex;
    align-items: center;
    gap: 10px; /* Réduit de 12px à 10px */
    color: #1e293b;
    font-weight: 500;
}

.user-avatar {
    width: 42px; /* Réduit de 50px à 42px */
    height: 42px; /* Réduit de 50px à 42px */
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem; /* Réduit de 1.2rem à 1rem */
    border: 2px solid rgba(255, 255, 255, 0.5); /* Réduit de 3px à 2px */
    box-shadow: var(--shadow);
    cursor: pointer;
    transition: var(--transition);
}

.user-avatar:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-lg);
}

.nav-icons {
    display: flex;
    gap: 18px; /* Réduit de 24px à 18px */
    align-items: center;
}

.nav-icon {
    color: #64748b;
    cursor: pointer;
    position: relative;
    padding: 8px; /* Réduit de 10px à 8px */
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1.1rem; /* Réduit de 1.2rem à 1.1rem */
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px; /* Réduit de 40px à 36px */
    height: 36px; /* Réduit de 40px à 36px */
}

.nav-icon:hover {
    color: #3b82f6;
    background: #f8fafc;
    transform: scale(1.1);
}

.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border-radius: 50%;
    width: 16px; /* Réduit de 18px à 16px */
    height: 16px; /* Réduit de 18px à 16px */
    font-size: 9px; /* Réduit de 10px à 9px */
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 1.5px solid #ffffff; /* Réduit de 2px à 1.5px */
    animation: pulse 2s infinite;
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 28px; /* Ajusté pour correspondre à la nouvelle taille */
    width: 380px; /* Réduit de 420px à 380px */
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    z-index: 999;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-radius: 0 0 12px 12px;
    display: none;
}

.notification-dropdown.active {
    max-height: 450px; /* Réduit de 500px à 450px */
    display: block;
}

.notification-navbar {
    width: 100%;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px; /* Réduit de 18px 20px à 14px 16px */
    background: #3b82f6;
    color: #ffffff;
}

.notification-header h2 {
    font-weight: 600;
    font-size: 1.1rem; /* Réduit de 1.25rem à 1.1rem */
}

.notification-count {
    color: #ffffff;
    font-size: 1.3rem; /* Réduit de 1.5rem à 1.3rem */
}

.unread-nonlues {
    color: #ffffff;
    font-size: 0.85rem; /* Réduit de 0.9rem à 0.85rem */
    font-weight: 400;
}

.notification-list {
    max-height: 280px; /* Réduit de 320px à 280px */
    overflow-y: auto;
    flex: 1;
}

.notification-item {
    padding: 14px 16px; /* Réduit de 16px 20px à 14px 16px */
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: flex-start;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.notification-item:hover {
    background: #f1f5f9;
}

.notification-item.unread {
    background: rgba(59, 130, 246, 0.05);
}

.notification-icon {
    margin-right: 12px; /* Réduit de 14px à 12px */
    width: 36px; /* Réduit de 40px à 36px */
    height: 36px; /* Réduit de 40px à 36px */
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.1rem; /* Réduit de 1.2rem à 1.1rem */
}

.notification-info {
    flex: 1;
}

.notification-message {
    font-weight: 500;
    margin-bottom: 4px;
    line-height: 1.4;
    font-size: 0.9rem; /* Ajouté pour réduire légèrement */
}

.notification-time {
    font-size: 0.7rem; /* Réduit de 0.75rem à 0.7rem */
    color: #64748b;
    margin-bottom: 6px; /* Réduit de 8px à 6px */
}

.notification-link {
    font-size: 0.75rem; /* Réduit de 0.8rem à 0.75rem */
    color: #3b82f6;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-link:hover {
    color: #2563eb;
    text-decoration: underline;
}

.notification-link i {
    margin-left: 4px;
    font-size: 0.65rem; /* Réduit de 0.7rem à 0.65rem */
}

.notification-actions {
    padding: 14px 16px; /* Réduit de 16px 20px à 14px 16px */
    display: flex;
    justify-content: center;
    border-top: 1px solid #e2e8f0;
}

.btn {
    padding: 7px 14px; /* Réduit de 8px 16px à 7px 14px */
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    font-size: 0.9rem; /* Ajouté pour réduire */
}

.btn-primary {
    background: #3b82f6;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2563eb;
}

.empty-state {
    padding: 30px 16px; /* Réduit de 40px 20px à 30px 16px */
    text-align: center;
    color: #64748b;
}

.empty-state i {
    font-size: 2.5rem; /* Réduit de 3rem à 2.5rem */
    margin-bottom: 12px; /* Réduit de 16px à 12px */
    color: #e2e8f0;
}

.icon-success {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.icon-warning {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}

.icon-info {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
}

.icon-danger {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.icon-system {
    background: rgba(139, 92, 246, 0.15);
    color: #8b5cf6;
}

.icon-material {
    background: rgba(100, 116, 139, 0.15);
    color: #64748b;
}

.icon-validation {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.icon-maintenance {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}

.icon-assignment {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
}

.icon-update {
    background: rgba(139, 92, 246, 0.15);
    color: #8b5cf6;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #c5c5c5;
    border-radius: 10px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.notification-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: center;
}

.notification-item.fade-out {
    opacity: 0;
    transform: translateX(-20px);
    height: 0;
    padding: 0;
    margin: 0;
    border: none;
    overflow: hidden;
}

.notification-badge {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-actions {
    transition: all 0.5s ease;
}

/* Styles pour le profil flottant */
.profile-card {
    position: absolute;
    top: calc(100% + 8px); /* Réduit de 10px à 8px */
    right: calc(28px - 8px); /* Ajusté pour la nouvelle taille */
    width: 320px; /* Réduit de 360px à 320px */
    background: linear-gradient(145deg, var(--accent-light), var(--primary-light));
    border-radius: 18px; /* Réduit de 20px à 18px */
    padding: 20px 16px; /* Réduit de 25px 20px à 20px 16px */
    box-shadow: var(--shadow-lg);
    z-index: 2000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
}

.profile-card.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.profile-card-header {
    display: flex;
    align-items: center;
    gap: 14px; /* Réduit de 16px à 14px */
    margin-bottom: 16px; /* Réduit de 20px à 16px */
    position: relative;
    z-index: 2;
}

.avatar-card-container {
    width: 75px; /* Réduit de 90px à 75px */
    height: 75px; /* Réduit de 90px à 75px */
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.8rem; /* Réduit de 2.2rem à 1.8rem */
    border: 3px solid rgba(255, 255, 255, 0.5); /* Réduit de 4px à 3px */
    box-shadow: var(--shadow);
}

.user-card-info {
    flex: 1;
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 6px; /* Réduit de 8px à 6px */
    width: 80%;
}

.user-card-name {
    color: var(--secondary);
    font-size: 0.9rem; /* Réduit de 1rem à 0.9rem */
    font-weight: 500;
    background: var(--light);
    padding: 5px 14px; /* Réduit de 6px 16px à 5px 14px */
    border-radius: 18px; /* Réduit de 20px à 18px */
    display: block;
    border: 1px solid var(--border);
}

.user-card-role {
    color: var(--secondary);
    font-size: 0.85rem; /* Réduit de 0.95rem à 0.85rem */
    font-weight: 500;
    background: var(--light);
    padding: 4px 12px; /* Réduit de 6px 16px à 4px 12px */
    border-radius: 18px; /* Réduit de 20px à 18px */
    display: block;
    border: 1px solid var(--border);
}

.menu-card-items {
    display: flex;
    flex-direction: column;
    gap: 10px; /* Réduit de 12px à 10px */
    position: relative;
    z-index: 2;
}

.menu-card-item {
    background: var(--white);
    border: none;
    padding: 12px 16px; /* Réduit de 14px 18px à 12px 16px */
    border-radius: 14px; /* Réduit de 16px à 14px */
    display: flex;
    align-items: center;
    gap: 10px; /* Réduit de 12px à 10px */
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px; /* Réduit de 15px à 14px */
    font-weight: 500;
    color: var(--dark);
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.menu-card-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: var(--transition);
}

.menu-card-item:hover::before {
    left: 100%;
}

.menu-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    background: linear-gradient(135deg, var(--white) 0%, var(--light) 100%);
}

.menu-card-item:active {
    transform: translateY(0);
}

.menu-card-item i {
    width: 18px; /* Réduit de 20px à 18px */
    height: 18px; /* Réduit de 20px à 18px */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--light);
    color: var(--primary);
    font-size: 18px; /* Réduit de 20px à 18px */
    transition: var(--transition);
}

/* Animation de pulse pour le badge */
@keyframes pulse-gentle {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.notification-badge.pulse {
    animation: pulse-gentle 2s infinite;
}

/* Animation de confirmation */
@keyframes checkmark {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}

.checkmark-animation {
    animation: checkmark 0.5s ease-out;
}

@media (max-width: 768px) {
    .navbar {
        padding: 0 12px; /* Réduit de 16px à 12px */
    }
    
    .notification-dropdown {
        right: 12px; /* Ajusté */
        width: calc(100vw - 24px); /* Ajusté */
        max-width: 360px; /* Réduit de 400px à 360px */
    }
    
    .profile-card {
        width: 280px; /* Réduit de 320px à 280px */
        right: 0;
    }
}

@media (max-width: 480px) {
    .navbar {
        padding: 0 10px; /* Réduit de 12px à 10px */
    }
    
    .notification-dropdown {
        right: 0;
        width: 100vw;
        border-radius: 0;
        max-width: 100%;
    }
    
    .notification-header {
        padding: 12px 14px; /* Réduit de 14px 16px à 12px 14px */
    }
    
    .notification-header h2 {
        font-size: 1rem; /* Réduit de 1.1rem à 1rem */
    }
    
    .notification-count {
        font-size: 1.1rem; /* Réduit de 1.2rem à 1.1rem */
    }
    
    .notification-item {
        padding: 10px 14px; /* Réduit de 12px 16px à 10px 14px */
        flex-direction: column;
    }
    
    .notification-icon {
        margin-right: 0;
        margin-bottom: 8px; /* Réduit de 10px à 8px */
        width: 32px; /* Réduit de 35px à 32px */
        height: 32px; /* Réduit de 35px à 32px */
        font-size: 0.9rem; /* Réduit de 1rem à 0.9rem */
    }
    
    .notification-message {
        font-size: 0.85rem; /* Réduit de 0.9rem à 0.85rem */
    }
    
    .notification-time {
        font-size: 0.65rem; /* Réduit de 0.7rem à 0.65rem */
    }
    
    .notification-actions {
        padding: 10px 14px; /* Réduit de 12px 16px à 10px 14px */
    }
    
    .btn {
        padding: 8px 16px; /* Réduit de 10px 20px à 8px 16px */
        width: 100%;
    }
    
    .nav-icons {
        gap: 14px; /* Réduit de 16px à 14px */
    }
    
    .nav-icon {
        width: 32px; /* Réduit de 36px à 32px */
        height: 32px; /* Réduit de 36px à 32px */
        font-size: 1rem; /* Réduit de 1.1rem à 1rem */
    }
    
    .logo {
        font-size: 1.3rem; /* Réduit de 1.5rem à 1.3rem */
    }
    
    .profile-card {
        width: 250px; /* Réduit de 280px à 250px */
        right: -12px; /* Ajusté */
    }
    
    .avatar-card-container {
        width: 60px; /* Réduit de 70px à 60px */
        height: 60px; /* Réduit de 70px à 60px */
        font-size: 1.5rem; /* Réduit de 1.8rem à 1.5rem */
    }
    
    .user-card-name {
        font-size: 13px; /* Réduit de 14px à 13px */
        padding: 6px 12px; /* Réduit de 8px 14px à 6px 12px */
    }
    
    .user-card-role {
        font-size: 11px; /* Réduit de 12px à 11px */
        padding: 4px 10px; /* Réduit de 5px 12px à 4px 10px */
    }
    
    .menu-card-item {
        padding: 10px 14px; /* Réduit de 12px 16px à 10px 14px */
        font-size: 13px; /* Réduit de 14px à 13px */
    }
}
</style>

<div class="navbar">
    <div class="logo">IT<span>PARCS</span></div>
    
    <div class="nav-content">
        <div class="nav-icons">
            <div class="nav-icon" id="notification-icon">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                <span class="notification-badge" id="notification-badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="user-welcome">
            <!-- Ajout de l'ID user-avatar-btn pour le bouton -->
            <button class="user-avatar" id="user-avatar-btn">
                <?= substr($_SESSION['prenom'] ?? 'U', 0, 1) . substr($_SESSION['nom'] ?? 'S', 0, 1) ?>
            </button>
        </div>
    </div>

    <!-- Carte de profil - positionnée à l'intérieur de nav-content pour un positionnement correct -->
    <div class="profile-card" id="profile-card">
        <div class="profile-card-header">
            <div class="avatar-card-container">
                <?= substr($_SESSION['prenom'] ?? 'U', 0, 1) . substr($_SESSION['nom'] ?? 'S', 0, 1) ?>
            </div>
            <div class="user-card-info">
                <div class="user-card-name" id="user-fullname"><?= ($_SESSION['prenom'] ?? 'Utilisateur') . ' ' . ($_SESSION['nom'] ?? 'Système') ?></div>
                <div class="user-card-role" id="user-role"><?= $_SESSION['role'] ?? 'Utilisateur' ?></div>
            </div>
        </div>

        <div class="menu-card-items">
            <a href="<?= BASE_URL ?? '#' ?>profil/informations" class="menu-card-item">
                <i class="fas fa-user"></i> 
                <span>Informations personnelles</span>
            </a>          
            <a href="<?= BASE_URL ?? '#' ?>profil/mot-de-passe" class="menu-card-item">
                <i class="fas fa-key"></i> 
                <span>Changer mot de passe</span>
            </a>
            <a href="<?= BASE_URL ?? '#' ?>logout" class="menu-card-item">
                <i class="fas fa-sign-out-alt"></i> 
                <span>Déconnexion</span>
            </a>
        </div>
    </div>
    
    <!-- Conteneur de notifications intégré à la navbar -->
    <div class="notification-dropdown" id="notification-dropdown">
        <div class="notification-navbar">
            <div class="notification-header">
                <h2>Notifications</h2>
                <?php if ($unreadCount > 0): ?>
                <div class="notification-count" id="notification-count"><?= $unreadCount ?> <span class="unread-nonlues">non lues</span></div>
                <?php else: ?>
                <div class="notification-count" id="notification-count">0 <span class="unread-nonlues">non lues</span></div>
                <?php endif; ?>
            </div>
            
            <div class="notification-list">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): 
                        $iconInfo = $notificationModel->getNotificationIcon($notification['type']);
                    ?>
                    <div class="notification-item unread" data-id="<?= $notification['id'] ?>">
                        <div class="notification-icon <?= $iconInfo['class'] ?>">
                            <i class="<?= $iconInfo['icon'] ?>"></i>
                        </div>
                        <div class="notification-info">
                            <div class="notification-message"><?= htmlspecialchars($notification['message']) ?></div>
                            <div class="notification-time">
                                <?php 
                                if ($notification['hours_ago'] < 24) {
                                    echo "Il y a " . $notification['hours_ago'] . " heure(s)";
                                } else {
                                    echo $notification['formatted_time'];
                                }
                                ?>
                            </div>
                            <a href="<?= BASE_URL . $notification['lien'] ?>" class="notification-link">
                                Voir les détails <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>Aucune notification</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($notifications) > 0): ?>
                <div class="notification-actions">
                    <button type="button" class="btn btn-primary" id="mark-all-read-btn">
                        Marquer tout comme lu
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Le script JavaScript reste identique car il n'a pas besoin d'être modifié
document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.getElementById('notification-icon');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationItems = document.querySelectorAll('.notification-item');
    const userAvatarBtn = document.getElementById('user-avatar-btn');
    const profileCard = document.getElementById('profile-card');
    const userRoleElement = document.getElementById('user-role');
    const userFullnameElement = document.getElementById('user-fullname');
    
    // Fonction pour traduire les rôles
    function translateUserRole(role) {
        const roles = {
            'admin': 'Administrateur',
            'agent': 'Agent',
            'support': 'Support',
            'technicien': 'Technicien',
            'utilisateur': 'Utilisateur'
        };
        return roles[role.toLowerCase()] || role;
    }
    
    // Mettre à jour l'interface avec les données de session
    if (userRoleElement) {
        userRoleElement.textContent = translateUserRole(userRoleElement.textContent);
    }
    
    // Ouvrir/fermer la carte de profil
    if (userAvatarBtn && profileCard) {
        userAvatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileCard.classList.toggle('active');
            
            // Fermer les notifications si ouvertes
            if (notificationDropdown.classList.contains('active')) {
                notificationDropdown.classList.remove('active');
            }
        });
    }
    
    // Fermer la carte en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (profileCard && !profileCard.contains(e.target) && e.target !== userAvatarBtn) {
            profileCard.classList.remove('active');
        }
    });
    
    // Empêcher la fermeture en cliquant à l'intérieur de la carte
    if (profileCard) {
        profileCard.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Gestion de l'ouverture/fermeture du dropdown de notifications
    if (notificationIcon && notificationDropdown) {
        notificationIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            
            // Fermer le profil si ouvert
            if (profileCard && profileCard.classList.contains('active')) {
                profileCard.classList.remove('active');
            }
        });
    }
    
    // Fermer le dropdown si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && e.target !== notificationIcon && !notificationIcon.contains(e.target)) {
            notificationDropdown.classList.remove('active');
        }
    });
    
    // Gestion du bouton "Marquer tout comme lu"
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            // Désactiver le bouton pendant le traitement
            markAllReadBtn.disabled = true;
            markAllReadBtn.textContent = 'Traitement...';
            
            // Appel AJAX pour marquer toutes les notifications comme lues
            fetch('<?= BASE_URL ?>notifications/marquer-toutes-lues', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animation de disparition des notifications
                    notificationItems.forEach((item, index) => {
                        // Délai progressif pour l'animation
                        setTimeout(() => {
                            item.style.transition = 'all 0.5s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(-20px)';
                            
                            // Supprimer l'élément après l'animation
                            setTimeout(() => {
                                item.remove();
                            }, 500);
                        }, index * 100); // Délai progressif
                    });
                    
                    // Mettre à jour le compteur de notifications
                    document.querySelector('.notification-count').textContent = '0';
                    document.querySelector('.notification-badge').textContent = '0';
                    document.querySelector('.unread-nonlues').textContent = 'non lues';
                    
                    // Mettre à jour le badge dans la navbar
                    const notificationBadge = document.querySelector('#notification-icon .notification-badge');
                    if (notificationBadge) {
                        notificationBadge.style.transition = 'all 0.5s ease';
                        notificationBadge.style.opacity = '0';
                        notificationBadge.style.transform = 'scale(0)';
                        
                        setTimeout(() => {
                            notificationBadge.remove();
                        }, 500);
                    }
                    
                    // Afficher l'état vide après la suppression de toutes les notifications
                    setTimeout(() => {
                        const notificationList = document.querySelector('.notification-list');
                        if (notificationList.children.length === 0) {
                            const emptyState = document.createElement('div');
                            emptyState.className = 'empty-state';
                            emptyState.innerHTML = `
                                <i class="fas fa-bell-slash"></i>
                                <p>Aucune notification</p>
                            `;
                            notificationList.appendChild(emptyState);
                            
                            // Cacher le bouton "Marquer tout comme lu"
                            const notificationActions = document.querySelector('.notification-actions');
                            if (notificationActions) {
                                notificationActions.style.transition = 'all 0.5s ease';
                                notificationActions.style.opacity = '0';
                                notificationActions.style.height = '0';
                                notificationActions.style.padding = '0';
                                
                                setTimeout(() => {
                                    notificationActions.remove();
                                }, 500);
                            }
                        }
                    }, notificationItems.length * 100 + 500);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver le bouton en cas d'erreur
                markAllReadBtn.disabled = false;
                markAllReadBtn.textContent = 'Marquer tout comme lu';
            })
            .finally(() => {
                // Réactiver le bouton après 2 secondes même en cas de succès
                setTimeout(() => {
                    if (markAllReadBtn) {
                        markAllReadBtn.disabled = false;
                        markAllReadBtn.textContent = 'Marquer tout comme lu';
                    }
                }, 2000);
            });
        });
    }
    
    // Gestion des clics sur les notifications pour les marquer comme lues
    const notificationLinks = document.querySelectorAll('.notification-link');
    notificationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Trouver l'élément parent notification
            const notificationItem = this.closest('.notification-item');
            const notificationId = notificationItem.dataset.id;
            
            // Animation de marquage comme lu
            notificationItem.style.transition = 'all 0.3s ease';
            notificationItem.style.backgroundColor = '#f8fafc';
            
            // Marquer comme lu via AJAX
            fetch('<?= BASE_URL ?>notifications/marquer-lue/' + notificationId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            // Marquer visuellement comme lu
            notificationItem.classList.remove('unread');
            
            // Mettre à jour le compteur
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            document.querySelector('.notification-count').textContent = unreadCount;
            
            // Mettre à jour le badge dans la navbar
            const notificationBadge = document.querySelector('#notification-icon .notification-badge');
            if (notificationBadge) {
                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    // Animation de réduction du badge
                    notificationBadge.style.transition = 'all 0.3s ease';
                    notificationBadge.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        notificationBadge.style.transform = 'scale(1)';
                    }, 300);
                } else {
                    // Animation de disparition du badge
                    notificationBadge.style.transition = 'all 0.5s ease';
                    notificationBadge.style.opacity = '0';
                    notificationBadge.style.transform = 'scale(0)';
                    setTimeout(() => {
                        notificationBadge.remove();
                    }, 500);
                }
            }
        });
    });
});
</script>
