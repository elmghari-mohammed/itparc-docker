// Fonctions communes pour tous les dashboards
class Dashboard {
    constructor() {
        this.initAnimations();
        this.initNotifications();
    }
    
    initAnimations() {
        // Animation des cartes au chargement
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('loading');
        });
    }
    
    initNotifications() {
        // Gestion des notifications
        const notificationBadges = document.querySelectorAll('.notification-badge');
        notificationBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                console.log('Notification cliquée');
                // Logique pour afficher les notifications
            });
        });
    }
    
    updateStats(equipments, broken, requests, claims) {
        // Mise à jour des statistiques
        const stats = {
            equipments: equipments || 8,
            broken: broken || 2,
            requests: requests || 3,
            claims: claims || 1
        };
        
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`.summary-card.${key} h3`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    }
    
    addActivity(type, title, description, date) {
        const timeline = document.querySelector('.timeline');
        const newActivity = this.createTimelineItem(type, title, description, date);
        timeline.insertBefore(newActivity, timeline.firstChild);
    }
    
    createTimelineItem(type, title, description, date) {
        const item = document.createElement('div');
        item.className = 'timeline-item';
        
        const iconClass = {
            'request': 'fas fa-plus',
            'claim': 'fas fa-wrench',
            'notification': 'fas fa-check'
        };
        
        item.innerHTML = `
            <div class="timeline-icon ${type}">
                <i class="${iconClass[type]}"></i>
            </div>
            <div class="timeline-content">
                <h4>${title}</h4>
                <p>${description}</p>
            </div>
            <div class="timeline-date">${date}</div>
        `;
        
        return item;
    }
    
    showAlert(type, title, message) {
        const alertsSection = document.querySelector('.alerts-section');
        const newAlert = this.createAlert(type, title, message);
        
        const existingAlerts = alertsSection.querySelector('.alert-item');
        if (existingAlerts) {
            alertsSection.insertBefore(newAlert, existingAlerts);
        } else {
            alertsSection.appendChild(newAlert);
        }
        
        // Auto-remove après 5 secondes
        setTimeout(() => {
            newAlert.remove();
        }, 5000);
    }
    
    createAlert(type, title, message) {
        const alert = document.createElement('div');
        alert.className = `alert-item ${type}`;
        
        const iconClass = {
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-times-circle'
        };
        
        const iconColor = {
            'success': 'var(--success)',
            'warning': 'var(--warning)',
            'error': 'var(--danger)'
        };
        
        alert.innerHTML = `
            <div class="alert-icon">
                <i class="${iconClass[type]}" style="color: ${iconColor[type]};"></i>
            </div>
            <div class="alert-content">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
        `;
        
        return alert;
    }
}