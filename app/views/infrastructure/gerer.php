<div class="container">
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-server"></i>
                Gestion de l'Infrastructure
            </h1>
            <p class="subtitle">Gérer les services, types d'équipements et salles du système</p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-tabs">
            <button class="tab-btn active" data-tab="services">
                <i class="fas fa-cogs"></i> Services
                <span class="tab-counter" id="services-counter"><?= count($services) ?></span>
            </button>
            <button class="tab-btn" data-tab="types">
                <i class="fas fa-desktop"></i> Types d'Équipements
                <span class="tab-counter" id="types-counter"><?= count($types) ?></span>
            </button>
            <button class="tab-btn" data-tab="salles">
                <i class="fas fa-door-open"></i> Salles
                <span class="tab-counter" id="salles-counter"><?= count($salles) ?></span>
            </button>
        </div>
        
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Rechercher..." id="searchInput">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </div>

        <!-- Statistiques de recherche -->
        <div class="search-stats" id="searchStats">
            Affichage de tous les éléments
        </div>
        
        <!-- Services Tab -->
        <div class="tab-content active" id="services-tab">
            <div class="table-container">
                <table class="infrastructure-table" id="services-table">
                    <thead>
                        <tr>
                            <th>Nom du Service</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['nom']) ?></td>
                            <td><?= htmlspecialchars($service['description'] ?? '') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" class="inline-form" data-type="service" data-id="<?= $service['id'] ?>">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="type" value="service">
                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                        <button type="button" class="icon-btn edit" title="Modifier" 
                                                onclick="openEditModal('service', <?= $service['id'] ?>, '<?= addslashes($service['nom']) ?>', '<?= addslashes($service['description'] ?? '') ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>
                                    <form method="POST" class="inline-form" data-type="service" data-id="<?= $service['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="type" value="service">
                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                        <button type="button" class="icon-btn delete" title="Supprimer" 
                                                onclick="confirmDelete('service', <?= $service['id'] ?>, '<?= addslashes($service['nom']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Types Tab -->
        <div class="tab-content" id="types-tab">
            <div class="table-container">
                <table class="infrastructure-table" id="types-table">
                    <thead>
                        <tr>
                            <th>Nom du Type</th>
                            <th>Description</th>
                            <th>Type d'usage</th>
                            <th>Nombre d'équipement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= htmlspecialchars($type['nom']) ?></td>
                            <td><?= htmlspecialchars($type['description'] ?? '') ?></td>
                            <td>
                                <span class="usage-badge <?= $type['est_personnel'] ? 'usage-personnel' : 'usage-commun' ?>">
                                    <?= $type['est_personnel'] ? 'Personnel' : 'Commun' ?>
                                </span>
                            </td>
                            <td><?= $equipementsParType[$type['id']] ?? 0 ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" class="inline-form" data-type="type" data-id="<?= $type['id'] ?>">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="type" value="type">
                                        <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                        <button type="button" class="icon-btn edit" title="Modifier" 
                                                onclick="openEditModal('type', <?= $type['id'] ?>, '<?= addslashes($type['nom']) ?>', '<?= addslashes($type['description'] ?? '') ?>', <?= $type['est_personnel'] ? 'true' : 'false' ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>
                                    <form method="POST" class="inline-form" data-type="type" data-id="<?= $type['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="type" value="type">
                                        <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                        <button type="button" class="icon-btn delete" title="Supprimer" 
                                                onclick="confirmDelete('type', <?= $type['id'] ?>, '<?= addslashes($type['nom']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Salles Tab -->
        <div class="tab-content" id="salles-tab">
            <div class="table-container">
                <table class="infrastructure-table" id="salles-table">
                    <thead>
                        <tr>
                            <th>Nom de la Salle</th>
                            <th>Numéro</th>
                            <th>Service</th>
                            <th>Capacité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salles as $salle): ?>
                        <tr>
                            <td><?= htmlspecialchars($salle['nom']) ?></td>
                            <td><?= htmlspecialchars($salle['numero']) ?></td>
                            <td><?= htmlspecialchars($salle['service_nom']) ?></td>
                            <td><?= $salle['capacite'] ?> personnes</td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" class="inline-form" data-type="salle" data-id="<?= $salle['id'] ?>">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="type" value="salle">
                                        <input type="hidden" name="id" value="<?= $salle['id'] ?>">
                                        <button type="button" class="icon-btn edit" title="Modifier" 
                                                onclick="openEditModalSalle(<?= $salle['id'] ?>, '<?= addslashes($salle['nom']) ?>', '<?= addslashes($salle['numero']) ?>', <?= $salle['service_id'] ?>, <?= $salle['capacite'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>
                                    <form method="POST" class="inline-form" data-type="salle" data-id="<?= $salle['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="type" value="salle">
                                        <input type="hidden" name="id" value="<?= $salle['id'] ?>">
                                        <button type="button" class="icon-btn delete" title="Supprimer" 
                                                onclick="confirmDelete('salle', <?= $salle['id'] ?>, '<?= addslashes($salle['nom']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout/modification -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <button class="close-btn" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">
                <i class="fas fa-plus"></i>
                Ajouter un élément
            </h2>
            <p class="modal-subtitle" id="modalSubtitle">Remplissez les informations ci-dessous</p>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="type" id="formType" value="service">
                <input type="hidden" name="id" id="formId" value="">
                
                <div class="form-group">
                    <label class="form-label required" for="elementName">Nom</label>
                    <input type="text" id="elementName" name="nom" class="form-input" placeholder="Entrez le nom" required>
                </div>
                
                <div class="form-group" id="descriptionField">
                    <label class="form-label" for="elementDescription">Description</label>
                    <textarea id="elementDescription" name="description" class="form-input" rows="3" placeholder="Description (optionnel)" style="resize: vertical; min-height: 80px;"></textarea>
                </div>
                
                <div class="form-group" id="usageField" style="display: none;">
                    <label class="form-label required" for="elementUsage">Type d'usage</label>
                    <select id="elementUsage" name="est_personnel" class="form-select" required>
                        <option value="0">Commun (Partagé)</option>
                        <option value="1">Personnel (Individuel)</option>
                    </select>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        <strong>Commun</strong>: Équipement partagé entre plusieurs utilisateurs (ex: serveur, imprimante réseau)<br>
                        <strong>Personnel</strong>: Équipement attribué à un utilisateur spécifique (ex: ordinateur portable, téléphone)
                    </small>
                </div>
                
                <div class="form-group" id="numeroField" style="display: none;">
                    <label class="form-label required" for="elementNumero">Numéro</label>
                    <input type="text" id="elementNumero" name="numero" class="form-input" placeholder="Ex: A101">
                </div>
                
                <div class="form-group" id="serviceField" style="display: none;">
                    <label class="form-label required" for="elementService">Service</label>
                    <select id="elementService" name="service_id" class="form-select" required>
                        <option value="">Sélectionnez un service</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="capacityField" style="display: none;">
                    <label class="form-label" for="elementCapacity">Capacité (personnes)</label>
                    <input type="number" id="elementCapacity" name="capacite" class="form-input" placeholder="Ex: 20" min="1">
                </div>             
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal()">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="button" class="btn btn-primary" onclick="submitForm()">
                <i class="fas fa-save"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<style>
