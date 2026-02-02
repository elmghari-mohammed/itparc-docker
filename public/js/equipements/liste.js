// /public/js/equipements/liste.js

// Fonction pour afficher les détails
function showDetails(equipmentId) {
    // Vérifier si les données sont déjà disponibles
    if (window.equipmentData && window.equipmentData[equipmentId]) {
        displayEquipmentDetails(window.equipmentData[equipmentId]);
        return;
    }
    
    // Sinon, charger les détails via AJAX
    fetch(`/itparc/equipements/details/${equipmentId}`)
        .then(response => {
            if (!response.ok) throw new Error('Équipement non trouvé');
            return response.json();
        })
        .then(data => {
            displayEquipmentDetails(data);
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
        });
}

// Fonction pour afficher l'interface de détail
function displayEquipmentDetails(equipment) {
    const overlay = document.getElementById('detailsOverlay');
    if (!overlay) return;
    
    // Créer le contenu HTML
    overlay.innerHTML = `
        <div class="details-container">
            <div class="equipment-header">
                <div class="header-content">
                    <button class="back-btn" onclick="closeDetails()">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </button>
                    <div class="equipment-main-info">
                        <div class="equipment-icon-large">
                            <i class="fas fa-${equipment.icon}"></i>
                        </div>
                        <div class="equipment-title-info">
                            <h2>${equipment.title}</h2>
                            <div class="equipment-subtitle">${equipment.subtitle}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-content">
                <div class="details-grid">
                    <!-- Informations générales -->
                    <div class="detail-section">
                        <div class="section-title">
                            <div class="section-icon info">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            Informations Générales
                        </div>
                        ${createDetailItem('Marque', equipment.details.brand, 'tag')}
                        ${createDetailItem('Modèle', equipment.details.model, equipment.icon)}
                        ${createDetailItem('Type', equipment.details.type, 'cogs')}
                    </div>

                    <!-- Dates importantes -->
                    <div class="detail-section">
                        <div class="section-title">
                            <div class="section-icon calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            Dates Importantes
                        </div>
                        ${createDetailItem('Début d\'utilisation', equipment.details.startDate, 'play-circle', true)}
                        ${createDetailItem('Fin de garantie', equipment.details.warrantyEnd, 'shield-alt')}
                        ${createDetailItem('Temps d\'utilisation', equipment.details.usageTime, 'clock')}
                    </div>

                    <!-- Informations utilisateur -->
                    <div class="detail-section">
                        <div class="section-title">
                            <div class="section-icon user">
                                <i class="fas fa-user"></i>
                            </div>
                            Statut de l'Équipement
                        </div>
                        ${createDetailItem('État actuel', equipment.details.currentState, 'info-circle', true)}
                        ${createDetailItem('Dernière vérification', equipment.details.lastCheck, 'calendar-day')}
                    </div>

                    <!-- Informations techniques -->
                    <div class="detail-section">
                        <div class="section-title">
                            <div class="section-icon location">
                                <i class="fas fa-cog"></i>
                            </div>
                            Informations Techniques
                        </div>
                        ${createDetailItem('Code équipement', equipment.details.code, 'barcode')}
                        ${createDetailItem('Catégorie', equipment.details.category, 'tag')}
                        ${createDetailItem('Priorité', equipment.details.priority, 'star')}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Afficher l'overlay avec une transition
    overlay.classList.add('active');
    
    // Empêcher le défilement de la page principale
    document.body.style.overflow = 'hidden';
}

// Fonction utilitaire pour créer un élément de détail
function createDetailItem(label, value, icon, highlight = false) {
    return `
        <div class="detail-item">
            <span class="detail-label">
                <i class="fas fa-${icon}"></i>
                ${label}
            </span>
            <span class="detail-value ${highlight ? 'highlight' : ''}">${value}</span>
        </div>
    `;
}

// Fonction pour fermer l'interface de détail
function closeDetails() {
    const overlay = document.getElementById('detailsOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.innerHTML = '';
        
        // Rétablir le défilement de la page principale
        document.body.style.overflow = '';
    }
}

// Fermer avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetails();
    }
});