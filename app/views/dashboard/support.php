<?php
// Récupération des données du tableau de bord pour le support
$dashboardData = $data ?? [];
?>

<div class="dashboard-header">
    <h1 class="greeting">Bienvenue, Support <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?> <?= htmlspecialchars($_SESSION['nom'] ?? '') ?></h1>
    <p class="subtitle">Gérez vos interventions et réclamations en toute efficacité</p>
</div>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Résumé Rapide -->
    <div class="card loading">
        <div class="section-header">
            <h3><i class="fas fa-chart-pie"></i> Performances du jour</h3>
        </div>
        <div class="summary-cards">
            <div class="summary-card demands">
                <i class="fas fa-clipboard-list"></i>
                <h3><?= $dashboardData['todayAssignedClaims'] ?? 0 ?></h3>
                <p>Réclamations assignées aujourd'hui</p>
            </div>
            <div class="summary-card claims">
                <i class="fas fa-cogs"></i>
                <h3><?= $dashboardData['inProgressClaims'] ?? 0 ?></h3>
                <p>Réclamations en cours</p>
            </div>
            <div class="summary-card completed">
                <i class="fas fa-check-circle"></i>
                <h3><?= $dashboardData['resolvedTodayClaims'] ?? 0 ?></h3>
                <p>Réclamations résolues aujourd'hui</p>
            </div>
            <div class="summary-card specialty">
                <i class="fas fa-chart-line"></i>
                <h3><?= $dashboardData['resolutionRate'] ?? 0 ?>%</h3>
                <p>Taux de résolution ce mois</p>    
            </div>
        </div>
    </div>
    
    <!-- Réclamations Non Assignées -->
    <div class="card activities-section loading">
        <div class="section-header">
            <h3><i class="fas fa-clock"></i> Réclamations en attente</h3>
            <a href="<?= BASE_URL ?>reclamations" class="view-all">Voir toutes</a>
        </div>
        <div class="timeline">
            <?php if (!empty($dashboardData['unassignedClaims'])): ?>
                <?php foreach ($dashboardData['unassignedClaims'] as $claim): ?>
                    <a href="<?= BASE_URL ?>reclamations/details/<?= $claim['id'] ?>" class="timeline-item timeline-button">
                        <div class="timeline-icon request">
                            <i class="fas fa-<?= $claim['type'] === 'hardware' ? 'desktop' : 'code' ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h4><?= htmlspecialchars($claim['motif']) ?></h4>
                            <p>
                                <?= htmlspecialchars($claim['user_name'] ?? 'Utilisateur inconnu') ?> - 
                                <?= htmlspecialchars($claim['service_name'] ?? 'Service inconnu') ?>
                                <br><small>Matériel: <?= htmlspecialchars($claim['serial_number'] ?? 'N/A') ?></small>
                                <?php if (!empty($claim['date_reclamation'])): ?>
                                    <br><small>Signalé le: <?= date('d/m/Y', strtotime($claim['date_reclamation'])) ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <p>Aucune réclamation à assigner pour le moment</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mes Réclamations en Cours -->
    <div class="card activities-section loading">
        <div class="section-header">
            <h3><i class="fas fa-list-ul"></i> Mes Réclamations en Cours</h3>
            <a href="<?= BASE_URL ?>reclamations/mes-reclamations" class="view-all">Voir toutes</a>
        </div>
        <div class="timeline">
            <?php if (!empty($dashboardData['myClaimsInProgress'])): ?>
                <?php foreach ($dashboardData['myClaimsInProgress'] as $claim): ?>
                    <a href="<?= BASE_URL ?>reclamations/details/<?= $claim['id'] ?>" class="timeline-item timeline-button">
                        <div class="timeline-icon claim">
                            <i class="fas fa-<?= $claim['type'] === 'hardware' ? 'tools' : 'bug' ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h4><?= htmlspecialchars($claim['motif']) ?></h4>
                            <p>
                                <?= htmlspecialchars($claim['user_name'] ?? 'Utilisateur inconnu') ?> - 
                                <?= htmlspecialchars($claim['service_name'] ?? 'Service inconnu') ?>
                                <br><small>Matériel: <?= htmlspecialchars($claim['serial_number'] ?? 'N/A') ?></small>
                                <?php if (!empty($claim['date_reclamation'])): ?>
                                    <br><small>Assignée le: <?= date('d/m/Y', strtotime($claim['date_reclamation'])) ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <p>Aucune réclamation en cours pour le moment</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alertes & Interventions Prioritaires -->
<div class="card alerts-section full-width-section loading">
    <div class="section-header">
        <h3><i class="fas fa-bell"></i> Alertes & Interventions Prioritaires</h3>
    </div>
    
    <?php if (!empty($dashboardData['alerts'])): ?>
        <?php foreach ($dashboardData['alerts'] as $alert): ?>
            <div class="alert-item <?= $alert['type'] ?>">
                <div class="alert-icon">
                    <i class="fas <?= $alert['icon'] ?>" style="color: var(--<?= $alert['type'] ?>);"></i>
                </div>
                <div class="alert-content">
                    <h4><?= htmlspecialchars($alert['title']) ?></h4>
                    <p><?= htmlspecialchars($alert['message']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert-item success">
            <div class="alert-icon">
                <i class="fas fa-check-circle" style="color: var(--success);"></i>
            </div>
            <div class="alert-content">
                <h4>Aucune alerte critique</h4>
                <p>Tous les systèmes fonctionnent normalement.</p>
            </div>
        </div>
    <?php endif; ?>
</div>