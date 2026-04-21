<?php
// notification.php : Contient l'UI et le JS du système de notifications réutilisable
?>

<!-- Container pour les notifications PHP -->
<div class="notification-container" id="notificationContainer">
    <?php if (!empty($success_message)): ?>
        <div class="notification success show">
            <div class="notification-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Succès</div>
                <div class="notification-message"><?= htmlspecialchars($success_message) ?></div>
            </div>
            <button class="notification-close" onclick="this.parentElement.classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
            <div class="notification-progress" style="width: 100%;"></div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="notification error show">
            <div class="notification-icon">
                <i class="fas fa-times"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Erreur</div>
                <div class="notification-message"><?= htmlspecialchars($error_message) ?></div>
            </div>
            <button class="notification-close" onclick="this.parentElement.classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
            <div class="notification-progress" style="width: 100%;"></div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Styles notification, NE PAS MODIFIER */
.notification-container {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
}
.notification {
    min-width: 330px;
    max-width: 370px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(60,72,88,.10);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 16px 18px 12px 14px;
    position: relative;
    font-family: inherit;
    opacity: 0;
    transform: translateY(-24px) scale(.98);
    transition: all 0.3s cubic-bezier(.4,0,.2,1);
    pointer-events: auto;
}
.notification.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}
.notification.success { border-left: 6px solid #10b981; }
.notification.error   { border-left: 6px solid #ef4444; }
.notification.warning { border-left: 6px solid #f59e0b; }
.notification.info    { border-left: 6px solid #3b82f6; }
.notification-icon {
    font-size: 22px;
    margin-top: 3px;
    color: #fff;
    background: #10b981;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.notification.error .notification-icon { background: #ef4444; }
.notification.warning .notification-icon { background: #f59e0b; }
.notification.info .notification-icon { background: #3b82f6; }
.notification-content {
    flex: 1;
    margin-left: 6px;
}
.notification-title {
    font-weight: 600;
    font-size: 1.08rem;
    margin-bottom: 2px;
}
.notification-message {
    font-size: 0.96rem;
    color: #334155;
}
.notification-close {
    background: none;
    border: none;
    outline: none;
    color: #94a3b8;
    font-size: 1.1rem;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 12px;
}
.notification-progress {
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 100%;
    background: #e0e7ef;
    border-radius: 0 0 12px 12px;
    overflow: hidden;
}
.notification.success .notification-progress { background: #10b981; }
.notification.error .notification-progress { background: #ef4444; }
.notification.warning .notification-progress { background: #f59e0b; }
.notification.info .notification-progress { background: #3b82f6; }
@media (max-width:550px) {
    .notification-container { right: 7px; top: 7px; }
    .notification { min-width: 96vw; max-width: 99vw;}
}
</style>

<script>
// Système de notifications JS réutilisable
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notificationContainer');
        this.notifications = [];
    }
    show(type, title, message, duration = 5000) {
        const notification = this.createNotification(type, title, message, duration);
        this.container.appendChild(notification);
        this.notifications.push(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        if (duration > 0) {
            this.startAutoRemove(notification, duration);
        }
        return notification;
    }
    createNotification(type, title, message, duration) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        const iconMap = {
            success: 'fas fa-check',
            error: 'fas fa-times',
            warning: 'fas fa-exclamation',
            info: 'fas fa-info'
        };
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="${iconMap[type] || 'fas fa-info'}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
            ${duration > 0 ? '<div class="notification-progress" style="width: 100%;"></div>' : ''}
        `;
        notification.querySelector('.notification-close').onclick = () => {
            this.remove(notification);
        };
        return notification;
    }
    startAutoRemove(notification, duration) {
        const progress = notification.querySelector('.notification-progress');
        if (progress) {
            let timeLeft = duration;
            const interval = 100;
            const timer = setInterval(() => {
                timeLeft -= interval;
                const percentage = (timeLeft / duration) * 100;
                progress.style.width = `${percentage}%`;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    this.remove(notification);
                }
            }, interval);
            notification.onmouseenter = () => clearInterval(timer);
            notification.onmouseleave = () => {
                if (timeLeft > 0) {
                    this.startAutoRemove(notification, timeLeft);
                }
            };
        }
    }
    remove(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
                this.notifications = this.notifications.filter(n => n !== notification);
            }
        }, 300);
    }
    success(title, message, duration) { return this.show('success', title, message, duration);}
    error(title, message, duration)   { return this.show('error', title, message, duration);}
    warning(title, message, duration) { return this.show('warning', title, message, duration);}
    info(title, message, duration)    { return this.show('info', title, message, duration);}
}

// Instancie la notification globale (à utiliser dans chaque page)
window.notify = window.notify || new NotificationSystem();

// Fermer automatiquement les notifications PHP après 5 secondes
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.notification.show');
    notifications.forEach(notification => {
        const progress = notification.querySelector('.notification-progress');
        if (progress) {
            let width = 100;
            const interval = 50;
            const totalTime = 5000;
            const decrement = (interval / totalTime) * 100;
            const timer = setInterval(() => {
                width -= decrement;
                progress.style.width = width + '%';
                if (width <= 0) {
                    clearInterval(timer);
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }, interval);
            notification.addEventListener('mouseenter', () => clearInterval(timer));
            notification.addEventListener('mouseleave', () => {
                const remainingTime = (width / 100) * totalTime;
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, remainingTime);
            });
        }
    });
});
</script>