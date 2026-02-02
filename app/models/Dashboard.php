<?php
require_once(__DIR__ . '/../../core/Model.php');

class Dashboard extends Model {
    // Pas de $table défini car cette classe utilise plusieurs tables
    // Elle utilisera les méthodes de requête personnalisées

    public function __construct() {
        parent::__construct(); // Initialise la connexion DB via Model
    }

    public function getAgentDashboardData($agentId) {
        // Équipements assignés à l'agent
        $equipmentsAssigned = $this->fetchColumn(
            "SELECT COUNT(*) AS equipmentsAssigned FROM materiel WHERE id_user = :id AND user_role = 'agent'",
            ['id' => $agentId]
        );
    
        // Réclamations ouvertes de l'agent
        $openClaims = $this->fetchColumn(
            "SELECT COUNT(*) AS openClaims FROM reclamation WHERE id_user = :id AND user_role = 'agent' AND statut = 'en_attente'",
            ['id' => $agentId]
        );
    
        // Réclamations en cours de l'agent
        $inProgressClaims = $this->fetchColumn(
            "SELECT COUNT(*) AS inProgressClaims FROM reclamation WHERE id_user = :id AND user_role = 'agent' AND statut = 'en_cours'",
            ['id' => $agentId]
        );
    
        // Réclamations résolues de l'agent (ce mois)
        $currentMonth = date('Y-m');
        $resolvedClaims = $this->fetchColumn(
            "SELECT COUNT(*) AS resolvedClaims FROM reclamation WHERE id_user = :id AND user_role = 'agent' AND statut IN ('resolu', 'ferme') AND DATE_FORMAT(date_resolution, '%Y-%m') = :current_month",
            ['id' => $agentId, 'current_month' => $currentMonth]
        );
    
        // Dernières réclamations de l'agent
        $recentClaims = $this->fetchAll("
            SELECT r.id, r.motif, r.type, r.date_reclamation, r.statut,
                   m.serial_number, m.model, m.marque
            FROM reclamation r
            JOIN materiel m ON r.materiel_id = m.id
            WHERE r.id_user = :id AND r.user_role = 'agent'
            ORDER BY r.date_reclamation DESC
            LIMIT 5
        ", ['id' => $agentId]);
    
        // Dernières notifications de l'agent
        $notifications = $this->fetchAll("
            SELECT sujet, message, date_creation, est_lue
            FROM notification 
            WHERE user_role = 'agent' AND user_id = :id
            ORDER BY date_creation DESC 
            LIMIT 5
        ", ['id' => $agentId]);
    
        return compact('equipmentsAssigned', 'openClaims', 'inProgressClaims', 'resolvedClaims', 'recentClaims', 'notifications');
    }

    public function getAdminDashboardData($adminId) {
        // Statistiques générales
        $stats = [];
        
        // Nombre total d'utilisateurs
        $stats['totalUsers'] = $this->fetchColumn("
            SELECT COUNT(*) AS total FROM (
                SELECT matricul FROM agent
                UNION ALL SELECT matricul FROM support
                UNION ALL SELECT matricul FROM admin
            ) AS all_users
        ");

        // Équipements totaux
        $stats['totalEquipments'] = $this->fetchColumn("SELECT COUNT(*) FROM materiel");

        // Réclamations du jour
        $today = date('Y-m-d');
        $stats['todayClaims'] = $this->fetchColumn(
            "SELECT COUNT(*) FROM reclamation WHERE DATE(date_reclamation) = :today",
            ['today' => $today]
        );

        // Réclamations ouvertes
        $stats['openClaims'] = $this->fetchColumn("SELECT COUNT(*) FROM reclamation WHERE statut = 'en_attente'");

        // Répartition des types de réclamation
        $reclamationTypes = [];
        $results = $this->fetchAll(
            "SELECT type, COUNT(*) as count FROM reclamation WHERE statut != 'ferme' GROUP BY type"
        );
        
        foreach ($results as $row) {
            $type = $row['type'] ?: 'autre';
            $reclamationTypes[$type] = (int)$row['count'];
        }

        // Alertes prioritaires
        $alerts = [];
        
        // Réclamations urgentes non traitées
        $urgentCount = $this->fetchColumn("SELECT COUNT(*) FROM reclamation WHERE statut = 'en_attente' AND motif LIKE '%urgent%'");
        if ($urgentCount > 0) {
            $alerts[] = [
                'type' => 'error',
                'icon' => 'fa-fire',
                'title' => 'Urgences non traitées',
                'message' => $urgentCount . ' réclamations urgentes nécessitent une attention immédiate'
            ];
        }

        // Garanties expirées
        $today = date('Y-m-d');
        $expiredWarranty = $this->fetchColumn(
            "SELECT COUNT(*) FROM materiel WHERE garantie_fin < :today",
            ['today' => $today]
        );
        if ($expiredWarranty > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-clock',
                'title' => 'Garanties expirées',
                'message' => $expiredWarranty . ' équipements avec garantie expirée nécessitent vérification'
            ];
        }

        // Comptes à valider
        $accountsToValidate = $this->fetchColumn("SELECT COUNT(*) FROM agent WHERE bloque = 1 OR nombre_tentative >= 3");
        if ($accountsToValidate > 0) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'fa-user-check',
                'title' => 'Comptes à valider',
                'message' => $accountsToValidate . ' comptes utilisateur nécessitent une attention'
            ];
        }

        // Réclamations sans support assigné
        $unassignedClaims = $this->fetchColumn("SELECT COUNT(*) FROM reclamation WHERE statut = 'en_attente' AND support_matricul IS NULL");
        if ($unassignedClaims > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-user-times',
                'title' => 'Réclamations non assignées',
                'message' => $unassignedClaims . ' réclamations en attente d\'assignation à un support'
            ];
        }