.usage-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.usage-personnel {
    background-color: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdefb;
}

.usage-commun {
    background-color: #f3e5f5;
    color: #7b1fa2;
    border: 1px solid #e1bee7;
}

.form-help {
    display: block;
    margin-top: 8px;
    color: #666;
    font-size: 12px;
    line-height: 1.4;
}

.form-help i {
    color: #2196f3;
    margin-right: 4px;
}

/* Styles pour les compteurs d'onglets */
.tab-counter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--primary);
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
    transition: var(--transition);
}

.tab-btn.active .tab-counter {
    background: white;
    color: var(--primary);
}

.tab-counter.empty {
    background: var(--secondary);
}

.search-stats {
    color: var(--secondary);
    font-size: 12px;
    margin-top: 8px;
    font-style: italic;
}

/* Animation pour le compteur */
@keyframes countUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.tab-counter.updated {
    animation: countUpdate 0.3s ease-in-out;
}
</style>

<script>
    // Variables globales
    let currentType = 'service';
    let totalCounts = {
        services: <?= count($services) ?>,
        types: <?= count($types) ?>,
        salles: <?= count($salles) ?>
    };

    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Mettre à jour les boutons d'onglet
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Mettre à jour le contenu des onglets
            tabContents.forEach(content => content.classList.remove('active'));
            document.getElementById(`${tabId}-tab`).classList.add('active');
            
            // Mettre à jour le type courant (convertir en singulier)
            if (tabId === 'services') currentType = 'service';
            else if (tabId === 'types') currentType = 'type';
            else if (tabId === 'salles') currentType = 'salle';

            // Mettre à jour les statistiques de recherche
            updateSearchStats();
        });
    });

    // Fonctionnalité de recherche
    const searchInput = document.querySelector('#searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const activeTable = document.querySelector('.tab-content.active table');
        const activeTab = document.querySelector('.tab-btn.active').getAttribute('data-tab');
        
        if (activeTable) {
            const tableRows = activeTable.querySelectorAll('tbody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (searchTerm === '' || rowText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mettre à jour le compteur de l'onglet actif
            updateTabCounter(activeTab, visibleCount);
            
            // Mettre à jour les statistiques de recherche
            updateSearchStats(searchTerm, visibleCount, activeTab);
        }
    });

    // Mettre à jour le compteur d'un onglet
    function updateTabCounter(tabType, count) {
        const counter = document.getElementById(`${tabType}-counter`);
        if (counter) {
            counter.textContent = count;
            
            // Ajouter une classe pour l'animation
            counter.classList.add('updated');
            setTimeout(() => counter.classList.remove('updated'), 300);
            
            // Changer l'apparence si aucun résultat
            if (count === 0) {
                counter.classList.add('empty');
            } else {
                counter.classList.remove('empty');
            }
        }
    }

    // Mettre à jour les statistiques de recherche
    function updateSearchStats(searchTerm = '', visibleCount = null, activeTab = null) {
        const statsElement = document.getElementById('searchStats');
        if (!statsElement) return;

        if (!activeTab) {
            activeTab = document.querySelector('.tab-btn.active').getAttribute('data-tab');
        }

        const totalCount = totalCounts[activeTab];
        
        if (searchTerm === '') {
            statsElement.textContent = `Affichage de tous les ${getTabName(activeTab)} (${totalCount} élément${totalCount !== 1 ? 's' : ''})`;
        } else {
            if (visibleCount === null) {
                // Compter les lignes visibles
                const activeTable = document.querySelector('.tab-content.active table');
                if (activeTable) {
                    visibleCount = activeTable.querySelectorAll('tbody tr:not([style*="display: none"])').length;
                } else {
                    visibleCount = 0;
                }
            }
            
            statsElement.textContent = `${visibleCount} ${getTabName(activeTab)} sur ${totalCount} correspondant à "${searchTerm}"`;
        }
    }

    // Obtenir le nom d'un onglet au format texte
    function getTabName(tabType) {
        const names = {
            'services': 'services',
            'types': 'types d\'équipements',
            'salles': 'salles'
        };
        return names[tabType] || 'éléments';
    }

    // Réinitialiser les compteurs quand on change d'onglet
    function resetTabCounters() {
        updateTabCounter('services', totalCounts.services);
        updateTabCounter('types', totalCounts.types);
        updateTabCounter('salles', totalCounts.salles);
        updateSearchStats();
    }

    // Ouvrir le modal pour ajouter un élément
    function openAddModal() {
        document.getElementById('formAction').value = 'add';
        
        // Correction: convertir le type au format singulier
        let formType = currentType;
        if (currentType === 'services') formType = 'service';
        if (currentType === 'types') formType = 'type';
        if (currentType === 'salles') formType = 'salle';
        
        document.getElementById('formType').value = formType;
        document.getElementById('formId').value = '';
        
        // Mettre à jour les titres en fonction du type
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        
        if (formType === 'service') {
            modalTitle.innerHTML = '<i class="fas fa-plus"></i> Ajouter un Service';
            modalSubtitle.textContent = 'Remplissez les informations du nouveau service';
        } else if (formType === 'type') {
            modalTitle.innerHTML = '<i class="fas fa-plus"></i> Ajouter un Type d\'Équipement';
            modalSubtitle.textContent = 'Remplissez les informations du nouveau type d\'équipement';
        } else if (formType === 'salle') {
            modalTitle.innerHTML = '<i class="fas fa-plus"></i> Ajouter une Salle';
            modalSubtitle.textContent = 'Remplissez les informations de la nouvelle salle';
        }
        
        // Afficher/masquer les champs appropriés
        toggleFormFields();
        
        // Réinitialiser le formulaire
        document.getElementById('editForm').reset();
        
        // Ouvrir le modal
        openModal();
    }

    // Ouvrir le modal pour modifier un service ou type
    function openEditModal(type, id, nom, description, estPersonnel = false) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formType').value = type;
        document.getElementById('formId').value = id;
        
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        
        // Déterminer le titre en fonction du type
        if (type === 'service') {
            modalTitle.innerHTML = '<i class="fas fa-edit"></i> Modifier le Service';
            modalSubtitle.textContent = 'Modifiez les informations du service';
        } else if (type === 'type') {
            modalTitle.innerHTML = '<i class="fas fa-edit"></i> Modifier le Type d\'Équipement';
            modalSubtitle.textContent = 'Modifiez les informations du type d\'équipement';
        }
        
        // Afficher/masquer les champs appropriés
        toggleFormFields();
        
        // Remplir le formulaire avec les données existantes
        document.getElementById('elementName').value = nom;
        document.getElementById('elementDescription').value = description;
        
        // Si c'est un type, remplir le champ d'usage
        if (type === 'type') {
            document.getElementById('elementUsage').value = estPersonnel ? '1' : '0';
        }
        
        // Ouvrir le modal
        openModal();
    }

    // Ouvrir le modal pour modifier une salle
    function openEditModalSalle(id, nom, numero, serviceId, capacite) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formType').value = 'salle';
        document.getElementById('formId').value = id;
        
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        
        modalTitle.innerHTML = '<i class="fas fa-edit"></i> Modifier la Salle';
        modalSubtitle.textContent = 'Modifiez les informations de la salle';
        
        // Afficher/masquer les champs appropriés
        toggleFormFields();
        
        // Remplir le formulaire avec les données existantes
        document.getElementById('elementName').value = nom;
        document.getElementById('elementNumero').value = numero;
        document.getElementById('elementService').value = serviceId;
        document.getElementById('elementCapacity').value = capacite;
        
        // Ouvrir le modal
        openModal();
    }

    // Confirmer la suppression d'un élément
    function confirmDelete(type, id, nom) {
        confirmation(
            `Supprimer ${nom} ?`, 
            "Supprimer", 
            `Êtes-vous sûr de vouloir supprimer "${nom}" ? Cette action est irréversible.`,
            function() {
                // Créer un formulaire de suppression et le soumettre
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'type';
                typeInput.value = type;
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(typeInput);
                form.appendChild(idInput);
                
                document.body.appendChild(form);
                form.submit();
            },
            null,
            "Annuler"
        );
    }

    // Afficher/masquer les champs du formulaire selon le type
    function toggleFormFields() {
        const type = document.getElementById('formType').value;
        const descriptionField = document.getElementById('descriptionField');
        const usageField = document.getElementById('usageField');
        const numeroField = document.getElementById('numeroField');
        const serviceField = document.getElementById('serviceField');
        const capacityField = document.getElementById('capacityField');
        
        // Masquer tous les champs optionnels d'abord
        descriptionField.style.display = 'block';
        usageField.style.display = 'none';
        numeroField.style.display = 'none';
        serviceField.style.display = 'none';
        capacityField.style.display = 'none';
        
        // Afficher les champs appropriés selon le type
        if (type === 'service') {
            // Services: nom et description
            usageField.style.display = 'none';
            numeroField.style.display = 'none';
            serviceField.style.display = 'none';
            capacityField.style.display = 'none';
        } else if (type === 'type') {
            // Types: nom, description et type d'usage
            usageField.style.display = 'block';
            numeroField.style.display = 'none';
            serviceField.style.display = 'none';
            capacityField.style.display = 'none';
        } else if (type === 'salle') {
            // Salles: nom, numéro, service et capacité
            descriptionField.style.display = 'none';
            usageField.style.display = 'none';
            numeroField.style.display = 'block';
            serviceField.style.display = 'block';
            capacityField.style.display = 'block';
        }
    }

    // Fonction pour ouvrir le modal
    function openModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Fonction pour fermer le modal
    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Fonction pour soumettre le formulaire
    function submitForm() {
        const form = document.getElementById('editForm');
        
        // Validation simple
        const name = document.getElementById('elementName').value.trim();
        if (!name) {
            notify.error('Erreur', 'Le nom est requis');
            return;
        }
        
        // Si c'est une salle, validation supplémentaire
        if (document.getElementById('formType').value === 'salle') {
            const numero = document.getElementById('elementNumero').value.trim();
            const service = document.getElementById('elementService').value;
            
            if (!numero) {
                notify.error('Erreur', 'Le numéro est requis');
                return;
            }
            
            if (!service) {
                notify.error('Erreur', 'Le service est requis');
                return;
            }
        }
        
        // Soumettre le formulaire
        form.submit();
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Fermer le modal avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Initialiser les compteurs au chargement
    document.addEventListener('DOMContentLoaded', function() {
        resetTabCounters();
    });
</script>