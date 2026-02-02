<div class="dashboard-header">
    <h1 class="greeting">Bienvenue, <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></h1>
    <p class="subtitle">Gérez vos équipements et réclamations en toute simplicité</p>
</div>

<div class="dashboard-grid">
    <!-- Résumé Rapide -->
    <div class="card loading">
        <div class="section-header">
            <h3><i class="fas fa-chart-pie"></i> Résumé Rapide</h3>
        </div>
        <div class="summary-cards">
            <div class="summary-card equipments">
                <i class="fas fa-desktop"></i>
                <h3><?= $data['equipmentsAssigned'] ?? 0 ?></h3>
                <p><?= ($data['equipmentsAssigned'] ?? 0) > 0 ? 'Équipements assignés' : 'Aucun équipement assigné' ?></p>
            </div>
            <div class="summary-card broken">
                <i class="fas fa-exclamation-circle"></i>
                <h3><?= ($data['openClaims'] ?? 0) + ($data['inProgressClaims'] ?? 0) ?></h3>
                <p><?= (($data['openClaims'] ?? 0) + ($data['inProgressClaims'] ?? 0)) > 0 ? 'Réclamations actives' : 'Aucune réclamation active' ?></p>
            </div>
            <div class="summary-card requests">
                <i class="fas fa-clipboard-list"></i>
                <h3><?= $data['openClaims'] ?? 0 ?></h3>
                <p><?= ($data['openClaims'] ?? 0) > 0 ? 'Réclamations en attente' : 'Aucune réclamation en attente' ?></p>
            </div>
            <div class="summary-card claims">
                <i class="fas fa-check-circle"></i>
                <h3><?= $data['resolvedClaims'] ?? 0 ?></h3>
                <p><?= ($data['resolvedClaims'] ?? 0) > 0 ? 'Réclamations résolues (mois)' : 'Aucune réclamation résolue' ?></p>
            </div>
        </div>
    </div>

    <!-- Dernières Activités (Réclamations) -->
    <div class="card activities-section loading">
        <div class="section-header">
            <h3><i class="fas fa-clock"></i> Dernières Réclamations</h3>
        </div>
        <div class="timeline">
            <?php if (empty($data['recentClaims'])) : ?>
                <p class="no-data">Aucune réclamation récente.</p>
            <?php else : ?>
                <?php foreach ($data['recentClaims'] as $claim) : ?>
                    <div class="timeline-item">
                        <div class="timeline-icon <?= $claim['type'] === 'hardware' ? 'hardware' : 'software' ?>">
                            <i class="fas <?= $claim['type'] === 'hardware' ? 'fa-cogs' : 'fa-code' ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h4><?= htmlspecialchars($claim['motif']) ?></h4>
                            <p>
                                <?= htmlspecialchars($claim['type'] === 'hardware' ? 'Matériel' : 'Logiciel') ?> - 
                                <?= htmlspecialchars($claim['statut']) ?>
                            </p>
                            <small>Équipement: <?= htmlspecialchars($claim['model'] . ' ' . $claim['marque']) ?></small>
                        </div>
                        <div class="timeline-date"><?= htmlspecialchars($claim['date_reclamation']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alertes & Notifications -->
    <div class="card alerts-section full-width-section loading">
        <div class="section-header">
            <h3><i class="fas fa-bell"></i> Dernières Notifications</h3>
        </div>
        <?php if (empty($data['notifications'])) : ?>
            <p class="no-data">Aucune notification récente.</p>
        <?php else : ?>
            <?php foreach ($data['notifications'] as $notification) : ?>
                <div class="alert-item <?= $notification['est_lue'] ? 'read' : 'unread' ?>">
                    <div class="alert-icon">
                        <i class="fas fa-bell" style="color: <?= $notification['est_lue'] ? '#6c757d' : '#007bff' ?>;"></i>
                    </div>
                    <div class="alert-content">
                        <h4><?= htmlspecialchars($notification['sujet']) ?></h4>
                        <p><?= htmlspecialchars($notification['message']) ?></p>
                        <small><?= htmlspecialchars($notification['date_creation']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>