        return [
            'stats' => $stats,
            'reclamationTypes' => $reclamationTypes,
            'alerts' => $alerts
        ];
    }

    public function getSupportDashboardData($supportId) {
        $data = [];
        
        // Réclamations assignées aujourd'hui
        $today = date('Y-m-d');
        $data['todayAssignedClaims'] = $this->fetchColumn(
            "SELECT COUNT(*) AS today_assigned FROM reclamation WHERE support_matricul = :id AND DATE(date_reclamation) = :today",
            ['id' => $supportId, 'today' => $today]
        );
        
        // Réclamations en cours
        $data['inProgressClaims'] = $this->fetchColumn(
            "SELECT COUNT(*) AS in_progress FROM reclamation WHERE support_matricul = :id AND statut = 'en_cours'",
            ['id' => $supportId]
        );
        
        // Réclamations résolues aujourd'hui
        $data['resolvedTodayClaims'] = $this->fetchColumn(
            "SELECT COUNT(*) AS resolved_today FROM reclamation WHERE support_matricul = :id AND DATE(date_resolution) = :today AND statut IN ('resolu', 'ferme')",
            ['id' => $supportId, 'today' => $today]
        );

        // Taux de résolution (ce mois)
        $currentMonth = date('Y-m');
        $totalClaimsThisMonth = $this->fetchColumn(
            "SELECT COUNT(*) FROM reclamation WHERE support_matricul = :id AND DATE_FORMAT(date_reclamation, '%Y-%m') = :current_month",
            ['id' => $supportId, 'current_month' => $currentMonth]
        );
        
        $resolvedClaimsThisMonth = $this->fetchColumn(
            "SELECT COUNT(*) FROM reclamation WHERE support_matricul = :id AND statut IN ('resolu', 'ferme') AND DATE_FORMAT(date_resolution, '%Y-%m') = :current_month",
            ['id' => $supportId, 'current_month' => $currentMonth]
        );
        
        $data['resolutionRate'] = $totalClaimsThisMonth > 0 ? round(($resolvedClaimsThisMonth / $totalClaimsThisMonth) * 100) : 0;

        // Réclamations en attente d'assignation (pour les supports)
        $data['unassignedClaims'] = $this->fetchAll("
            SELECT r.id, r.motif, r.type, r.date_reclamation,
                   m.serial_number, m.model, m.marque,
                   CONCAT(u.prenom, ' ', u.nom) AS user_name,
                   s.nom AS service_name
            FROM reclamation r
            JOIN materiel m ON r.materiel_id = m.id
            LEFT JOIN agent u ON r.user_role = 'agent' AND r.id_user = u.matricul
            LEFT JOIN service s ON u.service_id = s.id
            WHERE r.statut = 'en_attente' AND r.support_matricul IS NULL
            ORDER BY r.date_reclamation DESC
            LIMIT 5
        ");
        
        // Mes réclamations en cours
        $data['myClaimsInProgress'] = $this->fetchAll("
            SELECT 
                r.id, r.motif, r.type, r.date_reclamation, r.statut,
                m.serial_number, m.model, m.marque,
                CONCAT(u.prenom, ' ', u.nom) AS user_name,
                s.nom AS service_name
            FROM reclamation r
            JOIN materiel m ON r.materiel_id = m.id
            LEFT JOIN agent u ON r.user_role = 'agent' AND r.id_user = u.matricul
            LEFT JOIN service s ON u.service_id = s.id
            WHERE r.support_matricul = :id AND r.statut = 'en_cours'
            ORDER BY r.date_reclamation DESC
            LIMIT 5
        ", ['id' => $supportId]);
                
        // Alertes et notifications
        $data['alerts'] = [];
        
        // Alertes pour les réclamations urgentes non assignées
        $urgentUnassignedCount = $this->fetchColumn("
            SELECT COUNT(*) AS urgent_unassigned
            FROM reclamation
            WHERE statut = 'en_attente' AND support_matricul IS NULL AND (motif LIKE '%urgent%' OR motif LIKE '%important%')
        ");
        
        if ($urgentUnassignedCount > 0) {
            $data['alerts'][] = [
                'type' => 'warning',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'Réclamations urgentes non assignées',
                'message' => $urgentUnassignedCount . ' réclamation(s) urgente(s) en attente d\'assignation'
            ];
        }
        
        // Réclamations en retard (plus de 3 jours)
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        $overdueClaims = $this->fetchColumn("
            SELECT COUNT(*) AS overdue_claims
            FROM reclamation
            WHERE support_matricul = :id AND statut = 'en_cours' AND date_reclamation < :three_days_ago
        ", ['id' => $supportId, 'three_days_ago' => $threeDaysAgo]);
        
        if ($overdueClaims > 0) {
            $data['alerts'][] = [
                'type' => 'error',
                'icon' => 'fa-clock',
                'title' => 'Réclamations en retard',
                'message' => $overdueClaims . ' réclamation(s) en cours depuis plus de 3 jours'
            ];
        }
        
        // Message positif si tout est sous contrôle
        if (empty($data['alerts'])) {
            $data['alerts'][] = [
                'type' => 'success',
                'icon' => 'fa-check-circle',
                'title' => 'Aucune alerte critique',
                'message' => 'Tous les systèmes fonctionnent normalement'
            ];
        }
        
        return $data;
    }
}
?>