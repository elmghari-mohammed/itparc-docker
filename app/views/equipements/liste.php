<?php
// /app/views/equipements/liste.php

// Méthodes utilitaires pour la vue
function getEquipmentIcon($type) {
    if (stripos($type, 'portable') !== false) return 'laptop';
    if (stripos($type, 'imprimante') !== false) return 'print';
    if (stripos($type, 'projecteur') !== false) return 'video';
    return 'desktop';
}

function getEquipmentClass($type) {
    if (stripos($type, 'portable') !== false) return 'pc-portable';
    if (stripos($type, 'imprimante') !== false) return 'imprimante';
    if (stripos($type, 'projecteur') !== false) return 'projecteur';
    return 'pc-bureau';
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'broken': return 'broken';
        case 'warning': return 'warning';
        case 'working': return 'working';
        default: return 'working';
    }
}

function getStatusIcon($status) {
    switch ($status) {
        case 'broken': return 'times-circle';
        case 'warning': return 'exclamation-circle';
        case 'working': return 'check-circle';
        default: return 'check-circle';
    }
}
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Liste des Équipements</h1>
    <p class="subtitle">Vos équipements assignés et leur état actuel</p>
</div>

<div class="equipment-grid">
    <?php if (empty($equipements)): ?>
        <div class="no-equipment">
            <i class="fas fa-laptop"></i>
            <h3>Aucun équipement assigné</h3>
            <p>Vous n'avez actuellement aucun équipement assigné à votre compte.</p>
        </div>
    <?php else: ?>
        <?php foreach ($equipements as $equip): 
            $equipmentInfo = $equipmentData[$equip['id']];
            $statusClass = getStatusBadgeClass($equipmentInfo['status']);
            $statusIcon = getStatusIcon($equipmentInfo['status']);
        ?>
            <div class="equipment-card">
                <div class="info-circle" onclick="showDetails(<?= $equip['id'] ?>)">
                    <i class="fas fa-info"></i>
                </div>
                
                <div class="equipment-header">
                    <div class="equipment-icon <?= getEquipmentClass($equip['type_nom']) ?>">
                        <i class="fas fa-<?= getEquipmentIcon($equip['type_nom']) ?>"></i>
                    </div>
                    <div class="equipment-info">
                        <h3><?= htmlspecialchars($equip['marque'] . ' ' . $equip['model']) ?></h3>
                        <div class="equipment-type"><?= htmlspecialchars($equip['type_nom']) ?></div>
                    </div>
                </div>
                
                <div class="equipment-details">
                    <div class="detail-row">
                        <span class="detail-label">Marque</span>
                        <span class="detail-value"><?= htmlspecialchars($equip['marque']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Modèle</span>
                        <span class="detail-value"><?= htmlspecialchars($equip['model']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">État</span>
                        <span class="status-badge <?= $statusClass ?>">
                            <i class="fas fa-<?= $statusIcon ?>"></i>
                            <?= htmlspecialchars($equipmentInfo['libelleEtat']) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Overlay pour les détails -->
<div class="details-overlay" id="detailsOverlay"></div>

<script>
// Données des équipements
const equipmentData = <?= json_encode($equipmentData) ?>;
</script>