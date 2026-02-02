<?php
require_once(__DIR__ . '/../models/Dashboard.php');
require_once(__DIR__ . '/../../core/Controller.php');

class DashboardController extends Controller {
    public function index() {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }

        $role = $_SESSION['role'];
        $userId = $_SESSION['id'];
        $model = new Dashboard();
        
        // Récupérer les données spécifiques au dashboard
        $dashboardData = $this->getDashboardData($role, $userId);
        
        // Préparer les données pour la vue
        $data = array_merge($dashboardData, [
            'page_title' => 'Tableau de bord - ' . ucfirst($role),
            'currentPage' => 'dashboard',
            'additional_css' => ['dashboard.css', 'dashboard_' . $role . '.css'],
            'additional_js' => ['dashboard.js', 'dashboard_' . $role . '.js']
        ]);
        
        // Inclure Chart.js uniquement pour l'admin
        if ($role === 'admin') {
            $data['additional_js'][] = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
            $data['additional_js'][] = 'https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js';
        }
        
        // Utiliser le layout spécifique au rôle du dashboard
        $this->main('dashboard/' . $role, $data);
    }

    private function getDashboardData($role, $userId) {
        $model = new Dashboard();
        switch ($role) {
            case 'admin':
                return $model->getAdminDashboardData($userId);
            case 'agent':
                return $model->getAgentDashboardData($userId);
            case 'support':
                return $model->getSupportDashboardData($userId);
            case 'technicien':
                return $model->getTechnicienDashboardData($userId);
            default:
                return [];
        }
    }
}
?>