<?php
// Récupérer les données passées par le contrôleur
$data = $data ?? [];
$stats = $data['stats'] ?? [];
$reclamationTypes = $data['reclamationTypes'] ?? [];
$alerts = $data['alerts'] ?? [];
?>

<div class="dashboard-content">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="greeting">Bienvenue, Admin <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?></h1>
        <p class="subtitle">Gérez vos interventions, demandes et réparations en toute efficacité</p>
    </div>

    <!-- Top Summary Cards -->
    <div class="dashboard-grid">
        <div class="card loading">
            <div class="section-header"><i class="fas fa-chart-pie"></i> Vue système (4 métriques clés)</div>
            <div class="summary-cards">
                <div class="summary-card equipments"><i class="fas fa-users"></i><h3><?= $stats['totalUsers'] ?? 0 ?></h3><p>Utilisateurs totaux</p></div>
                <div class="summary-card broken"><i class="fas fa-desktop"></i><h3><?= $stats['totalEquipments'] ?? 0 ?></h3><p>Équipements totaux</p></div>
                <div class="summary-card requests"><i class="fas fa-clipboard-list"></i><h3><?= $stats['todayClaims'] ?? 0 ?></h3><p>Réclamations du jour</p></div>
                <div class="summary-card claims"><i class="fas fa-exclamation-circle"></i><h3><?= $stats['openClaims'] ?? 0 ?></h3><p>Réclamations ouvertes</p></div>
            </div>
        </div>

        <div class="card activities-section loading">
            <div class="section-header"><i class="fas fa-bolt"></i> Actions rapides</div>
            <div class="timeline">
                <a href="<?= BASE_URL ?>admin/create-user" class="timeline-item timeline-button">
                    <div class="timeline-icon request"><i class="fas fa-user-plus"></i></div>
                    <div class="timeline-content">
                        <h4>Créer utilisateur</h4>
                        <p>Ajouter un nouvel utilisateur au système</p>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>admin/add-equipment" class="timeline-item timeline-button">
                    <div class="timeline-icon addEquipent"><i class="fas fa-desktop"></i></div>
                    <div class="timeline-content">
                        <h4>Ajouter matériel</h4>
                        <p>Enregistrer un nouvel équipement dans l'inventaire</p>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>admin/quick-report" class="timeline-item timeline-button">
                    <div class="timeline-icon claim"><i class="fas fa-chart-bar"></i></div>
                    <div class="timeline-content">
                        <h4>Rapport rapide</h4>
                        <p>Générer un rapport d'activité du système</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Side by Side Chart & Alerts -->
    <div class="side-by-side-container">
        <div class="card loading">
            <div class="section-header"><i class="fas fa-chart-pie"></i> Répartition des types de réclamation</div>
            <div class="chart-container">
                <canvas id="donutChart" width="300px" height="300px"></canvas>
            </div>
            <div class="chart-legend">
                <?php 
                $totalReclamations = array_sum($reclamationTypes);
                foreach ($reclamationTypes as $type => $count): 
                    if ($count > 0):
                        $percentage = $totalReclamations > 0 ? round(($count / $totalReclamations) * 100) : 0;
                ?>
                <div class="legend-item">
                    <span class="legend-color" style="background-color: <?= $type === 'hardware' ? '#3b82f6' : ($type === 'software' ? '#8b5cf6' : '#f59e0b') ?>;"></span>
                    <span class="legend-label"><?= ucfirst($type) ?> (<?= $percentage ?>%)</span>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>

        <div class="card alerts-section loading">
            <div class="section-header"><i class="fas fa-bell"></i> Alertes prioritaires</div>
            <?php if (!empty($alerts)): ?>
                <?php foreach ($alerts as $alert): ?>
                <div class="alert-item <?= $alert['type'] ?>">
                    <div class="alert-icon"><i class="fas <?= $alert['icon'] ?>" style="color: var(--<?= $alert['type'] ?>)"></i></div>
                    <div class="alert-content">
                        <h4><?= $alert['title'] ?></h4>
                        <p><?= $alert['message'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert-item success">
                    <div class="alert-icon"><i class="fas fa-check-circle" style="color: var(--success)"></i></div>
                    <div class="alert-content">
                        <h4>Aucune alerte</h4>
                        <p>Tout fonctionne normalement</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Inclure le script JavaScript spécifique à l'admin -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"></script>
    
<script>
    // Données pour le graphique
    const reclamationData = <?= json_encode($reclamationTypes) ?>;
</script>