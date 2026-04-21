<?php
// /app/views/equipements/gerer.php
?>
<style>
    :root {
        --compact-padding: 16px;
        --compact-margin: 12px;
    }

    .dashboard-title {
        color: var(--dark);
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--dark), var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 4px;
        letter-spacing: -0.2px;
    }

    .subtitle {
        color: var(--secondary);
        font-size: 0.875rem;
        font-weight: 400;
        margin: 0;
        line-height: 1.3;
    }

    .dashboard-title i {
        font-size: 1.5rem;
        margin-right: 8px;
    }

    .card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        padding: var(--compact-padding);
        margin-bottom: 16px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: var(--border-radius);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        border: none;
        gap: 6px;
        font-size: 14px;
        text-decoration: none;
    }

    .search-container {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .search-input, .filter-select {
        flex: 1;
        min-width: 150px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--border-radius);
        font-size: 14px;
        transition: var(--transition);
    }

    .search-input:focus, .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .table-container {
        overflow-x: auto;
        font-size: 14px;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-table th {
        background-color: var(--light-secondary);
        padding: 12px 10px;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }

    .user-table td {
        padding: 10px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .user-table tr:hover {
        background-color: var(--light);
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge-agent { background-color: rgba(59, 130, 246, 0.1); color: var(--primary); }
    .badge-technicien { background-color: rgba(139, 92, 246, 0.1); color: var(--accent); }
    .badge-support { background-color: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .badge-administrateur { background-color: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .badge-commun { background-color: rgba(16, 185, 129, 0.1); color: var(--success); }
    .badge-none { background-color: rgba(107, 114, 128, 0.1); color: var(--secondary); }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: var(--border-radius);
        background: transparent;
        border: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
        font-size: 13px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 16px;
        gap: 6px;
    }

    .pagination-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: var(--border-radius);
        background: var(--white);
        border: 1px solid var(--border);
        color: var(--dark);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 30px 0;
        color: var(--secondary);
        font-size: 14px;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 12px;
        color: var(--border);
    }

    /* Modal compact */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        width: 100%;
        max-width: 480px;
        margin: 15px;
        transform: translateY(15px) scale(0.98);
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
        transition: var(--transition);
    }

    .modal-overlay.active .modal {
        transform: translateY(0) scale(1);
    }

    .modal-header {
        padding: 16px 20px 0 20px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 16px;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-right: 30px;
    }

    .modal-subtitle {
        color: var(--secondary);
        font-size: 13px;
        margin-bottom: 12px;
    }

    .modal-body {
        padding: 0 20px;
    }

    .modal-footer {
        padding: 16px 20px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid var(--border);
        margin-top: 16px;
    }

    .close-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--secondary);
        cursor: pointer;
        transition: var(--transition);
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--border-radius);
    }

    /* Form compact */
    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 4px;
        font-size: 13px;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--border-radius);
        font-size: 14px;
        transition: var(--transition);
        background: var(--white);
    }

    .form-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .form-note {
        font-size: 11px;
        color: var(--secondary);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Status compact */
    .status-good { color: var(--success); font-weight: 500; font-size: 13px; }
    .status-warning { color: var(--warning); font-weight: 500; font-size: 13px; }
    .status-danger { color: var(--danger); font-weight: 500; font-size: 13px; }

    /* Notifications compact */
    .notification-container {
        position: fixed;
        top: 15px;
        right: 15px;
        z-index: 2000;
        max-width: 350px;
    }

    .notification {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        margin-bottom: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        position: relative;
        transform: translateX(100%);
        opacity: 0;
        transition: var(--transition);
        border-left: 3px solid;
        font-size: 13px;
    }

    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: var(--white);
        flex-shrink: 0;
    }

    .notification-close {
        position: absolute;
        top: 6px;
        right: 6px;
        background: none;
        border: none;
        color: var(--secondary);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        font-size: 12px;
    }

    /* Responsive optimisé */
    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 1.25rem;
        }

        .dashboard-title i {
            font-size: 1.25rem;
        }

        .subtitle {
            font-size: 0.8rem;
        }

        .search-container {
            flex-direction: column;
            gap: 8px;
        }

        .search-input, .filter-select {
            min-width: 100%;
        }

        .user-table {
            min-width: 900px;
            font-size: 13px;
        }

        .user-table th, .user-table td {
            padding: 8px 6px;
        }

        .modal {
            margin: 10px;
            max-height: 95vh;
        }

        .modal-footer {
            flex-direction: column;
        }

        .form-group-row {
            grid-template-columns: 1fr;
        }

        .notification-container {
            top: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .action-buttons {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .card {
            padding: 12px;
            margin-bottom: 12px;
        }

        .modal-header, .modal-body, .modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .modal-title {
            font-size: 1.1rem;
        }
    }
</style>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-laptop"></i>
            Gestion des Équipements
        </h1>
        <p class="subtitle">Consulter et gérer tous les équipements du système <span id="equipmentCount">(<strong><?php echo isset($materiels) && is_array($materiels) ? count($materiels) : 0; ?></strong> Équipements)</span></p>
    </div>

    <!-- Messages de succès/erreur -->
    <?php if (!empty($success_message)): ?>
        <div class="notification success show">
            <div class="notification-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Succès</div>
                <div class="notification-message"><?php echo htmlspecialchars($success_message); ?></div>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
            <div class="notification-progress" style="width: 100%;"></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="notification error show">
            <div class="notification-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Erreur</div>
                <div class="notification-message"><?php echo htmlspecialchars($error_message); ?></div>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
            <div class="notification-progress" style="width: 100%;"></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un équipement...">
            <select class="filter-select" id="filter-type">
                <option value="">Tous les types</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= htmlspecialchars($type['nom']) ?>"><?= htmlspecialchars($type['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" id="filter-service">
                <option value="">Tous les services</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= htmlspecialchars($service['nom']) ?>"><?= htmlspecialchars($service['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" id="filter-status">
                <option value="">Tous les statuts</option>
                <option value="fonctionnel">Fonctionnel</option>
                <option value="en_panne">En panne</option>
            </select>
            <button class="btn btn-outline" id="sortDateBtn" data-sort="desc">
                <i class="fas fa-sort-amount-down"></i>
                Date (récent)
            </button>
        </div>
        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Type</th>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Service</th>
                        <th>Salle</th>
                        <th>Date début</th>
                        <th>Fin garantie</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="equipmentTableBody">
                    <!-- Rempli par JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="pagination" id="paginationContainer">
            <!-- Pagination gérée par JS -->
        </div>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal-overlay" id="editEquipmentModal">
    <div class="modal">
        <button class="close-btn" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-edit"></i>
                Modifier l'équipement
            </h2>
            <p class="modal-subtitle">Modifiez les informations de l'équipement ci-dessous</p>
        </div>
        <div class="modal-body">
            <form id="editEquipmentForm" method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="equipement_id" id="editEquipmentId">
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label class="form-label required" for="equipmentSerial">Serial Number</label>
                        <input type="text" id="equipmentSerial" name="serial_number" class="form-input" required readonly>
                        <div class="form-note warning">
                            <i class="fas fa-info-circle"></i>
                            Le serial number ne peut pas être modifié
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="equipmentMarque">Marque</label>
                        <input type="text" id="equipmentMarque" name="marque" class="form-input" required>
                    </div>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label class="form-label required" for="equipmentModel">Modèle</label>
                        <input type="text" id="equipmentModel" name="model" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="equipmentType">Type</label>
                        <select id="equipmentType" name="type_id" class="form-select" required>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label class="form-label required" for="equipmentDateDebut">Date début</label>
                        <input type="date" id="equipmentDateDebut" name="date_debut" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="equipmentGarantie">Fin garantie</label>
                        <input type="date" id="equipmentGarantie" name="garantie_fin" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="equipmentDetail">Détails</label>
                    <textarea id="equipmentDetail" name="detail" class="form-input" rows="3" placeholder="Description détaillée de l'équipement..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="button" class="btn btn-primary" onclick="saveChanges()">
                <i class="fas fa-save"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal-overlay" id="confirmationModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirmation
            </h2>
        </div>
        <div class="modal-body">
            <div id="confirmationMessage"></div>
            <div id="confirmationDetails" class="user-preview" style="display: none;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelBtn">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="button" class="btn btn-danger" id="confirmBtn">
                <i class="fas fa-check"></i>
                Confirmer
            </button>
        </div>
    </div>
</div>

<script>
    // Données PHP injectées en JS - AVEC VÉRIFICATION
    let allEquipments = <?php echo isset($materiels) && is_array($materiels) ? json_encode($materiels) : '[]'; ?>;
    let services = <?php echo isset($services) && is_array($services) ? json_encode($services) : '[]'; ?>;
    let types = <?php echo isset($types) && is_array($types) ? json_encode($types) : '[]'; ?>;
    let filteredEquipments = [...allEquipments];
    let currentPage = 1;
    const equipmentsPerPage = 8;
    let currentSort = 'desc';
    let pendingForm = null;

    // Système de notification
    const notify = {
        container: null,

        init() {
            this.container = document.createElement('div');
            this.container.className = 'notification-container';
            document.body.appendChild(this.container);
        },

        show(type, title, message, duration = 5000) {
            if (!this.container) this.init();

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas fa-${this.getIcon(type)}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
            <div class="notification-progress"></div>
        `;

            this.container.appendChild(notification);

            // Animation d'entrée
            setTimeout(() => notification.classList.add('show'), 100);

            // Gestion de la fermeture
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => this.close(notification));

            // Barre de progression
            const progress = notification.querySelector('.notification-progress');
            let width = 100;
            const interval = setInterval(() => {
                width -= 100 / (duration / 50);
                progress.style.width = width + '%';
                if (width <= 0) {
                    clearInterval(interval);
                    this.close(notification);
                }
            }, 50);

            // Fermeture automatique
            setTimeout(() => this.close(notification), duration);
        },

        getIcon(type) {
            const icons = {
                success: 'check',
                error: 'exclamation-triangle',
                warning: 'exclamation-circle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        },

        close(notification) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        },

        success(title, message, duration) {
            this.show('success', title, message, duration);
        },

        error(title, message, duration) {
            this.show('error', title, message, duration);
        },

        warning(title, message, duration) {
            this.show('warning', title, message, duration);
        },

        info(title, message, duration) {
            this.show('info', title, message, duration);
        }
    };

    // Système de confirmation
    function confirmation(message, confirmText, details, onConfirm, onCancel, cancelText = 'Annuler') {
        const modal = document.getElementById('confirmationModal');
        const messageEl = document.getElementById('confirmationMessage');
        const detailsEl = document.getElementById('confirmationDetails');
        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        messageEl.textContent = message;
        confirmBtn.textContent = confirmText;
        cancelBtn.textContent = cancelText;

        if (details) {
            detailsEl.innerHTML = details;
            detailsEl.style.display = 'block';
        } else {
            detailsEl.style.display = 'none';
        }

        // Gestionnaires d'événements
        const confirmHandler = () => {
            closeConfirmation();
            if (onConfirm) onConfirm();
        };

        const cancelHandler = () => {
            closeConfirmation();
            if (onCancel) onCancel();
        };

        const closeConfirmation = () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            confirmBtn.removeEventListener('click', confirmHandler);
            cancelBtn.removeEventListener('click', cancelHandler);
            document.removeEventListener('keydown', handleKeydown);
        };

        const handleKeydown = (e) => {
            if (e.key === 'Escape') cancelHandler();
            if (e.key === 'Enter') confirmHandler();
        };

        confirmBtn.addEventListener('click', confirmHandler);
        cancelBtn.addEventListener('click', cancelHandler);
        document.addEventListener('keydown', handleKeydown);

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Équipements chargés:', allEquipments.length);
        console.log('Services:', services.length);
        console.log('Types:', types.length);

        if (allEquipments.length === 0) {
            console.warn('Aucun équipement trouvé dans la base de données');
            notify.warning('Aucun équipement', 'Aucun équipement trouvé dans la base de données.', 3000);
        }

        processEquipments();
        renderEquipments();
        renderPagination();
        notify.init();
        attachEventListeners();
        attachConfirmationListeners();
        setupFilters();
    });

    function processEquipments() {
        allEquipments = allEquipments.map(equipment => ({
            ...equipment,
            fullUserName: equipment.user_prenom && equipment.user_nom ?
                `${equipment.user_prenom} ${equipment.user_nom}` :
                (equipment.affectation_type === 'commun' ? 'Commun' : 'Non assigné'),
            salleInfo: equipment.salle_numero && equipment.salle_nom ?
                `${equipment.salle_numero} - ${equipment.salle_nom}` :
                'Non assignée',
            status: equipment.status || 'disponible',
            status_text: equipment.status_text || 'Disponible',
            status_class: equipment.status_class || 'status-warning',
            date_debut: equipment.date_debut || equipment.date_enregistrement
        }));
        filteredEquipments = [...allEquipments];
    }

    function renderEquipments() {
        const tbody = document.getElementById('equipmentTableBody');
        const start = (currentPage - 1) * equipmentsPerPage;
        const pageEquipments = filteredEquipments.slice(start, start + equipmentsPerPage);

        if (pageEquipments.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="12" class="empty-state">
                    <i class="fas fa-laptop"></i>
                    <p>Aucun équipement trouvé.</p>
                    ${filteredEquipments.length === 0 && allEquipments.length > 0 ?
                '<p class="text-muted">Aucun équipement ne correspond aux filtres appliqués.</p>' : ''}
                </td>
            </tr>
        `;
            document.getElementById('equipmentCount').innerHTML = '(<strong>0</strong> Équipements)';
            return;
        }

        tbody.innerHTML = pageEquipments.map(equipment => {
            // Gestion des équipements communs vs personnels
            const isCommun = equipment.affectation_type === 'commun';
            const userName = isCommun ?
                'Commun' :
                (equipment.user_prenom && equipment.user_nom ?
                    `${equipment.user_prenom} ${equipment.user_nom}` :
                    'Non assigné');

            const userRole = isCommun ?
                'Commun' :
                (equipment.user_role || 'Non assigné');

            const userRoleClass = isCommun ?
                'badge-commun' :
                `badge-${equipment.user_role?.toLowerCase() || 'none'}`;

            // Formatage des dates
            const dateDebut = equipment.date_debut ?
                new Date(equipment.date_debut).toLocaleDateString('fr-FR') : 'N/A';
            const garantieFin = equipment.garantie_fin ?
                new Date(equipment.garantie_fin).toLocaleDateString('fr-FR') : 'N/A';

            return `
            <tr>
                <td><strong>${equipment.serial_number}</strong></td>
                <td>${equipment.marque || 'N/A'}</td>
                <td>${equipment.model || 'N/A'}</td>
                <td>${equipment.type_nom}</td>
                <td>${userName}</td>
                <td><span class="badge ${userRoleClass}">${userRole}</span></td>
                <td>${equipment.service_nom || 'N/A'}</td>
                <td>${equipment.salle_nom || 'N/A'} ${equipment.salle_numero ? '(' + equipment.salle_numero + ')' : ''}</td>
                <td>${dateDebut}</td>
                <td>${garantieFin}</td>
                <td><span class="${equipment.status_class}">${equipment.status_text}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="icon-btn edit" title="Modifier" data-id="${equipment.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display:inline;" data-form-type="delete" data-equipment-id="${equipment.id}" data-equipment-serial="${equipment.serial_number}">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="equipement_id" value="${equipment.id}">
                            <button type="button" class="icon-btn delete confirm-btn" title="Supprimer" data-action="supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `;
        }).join('');

        document.getElementById('equipmentCount').innerHTML = `(<strong>${filteredEquipments.length}</strong> Équipements)`;
        attachEventListeners();
        attachConfirmationListeners();
    }

    function attachConfirmationListeners() {
        document.querySelectorAll('.confirm-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const form = this.closest('form');
                const action = this.getAttribute('data-action');
                const equipmentId = form.getAttribute('data-equipment-id');
                const serialNumber = form.getAttribute('data-equipment-serial');

                pendingForm = form;

                const details = `<div class="equipment-info">
                <i class="fas fa-laptop" style="font-size: 2rem; color: #3b82f6;"></i>
                <div class="equipment-details">
                    <div class="equipment-serial"><strong>Serial:</strong> ${serialNumber}</div>
                    <div class="equipment-action"><strong>Action:</strong> ${action}</div>
                </div>
            </div>`;

                let actionText = "";
                let btnText = "Confirmer";
                let cancelText = "Annuler";

                if (action === 'supprimer') {
                    actionText = "supprimer";
                    btnText = "Supprimer";
                    cancelText = "Fermer";
                }

                confirmation(
                    `Êtes-vous sûr de vouloir ${actionText} cet équipement ?`,
                    btnText,
                    details,
                    function() {
                        if (pendingForm) pendingForm.submit();
                    },
                    function() {
                        pendingForm = null;
                    },
                    cancelText
                );
            });
        });
    }

    function renderPagination() {
        const container = document.getElementById('paginationContainer');
        const totalPages = Math.ceil(filteredEquipments.length / equipmentsPerPage);
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';
        if (currentPage > 1) {
            html += `<button class="pagination-btn" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
        } else {
            html += `<span class="pagination-btn" style="opacity:0.5;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>`;
        }

        const start = Math.max(1, currentPage - 1);
        const end = Math.min(totalPages, start + 2);
        for (let i = start; i <= end; i++) {
            html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }

        if (currentPage < totalPages) {
            html += `<button class="pagination-btn" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
        } else {
            html += `<span class="pagination-btn" style="opacity:0.5;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>`;
        }

        container.innerHTML = html;
    }

    function changePage(page) {
        currentPage = page;
        renderEquipments();
        renderPagination();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function attachEventListeners() {
        document.querySelectorAll('.icon-btn.edit').forEach(btn => {
            btn.onclick = () => {
                const id = btn.getAttribute('data-id');
                const equipment = allEquipments.find(e => e.id == id);
                if (equipment) {
                    loadEquipmentData(equipment);
                    openModal();
                } else {
                    notify.error('Erreur', 'Équipement non trouvé.', 3000);
                }
            };
        });
    }

    function setupFilters() {
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('filter-type').addEventListener('change', applyFilters);
        document.getElementById('filter-service').addEventListener('change', applyFilters);
        document.getElementById('filter-status').addEventListener('change', applyFilters);

        document.getElementById('sortDateBtn').addEventListener('click', () => {
            currentSort = currentSort === 'desc' ? 'asc' : 'desc';
            sortEquipments(currentSort);
            renderEquipments();
            renderPagination();
            updateSortButtonText();
        });
    }

    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const typeFilter = document.getElementById('filter-type').value;
        const serviceFilter = document.getElementById('filter-service').value;
        const statusFilter = document.getElementById('filter-status').value;

        filteredEquipments = allEquipments.filter(equipment => {
            const matchesSearch = !searchTerm ||
                equipment.serial_number.toLowerCase().includes(searchTerm) ||
                equipment.marque?.toLowerCase().includes(searchTerm) ||
                equipment.model?.toLowerCase().includes(searchTerm) ||
                equipment.type_nom?.toLowerCase().includes(searchTerm) ||
                equipment.user_nom?.toLowerCase().includes(searchTerm) ||
                equipment.user_prenom?.toLowerCase().includes(searchTerm);

            const matchesType = !typeFilter || equipment.type_nom === typeFilter;
            const matchesService = !serviceFilter || equipment.service_nom === serviceFilter;
            const matchesStatus = !statusFilter || equipment.status === statusFilter;

            return matchesSearch && matchesType && matchesService && matchesStatus;
        });

        currentPage = 1;
        renderEquipments();
        renderPagination();
    }

    function sortEquipments(order) {
        filteredEquipments.sort((a, b) => {
            const dateA = new Date(a.date_debut || a.date_enregistrement);
            const dateB = new Date(b.date_debut || b.date_enregistrement);
            return order === 'desc' ? dateB - dateA : dateA - dateB;
        });
    }

    function updateSortButtonText() {
        const btn = document.getElementById('sortDateBtn');
        const icon = currentSort === 'desc' ? 'fa-sort-amount-down' : 'fa-sort-amount-up';
        const text = currentSort === 'desc' ? 'Date (récent)' : 'Date (ancien)';

        btn.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
    }

    function openModal() {
        document.getElementById('editEquipmentModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('editEquipmentModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function loadEquipmentData(equipment) {
        document.getElementById('editEquipmentId').value = equipment.id;
        document.getElementById('equipmentSerial').value = equipment.serial_number;
        document.getElementById('equipmentMarque').value = equipment.marque || '';
        document.getElementById('equipmentModel').value = equipment.model || '';
        document.getElementById('equipmentType').value = equipment.type_id;
        document.getElementById('equipmentDateDebut').value = equipment.date_debut || equipment.date_enregistrement || '';
        document.getElementById('equipmentGarantie').value = equipment.garantie_fin || '';
        document.getElementById('equipmentDetail').value = equipment.detail || '';
    }

    function saveChanges() {
        const form = document.getElementById('editEquipmentForm');
        const formData = new FormData(form);

        // Validation simple
        const marque = formData.get('marque');
        const model = formData.get('model');

        if (!marque || !model) {
            notify.error('Erreur', 'La marque et le modèle sont obligatoires.', 3000);
            return;
        }

        form.submit();
    }

    // Gestion des événements globaux
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (document.getElementById('editEquipmentModal').classList.contains('active')) {
                closeModal();
            }
            if (document.getElementById('confirmationModal').classList.contains('active')) {
                document.getElementById('confirmationModal').classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }
        if (e.ctrlKey && e.key === 's' && document.getElementById('editEquipmentModal').classList.contains('active')) {
            e.preventDefault();
            saveChanges();
        }
    });

    // Gestion des notifications existantes (PHP)
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fermeture des notifications PHP après 5 secondes
        setTimeout(() => {
            document.querySelectorAll('.notification.show').forEach(notification => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            });
        }, 5000);

        // Fermeture manuelle des notifications PHP
        document.querySelectorAll('.notification-close').forEach(btn => {
            btn.addEventListener('click', function() {
                const notification = this.closest('.notification');
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            });
        });
    });

    // Export des fonctions globales
    window.changePage = changePage;
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.saveChanges = saveChanges;
    window.confirmation = confirmation;
</script>
