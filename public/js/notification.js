// notification.js

class NotificationSystem {
    constructor(containerId = 'notificationContainer') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Notification container with ID "${containerId}" not found.`);
            return;
        }
        this.notifications = [];
    }

    show(type, title, message, duration = 5000) {
        const notification = this.createNotification(type, title, message, duration);
        this.container.appendChild(notification);
        this.notifications.push(notification);

        // Ajouter la classe 'show' avec un léger délai pour déclencher l'animation CSS
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Démarrer le compte à rebours pour la suppression automatique
        if (duration > 0) {
            this.startAutoRemove(notification, duration);
        }

        return notification;
    }

    createNotification(type, title, message, duration) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        // Définir l'icône en fonction du type
        const iconMap = {
            success: 'fas fa-check',
            error: 'fas fa-times',
            danger: 'fas fa-times',
            warning: 'fas fa-exclamation',
            info: 'fas fa-info'
        };

        const iconClass = iconMap[type] || 'fas fa-info';

        notification.innerHTML = `
            <div class="notification-icon">
                <i class="${iconClass}"></i>
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

        // Gérer la fermeture manuelle
        notification.querySelector('.notification-close').onclick = () => {
            this.remove(notification);
        };

        return notification;
    }

    startAutoRemove(notification, duration) {
        const progress = notification.querySelector('.notification-progress');
        if (!progress) return;

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

        // Mettre en pause le timer quand la souris est sur la notification
        notification.addEventListener('mouseenter', () => {
            clearInterval(timer);
        });

        // Reprendre/replanifier la suppression quand la souris quitte la notification
        notification.addEventListener('mouseleave', () => {
            if (timeLeft > 0) {
                setTimeout(() => {
                    this.remove(notification);
                }, timeLeft);
            }
        });
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

    // Méthodes pratiques pour chaque type de notification
    success(title, message, duration = 5000) {
        return this.show('success', title, message, duration);
    }

    error(title, message, duration = 5000) {
        return this.show('error', title, message, duration);
    }

    warning(title, message, duration = 5000) {
        return this.show('warning', title, message, duration);
    }

    info(title, message, duration = 5000) {
        return this.show('info', title, message, duration);
    }
}

// Initialisation globale
// Vous pouvez créer une instance globale pour une utilisation facile
// Ex: window.notify.success('Titre', 'Message');
document.addEventListener('DOMContentLoaded', function() {
    window.notify = new NotificationSystem('notificationContainer');

    // Optionnel : Fermer automatiquement les notifications PHP existantes
    const existingNotifications = document.querySelectorAll('.notification.show');
    existingNotifications.forEach(notification => {
        const progress = notification.querySelector('.notification-progress');
        if (progress && progress.style.width === '100%') {
            const duration = 5000; // 5 secondes, ajustez si nécessaire
            const width = 100;
            const interval = 50;
            const decrement = (interval / duration) * 100;
            const timer = setInterval(() => {
                let currentWidth = parseFloat(progress.style.width);
                currentWidth -= decrement;
                progress.style.width = currentWidth + '%';
                if (currentWidth <= 0) {
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
                const remainingTime = (parseFloat(progress.style.width) / 100) * duration;
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

