<style>
    /* CSS compact */

    .card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 1rem;
        overflow: hidden; /* Nouveau */
    }

    .page-actions {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filtres {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        width: 100%;
    }

    .filtre-groupe {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filtre-groupe label {
        font-size: 12px;
        font-weight: 500;
    }

    .filtre-select, .search-input {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid var(--border);
        font-size: 12px;
        min-width: 120px;
    }

    .search-input { min-width: 150px; }

    .btn-reset {
        padding: 6px 10px;
        font-size: 12px;
    }

    .card-body { 
        overflow-x: auto;
    }

    .sort-th { display: flex; align-items: center; gap: 5px; }
    .sort-label { font-size: 0.75rem; }

    .btn {
        padding: 0.4rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        gap: 0.3rem;
    }

    .btn-details, .btn-action, .btn-prendre {
        padding: 0.3rem 0.5rem;
        min-width: 70px;
        font-size: 0.75rem;
    }

    .btn-prendre {
        background: linear-gradient(to right, #3b82f6, #1d4ed8);
        color: white;
    }

    .btn-prendre:hover {
        background: #1d4ed8;
    }

    .btn-action {
        background: linear-gradient(to right, #10b981, #0d9488);
        color: white;
    }

    .btn-action:hover {
        background: #0d9488;
    }

    .table-responsive { overflow-x: auto; }
    
    .data-table {
        font-size: 0.8rem;
        border-collapse: collapse; /* Nouveau */
        width: 100%; /* Nouveau */
    }

    .data-table th, .data-table td {
        padding: 0.6rem 0.8rem;
        border-bottom: 1px solid #f0f0f0; /* Nouveau */
    }

    .data-table th {
        font-size: 0.75rem;
        padding: 0.7rem 0.8rem;
        background-color: #f8fafc; /* Nouveau */
        border-bottom: 2px solid #e2e8f0; /* Nouveau */
    }

    .data-table tr:hover {
        background-color: #f8fafc; /* Nouveau */
    }

    .badge {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .badge-primary {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .empty-state {
        padding: 2rem 1rem;
        font-size: 0.9rem;
        text-align: center;
        display: none;
    }

    .empty-state i { font-size: 2rem; }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: var(--white);
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .modal-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        font-size: 1.2rem;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        padding: 0.5rem 0;
    }

    .detail-item label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.8rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .detail-item span, .detail-item p {
        color: #1f2937;
        font-size: 0.9rem;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .action-form {
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
    }

    .action-form h4 {
        margin-bottom: 1rem;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-control, .form-textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
    }

    .modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px; /* Espacement entre les boutons */
}

.modal-footer .btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
}

.modal-footer .modal-close-btn {
    background: #6b7280;
    color: white;
    border: none;
}

.modal-footer .modal-close-btn:hover {
    background: #4b5563;
}

.modal-footer .btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
}

.modal-footer .btn-primary:hover {
    background: #2563eb;
}

    @media (max-width: 768px) {
        .content-wrapper { padding: 0.5rem; }
        .data-table th, .data-table td { padding: 0.5rem; }
        .details-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="content">
    <div class="dashboard-header">
        <h1 class="greeting">Réclamations qui me sont assignées & libre</h1>
        <p class="subtitle" id="results-count"><?= count($reclamations) ?> réclamation(s)</p>
    </div>

    <div class="page-actions">
        <div class="filtres">
            <div class="filtre-groupe">
                <label for="searchInput">Recherche</label>
                <input type="text" class="search-input" id="searchInput" placeholder="Rechercher...">
            </div>
            
            <div class="filtre-groupe">
                <label for="filtre-materiel">Type d'équipement</label>
                <select class="filtre-select" id="filtre-materiel">
                    <option value="tous">Tous les types</option>
                    <?php foreach ($typesEquipement as $typeName): ?>
                        <option value="<?= htmlspecialchars($typeName) ?>"><?= htmlspecialchars($typeName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filtre-groupe">
                <label for="filtre-statut">Statut</label>
                <select class="filtre-select" id="filtre-statut">
                    <option value="tous">Tous</option>
                    <option value="libre">Libre</option>
                    <option value="en_cours">En cours</option>
                    <option value="resolu">Résolu</option>
                    <option value="ferme">Fermé</option>
                </select>
            </div>
            
            <div class="filtre-groupe">
                <label for="filtre-date">Tri date</label>
                <select class="filtre-select" id="filtre-date">
                    <option value="recentes">Récentes</option>
                    <option value="anciennes">Anciennes</option>
                </select>
            </div>
            
            <div class="filtre-groupe">
                <label>&nbsp;</label>
                <button class="btn-reset" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($reclamations)): ?>
                <div id="empty-state" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune réclamation</h3>
                    <p>Aucune réclamation ne correspond aux critères.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive" id="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>
                                    <div class="sort-th">
                                        <span class="sort-label">Date</span>
                                    </div>
                                </th>
                                <th>Type d'équipement</th>
                                <th>Motif</th>
                                <th>Salle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reclamations-tbody">
                            <?php foreach ($reclamations as $reclamation): ?>
                                <?php
                                // Mapping des statuts avec gestion du statut "libre"
                                $statusLabels = [
                                    'libre' => 'Libre',
                                    'en_attente' => 'En attente', 
                                    'en_cours' => 'En cours',
                                    'resolu' => 'Résolu',
                                    'ferme' => 'Fermé'
                                ];
                                
                                $statusBadges = [
                                    'libre' => 'badge-warning',
                                    'en_attente' => 'badge-warning',
                                    'en_cours' => 'badge-primary',
                                    'resolu' => 'badge-success',
                                    'ferme' => 'badge-danger'
                                ];
                                
                                // Déterminer le type d'équipement
                                $typeEquipement = $reclamation['type_equipement'] ?? 'Non spécifié';
                                
                                $displayStatus = $statusLabels[$reclamation['statut_affichage'] ?? $reclamation['statut']] ?? 'Inconnu';
                                $badgeClass = $statusBadges[$reclamation['statut_affichage'] ?? $reclamation['statut']] ?? 'badge';
                                ?>
                                <tr class="reclamation-item" 
                                    data-id="<?= $reclamation['id'] ?>" 
                                    data-date="<?= $reclamation['date_reclamation'] ?>"
                                    data-statut="<?= $reclamation['statut_affichage'] ?? $reclamation['statut'] ?>" 
                                    data-materiel="<?= htmlspecialchars($typeEquipement) ?>"
                                    data-search="<?= htmlspecialchars($reclamation['motif'] . ' ' . ($reclamation['salle_nom'] ?? '') . ' ' . $reclamation['id'] . ' ' . $typeEquipement) ?>">
                                    
                                    <td><?= $reclamation['id'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($reclamation['date_reclamation'])) ?></td>
                                    <td><?= htmlspecialchars($typeEquipement) ?></td>
                                    <td><?= htmlspecialchars($reclamation['motif']) ?></td>
                                    <td><?= htmlspecialchars($reclamation['salle_nom'] ?? 'N/A') ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= $displayStatus ?></span></td>
                                    <td>
                                        <div style="display: flex; gap: 0.3rem;">
                                            <?php if (($reclamation['statut_affichage'] ?? $reclamation['statut']) === 'libre'): ?>
                                                <!-- Bouton "Prendre" pour les réclamations libres -->
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="prendre">
                                                    <input type="hidden" name="reclamation_id" value="<?= $reclamation['id'] ?>">
                                                    <button type="submit" class="btn btn-prendre" title="Prendre cette réclamation">
                                                        <i class="fas fa-hand-paper"></i> Prendre
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <!-- Bouton "Traiter" pour les réclamations assignées -->
                                                <button class="btn btn-action traiter-btn" data-id="<?= $reclamation['id'] ?>">
                                                    <i class="fas fa-cog"></i> Traiter
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de traitement -->
<div class="modal" id="detailsModal">
    <div class="modal-content">
        <form method="POST" action="<?= $baseUrl ?>/reclamations/traiter" id="reclamation-form">
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" id="modal-reclamation-id" name="reclamation_id" value="">
            
            <div class="modal-header">
                <h2><i class="fas fa-ticket-alt"></i> Détails de la réclamation <span id="modal-id"></span></h2>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Date:</label>
                        <span id="modal-date"></span>
                    </div>
                    <div class="detail-item">
                        <label>Type d'équipement:</label>
                        <span id="modal-type-equipement"></span>
                    </div>
                    <div class="detail-item">
                        <label>Motif:</label>
                        <span id="modal-motif"></span>
                    </div>
                    <div class="detail-item">
                        <label>Salle:</label>
                        <span id="modal-salle"></span>
                    </div>
                    <div class="detail-item">
                        <label>Statut actuel:</label>
                        <span id="modal-statut"></span>
                    </div>
                    <div class="detail-item full-width">
                        <label>Description complète:</label>
                        <p id="modal-description"></p>
                    </div>
                    <div class="detail-item full-width">
                        <label>Matériel concerné:</label>
                        <p id="modal-materiel"></p>
                    </div>
                    <div class="detail-item full-width">
                        <label>Demandeur:</label>
                        <p id="modal-demandeur"></p>
                    </div>
                    <div class="detail-item full-width">
                        <label>Remarques précédentes:</label>
                        <p id="modal-remarques" style="font-style: italic; color: #6b7280;"></p>
                    </div>
                </div>
                
                <div class="action-form">
                    <h4><i class="fas fa-edit"></i> Mettre à jour cette réclamation</h4>
                    <div class="form-group">
                        <label for="statut-update">Nouveau statut</label>
                        <select id="statut-update" name="statut" class="form-control" required>
                            <?php foreach ($statuts as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reclamation-type">Type de réclamation <span>*</span></label>
                        <select id="reclamation-type" name="type" class="form-control" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="hardware">Hardware</option>
                            <option value="software">Software</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="support-notes">Notes du support</label>
                        <textarea id="support-notes" name="motif_support" class="form-textarea" placeholder="Ajoutez vos notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn modal-close-btn">Fermer</button>
                <button type="submit" class="btn btn-primary" id="update-reclamation">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ================================
// Configuration
// ================================
const baseUrl = '<?= $baseUrl ?>';
const reclamationsData = <?= json_encode($reclamations) ?>;
const typesEquipement = <?= json_encode($typesEquipement) ?>;

// Status mapping
const STATUS_LABELS = {
    'libre': 'Libre',
    'en_attente': 'En attente',
    'en_cours': 'En cours',
    'resolu': 'Résolu',
    'ferme': 'Fermé'
};

// DOM elements
const modal = document.getElementById('detailsModal');
const reclamationForm = document.getElementById('reclamation-form');
const tbody = document.getElementById('reclamations-tbody');
const emptyState = document.getElementById('empty-state');

// Fonction pour normaliser le texte (supprimer les accents)
function normalizeText(text) {
    return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

// Fonction de filtrage COMPLÈTE
function filterReclamations() {
    const searchText = normalizeText(document.getElementById('searchInput').value);
    const materielValue = document.getElementById('filtre-materiel').value;
    const statutValue = document.getElementById('filtre-statut').value;
    const dateValue = document.getElementById('filtre-date').value;
    
    const rows = Array.from(document.querySelectorAll('#reclamations-tbody tr'));
    let visibleRows = [];
    
    // Étape 1: Filtrer les lignes
    rows.forEach(row => {
        const searchData = normalizeText(row.getAttribute('data-search') || '');
        const materielData = row.getAttribute('data-materiel') || '';
        const statutData = row.getAttribute('data-statut') || '';
        
        // Critères de filtrage
        const matchesSearch = searchText === '' || searchData.includes(searchText);
        const matchesMateriel = materielValue === 'tous' || materielData === materielValue;
        const matchesStatut = statutValue === 'tous' || statutData === statutValue;
        
        if (matchesSearch && matchesMateriel && matchesStatut) {
            row.style.display = '';
            visibleRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });
    
    // Étape 2: Trier par date si nécessaire
    if (dateValue !== 'recentes') {
        visibleRows.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            return dateA - dateB; // Anciennes d'abord
        });
    } else {
        // Récentes d'abord (ordre décroissant par défaut)
        visibleRows.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            return dateB - dateA; // Récentes d'abord
        });
    }
    
    // Étape 3: Réorganiser les lignes dans le DOM
    const tbody = document.getElementById('reclamations-tbody');
    visibleRows.forEach(row => {
        tbody.appendChild(row); // Cela les replace dans le bon ordre
    });
    
    // Gérer l'affichage du message vide
    if (emptyState) {
        const tableContainer = document.querySelector('.table-responsive');
        if (visibleRows.length === 0) {
            emptyState.style.display = 'block';
            if (tableContainer) tableContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            if (tableContainer) tableContainer.style.display = 'block';
        }
    }
    
    // Mettre à jour le compteur
    document.getElementById('results-count').textContent = visibleRows.length + ' réclamation(s)';
}

// Réinitialiser les filtres
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filtre-materiel').value = 'tous';
    document.getElementById('filtre-statut').value = 'tous';
    document.getElementById('filtre-date').value = 'recentes';
    filterReclamations();
}

// Fonctions modal
function attachButtonEvents() {
    document.querySelectorAll('.traiter-btn').forEach(btn => {
        btn.onclick = () => openModal(btn.dataset.id);
    });
}

function openModal(id) {
    const item = reclamationsData.find(r => r.id == id);
    if (!item) return;

    const displayStatus = STATUS_LABELS[item.statut] || 'Inconnu';
    const typeEquipement = item.type_equipement || 'Non spécifié';

    // Remplir les données du modal
    document.getElementById('modal-id').textContent = item.id;
    document.getElementById('modal-reclamation-id').value = item.id;
    document.getElementById('modal-date').textContent = formatDate(item.date_reclamation);
    document.getElementById('modal-type-equipement').textContent = escapeHtml(typeEquipement);
    document.getElementById('modal-motif').textContent = escapeHtml(item.motif);
    document.getElementById('modal-salle').textContent = escapeHtml(item.salle_nom || 'Non spécifiée');
    document.getElementById('modal-statut').textContent = displayStatus;
    document.getElementById('modal-description').textContent = escapeHtml(item.motif);
    document.getElementById('modal-materiel').textContent = escapeHtml(
        (item.marque && item.model) ? `${item.marque} ${item.model}` : 'Non spécifié'
    );
    document.getElementById('modal-demandeur').textContent = escapeHtml(item.demandeur_nom || 'Inconnu');
    document.getElementById('modal-remarques').textContent = escapeHtml(item.motif_support || 'Aucune remarque');

    // Pré-remplir les champs du formulaire
    document.getElementById('statut-update').value = item.statut;
    document.getElementById('reclamation-type').value = item.type || '';
    document.getElementById('support-notes').value = item.motif_support || '';

    modal.classList.add('show');
}

function formatDate(isoDate) {
    if (!isoDate) return 'Non spécifiée';
    const date = new Date(isoDate);
    return date.toLocaleDateString('fr-FR');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Événements
document.addEventListener('DOMContentLoaded', () => {
    attachButtonEvents();
    
    // Événements de filtrage
    document.getElementById('searchInput').addEventListener('input', filterReclamations);
    document.getElementById('filtre-materiel').addEventListener('change', filterReclamations);
    document.getElementById('filtre-statut').addEventListener('change', filterReclamations);
    document.getElementById('filtre-date').addEventListener('change', filterReclamations);
    
    // Événements modal
    document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.remove('show');
        });
    });

    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.remove('show');
    });

    // Validation du formulaire
    reclamationForm.addEventListener('submit', function(e) {
        const statut = document.getElementById('statut-update').value;
        const type = document.getElementById('reclamation-type').value;
        
        if (!statut || !type) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires');
            return false;
        }
    });
});

// Tri par date
if (document.querySelector('.sort-th')) {
    document.querySelector('.sort-th').addEventListener('click', function() {
        const rows = Array.from(tbody.querySelectorAll('tr:not([style*="display: none"])'));
        const direction = this.classList.contains('asc') ? -1 : 1;
        
        rows.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            return (dateA - dateB) * direction;
        });
        
        // Réinsérer les lignes triées
        rows.forEach(row => tbody.appendChild(row));
        
        // Changer la direction
        this.classList.toggle('asc');
    });
}

// Initialiser le filtrage au chargement
document.addEventListener('DOMContentLoaded', filterReclamations);
</script>