<?php
// /app/views/reclamations/suivi.php

// Récupération des données de l'utilisateur (passées par le contrôleur)
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';
$user_role = $_SESSION['role'] ?? 'agent';

// Les réclamations sont déjà passées par le contrôleur dans la variable $reclamations

// Récupérer les matériels uniques pour le filtre
$materiels = [];
foreach ($reclamations as $reclamation) {
    if (!in_array($reclamation['materiel_info'], $materiels)) {
        $materiels[] = $reclamation['materiel_info'];
    }
}
sort($materiels);
?>

<style>
    /* Actions de page - COMPACT */
    .page-actions {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Filtres - COMPACT */
    .filtres {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        width: 100%;
        justify-content: flex-start;
    }

    .filtre-groupe {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filtre-groupe label {
        font-size: 12px;
        font-weight: 500;
        color: var(--dark);
    }

    .filtre-select, .search-input {
        padding: 6px 10px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border);
        background-color: var(--white);
        color: var(--dark);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        cursor: pointer;
        transition: var(--transition);
        min-width: 120px;
        height: 32px;
    }

    .search-input {
        min-width: 150px;
    }

    .filtre-select:focus, .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .btn-reset {
        align-self: flex-end;
        padding: 6px 10px;
        background-color: var(--secondary);
        color: var(--white);
        border: none;
        border-radius: var(--border-radius);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        cursor: pointer;
        transition: var(--transition);
        height: 32px;
    }

    .btn-reset:hover {
        background-color: var(--dark-light);
    }

    /* Carte - COMPACT */
    .card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        overflow: hidden;
        animation: fadeInUp 0.6s ease;
    }

    .card-header {
        background: var(--primary);
        color: var(--white);
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 16px;
    }

    /* Liste verticale des réclamations - COMPACT */
    .reclamation-list {
        display: flex !important;
        flex-direction: column;
        gap: 12px;
    }

    .reclamation-item {
        background: var(--light-secondary);
        border-radius: var(--border-radius);
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: var(--transition);
        border-left: 3px solid var(--primary);
    }

    .reclamation-item.hidden {
        display: none !important;
    }

    .reclamation-item:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .reclamation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .reclamation-id {
        font-weight: 700;
        color: var(--primary-dark);
        font-size: 14px;
    }

    .reclamation-date {
        color: var(--secondary);
        font-size: 11px;
    }

    .reclamation-materiel {
        font-weight: 500;
        font-size: 13px;
    }

    .reclamation-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
    }

    .reclamation-motif-technicien {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 4px;
        padding: 8px;
        margin-top: 6px;
        font-size: 11px;
        color: var(--danger);
    }

    /* Badges de statut - COMPACT */
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge-success {
        background-color: rgba(16, 185, 129, 0.15);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-warning {
        background-color: rgba(245, 158, 11, 0.15);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-danger {
        background-color: rgba(239, 68, 68, 0.15);
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-info {
        background-color: rgba(59, 130, 246, 0.15);
        color: var(--primary);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .badge-secondary {
        background-color: rgba(100, 116, 139, 0.15);
        color: var(--secondary);
        border: 1px solid rgba(100, 116, 139, 0.3);
    }

    /* Boutons - COMPACT */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border: none;
        border-radius: var(--border-radius);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        height: 32px;
    }

    .btn-primary {
        background-color: var(--primary);
        color: var(--white);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Nouvelle réclamation button - COMPACT */
    .nouvelle-reclamation-section {
        text-align: center;
        padding: 20px 0;
        border-top: 1px dashed var(--border);
        margin-top: 1rem;
    }

    .nouvelle-reclamation-section h3 {
        color: var(--secondary);
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 500;
    }

    .btn-nouvelle {
        background: linear-gradient(45deg, var(--primary), var(--accent));
        color: var(--white);
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 600;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
    }

    .btn-nouvelle:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }

    /* Message vide - COMPACT */
    .empty-state {
        text-align: center;
        padding: 30px 15px;
        color: var(--secondary);
    }

    .empty-state i {
        font-size: 36px;
        margin-bottom: 10px;
        color: var(--border);
    }

    .mt-3 {
        margin-top: 12px;
    }

    .results-count {
        font-size: 12px;
        color: var(--secondary);
        margin-bottom: 12px;
        text-align: center;
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Styles responsives - OPTIMISÉ */
    @media (max-width: 768px) {
        .content {
            padding: 10px;
        }
        
        .dashboard-header {
            margin-bottom: 1rem;
        }
        
        .greeting { 
            font-size: 1.2rem; 
        }
        
        .subtitle { 
            font-size: 0.8rem; 
        }
        
        .page-actions {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1rem;
            gap: 8px;
        }
        
        .filtres {
            width: 100%;
            justify-content: space-between;
            gap: 8px;
        }
        
        .filtre-groupe {
            flex: 1;
            min-width: 100px;
        }
        
        .filtre-select, .search-input {
            min-width: 100px;
            font-size: 11px;
            padding: 5px 8px;
            height: 28px;
        }
        
        .card-body {
            padding: 12px;
        }
        
        .reclamation-header, .reclamation-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
        
        .reclamation-item {
            padding: 10px;
            gap: 6px;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
            font-size: 11px;
            padding: 6px 10px;
            height: 28px;
        }
        
        .card-header {
            padding: 10px 12px;
        }
        
        .card-header h2 {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .content {
            padding: 6px;
        }
        
        .filtres {
            flex-direction: column;
            gap: 6px;
        }
        
        .filtre-groupe {
            min-width: 100%;
        }
        
        .reclamation-id {
            font-size: 13px;
        }
        
        .reclamation-materiel {
            font-size: 12px;
        }
        
        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }
    }

    /* Amélioration de l'espacement pour écrans larges */
    @media (min-width: 1200px) {
        .content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .filtres {
            justify-content: flex-start;
        }
        
        .filtre-groupe {
            flex: 0 1 auto;
        }
    }
</style>

<div class="content">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="greeting">Suivi des Réclamations</h1>
            <p class="subtitle">Consultez l'état et l'historique de vos réclamations</p>
        </div>
        
        <div class="page-actions">
            <div class="filtres">
                <div class="filtre-groupe">
                    <label for="searchInput">Recherche</label>
                    <input type="text" class="search-input" id="searchInput" placeholder="Rechercher...">
                </div>
                
                <div class="filtre-groupe">
                    <label for="filtre-materiel">Matériel</label>
                    <select class="filtre-select" id="filtre-materiel">
                        <option value="tous">Tous les matériels</option>
                        <?php foreach ($materiels as $materiel): ?>
                            <option value="<?php echo htmlspecialchars($materiel); ?>"><?php echo htmlspecialchars($materiel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filtre-groupe">
                    <label for="filtre-statut">Statut</label>
                    <select class="filtre-select" id="filtre-statut">
                        <option value="tous">Tous les statuts</option>
                        <?php foreach ($statuts as $key => $libelle): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>">
                                <?php echo htmlspecialchars($libelle); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filtre-groupe">
                    <label for="filtre-date">Trier par date</label>
                    <select class="filtre-select" id="filtre-date">
                        <option value="recentes">Plus récentes</option>
                        <option value="anciennes">Plus anciennes</option>
                    </select>
                </div>
                
                <div class="filtre-groupe">
                    <label>&nbsp;</label>
                    <button class="btn-reset" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
                
        <div class="card">
            <div class="card-header">
                <h2>Vos réclamations</h2>
                <span id="results-count"><?php echo count($reclamations); ?> réclamation(s)</span>
            </div>
            <div class="card-body">
                <?php if (empty($reclamations)): ?>
                    <div id="empty-state" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucune réclamation trouvée</h3>
                        <p>Vous n'avez encore soumis aucune réclamation.</p>
                    </div>
                <?php else: ?>
                    <div id="empty-state" class="empty-state" style="display: none;">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucune réclamation trouvée</h3>
                        <p>Aucune réclamation ne correspond aux critères de filtrage sélectionnés.</p>
                    </div>
                    
                    <div class="reclamation-list" id="reclamation-list">
                        <?php foreach ($reclamations as $reclamation): ?>
                            <div class="reclamation-item" 
                                data-statut="<?php echo htmlspecialchars($reclamation['statut']); ?>" 
                                data-materiel="<?php echo htmlspecialchars($reclamation['materiel_info']); ?>" 
                                data-date="<?php echo htmlspecialchars($reclamation['date_reclamation']); ?>" 
                                data-search="<?php echo htmlspecialchars(strtolower($reclamation['id'] . ' ' . $reclamation['materiel_info'] . ' ' . $reclamation['statut_libelle'] . ' ' . $reclamation['statut'])); ?>">
                                                        
                            <div class="reclamation-header">
                                <span class="reclamation-id"><?php echo $reclamation['id']; ?></span>
                                <span class="reclamation-date">Soumis le <?php echo date('d/m/Y', strtotime($reclamation['date_reclamation'])); ?></span>
                            </div>
                            
                            <div class="reclamation-materiel">
                                <?php echo $reclamation['materiel_info']; ?>
                            </div>
                            
                            <div class="reclamation-footer">
                                <?php
                                // Déterminer la classe CSS en fonction du statut technique
                                $badge_class = '';
                                switch ($reclamation['statut']) {
                                    case 'resolu':
                                        $badge_class = 'badge-success';
                                        break;
                                    case 'ferme':
                                        $badge_class = 'badge-danger';
                                        break;
                                    case 'en_cours':
                                        $badge_class = 'badge-warning';
                                        break;
                                    case 'en_attente':
                                    default:
                                        $badge_class = 'badge-info';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($reclamation['statut_libelle']); // Afficher le libellé ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($reclamation['motif_technicien'])): ?>
                            <div class="reclamation-motif-technicien">
                                <strong>Motif du technicien:</strong> <?php echo $reclamation['motif_technicien']; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        

    </div>
</div>

<script>
    // Fonction pour formater le texte de recherche (supprimer les accents)
    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }
    
    // Fonction pour trier les réclamations par date avec animation
    function sortReclamationsByDate(reclamationsContainer, order) {
        const reclamations = Array.from(reclamationsContainer.querySelectorAll('.reclamation-item:not(.hidden)'));
        
        if (reclamations.length === 0) return;
        
        reclamations.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            
            if (order === 'recentes') {
                return dateB - dateA; // Plus récentes d'abord
            } else {
                return dateA - dateB; // Plus anciennes d'abord
            }
        });
        
        // Animation de réorganisation
        reclamationsContainer.style.opacity = '0.7';
        reclamationsContainer.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            // Réorganiser les éléments dans le conteneur
            reclamations.forEach(reclamation => {
                reclamationsContainer.appendChild(reclamation);
            });
            
            reclamationsContainer.style.opacity = '1';
        }, 150);
    }
    
    // Fonction pour filtrer les réclamations
    function filterReclamations() {
        const searchText = normalizeText(document.getElementById('searchInput').value);
        const materielValue = document.getElementById('filtre-materiel').value;
        const statutValue = document.getElementById('filtre-statut').value;
        const dateValue = document.getElementById('filtre-date').value;
        
        const reclamations = document.querySelectorAll('.reclamation-item');
        let visibleCount = 0;
        
        // Première passe : filtrer par recherche, matériel et statut
        reclamations.forEach(reclamation => {
            const reclamationSearch = reclamation.getAttribute('data-search');
            const reclamationMateriel = reclamation.getAttribute('data-materiel');
            const reclamationStatut = reclamation.getAttribute('data-statut');
            
            // Vérifier les critères de filtrage
            const matchesSearch = searchText === '' || normalizeText(reclamationSearch).includes(searchText);
            const matchesMateriel = materielValue === 'tous' || reclamationMateriel === materielValue;
            const matchesStatut = statutValue === 'tous' || reclamationStatut === statutValue;
            
            if (matchesSearch && matchesMateriel && matchesStatut) {
                reclamation.classList.remove('hidden');
                visibleCount++;
            } else {
                reclamation.classList.add('hidden');
            }
        });
        
        // Deuxième passe : trier par date
        const reclamationList = document.getElementById('reclamation-list');
        if (reclamationList) {
            sortReclamationsByDate(reclamationList, dateValue);
        }
        
        // Gérer l'affichage du message vide
        const emptyState = document.getElementById('empty-state');
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
            if (reclamationList) reclamationList.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            if (reclamationList) reclamationList.style.display = 'flex';
        }
        
        // Mettre à jour le compteur de résultats
        document.getElementById('results-count').textContent = visibleCount + ' réclamation(s)';
    }
    
    // Fonction pour réinitialiser les filtres
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filtre-materiel').value = 'tous';
        document.getElementById('filtre-statut').value = 'tous';
        document.getElementById('filtre-date').value = 'recentes';
        filterReclamations();
    }
    
    // Ajouter les écouteurs d'événements
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchInput').addEventListener('input', filterReclamations);
        document.getElementById('filtre-materiel').addEventListener('change', filterReclamations);
        document.getElementById('filtre-statut').addEventListener('change', filterReclamations);
        document.getElementById('filtre-date').addEventListener('change', filterReclamations);
        
        // Trier initialement par date récente
        setTimeout(filterReclamations, 100);
    });
</script>