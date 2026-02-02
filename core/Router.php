<?php
// /core/Router.php

require_once(__DIR__ . '/../app/helpers/RouteHelper.php');

class Router {
    public static function route() {
        $base = rtrim(BASE_URL, '/');
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $path = preg_replace("#^$base#", '', $uri);
        $path = '/' . ltrim($path, '/');
        
        // Routes définies avec leurs contrôleurs et méthodes
        $routes = [
            // Auth routes
            '/login' => ['AuthController', 'login'],
            '/logout' => ['AuthController', 'logout'],
            
            // Dashboard routes
            '/dashboard' => ['DashboardController', 'index'],
            '/' => ['DashboardController', 'index'],
            
            // Equipements routes
            '/equipements/liste' => ['EquipementsController', 'liste'],
            '/equipements/ajouter' => ['EquipementsController', 'ajouter'],
            '/equipements/gerer' => ['EquipementsController', 'gerer'],    
                    
            // Réclamations routes
            '/reclamations/nouvelle' => ['ReclamationsController', 'nouvelle'],
            '/reclamations/suivi' => ['ReclamationsController', 'suivi'],
            '/reclamations/traiter' => ['ReclamationsController', 'traiter'],
            '/reclamations/superviser' => ['ReclamationsController', 'superviser'],
            
            // Profil routes
            '/profil/informations' => ['ProfilController', 'informations'],
            '/profil/mot-de-passe' => ['ProfilController', 'motDePasse'],
            
            // Paramètres routes
            '/parametres' => ['ParametresController', 'index'],

            // Routes pour la gestion des utilisateurs
            '/utilisateurs/gerer' => ['UtilisateursController', 'gerer'],
            '/utilisateurs/ajouter' => ['UtilisateursController', 'ajouter'],

            // Routes pour la gestion de l'infrastructure
            '/infrastructure' => ['InfrastructureController', 'gerer'] ,
            
            // Routes pour les notifications
            '/notifications/marquer-toutes-lues' => ['NotificationController', 'markAllAsRead'],
            '/notifications/marquer-lue/(.+)' => ['NotificationController', 'markAsRead'],
            
        ];
        
        // Vérifier d'abord les routes exactes
        if (isset($routes[$path])) {
            list($controller, $method) = $routes[$path];
            self::callController($controller, $method);
            return;
        }

        // Et cette route pour le traitement POST
        if ($path === '/equipements/ajouter' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            self::callController('EquipementsController', 'ajouter');
            return;
        }

        if ($path === '/equipements/ajouter' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            self::callController('EquipementsController', 'ajouter');
            return;
        }

      
        // Vérifier les routes paramétrées
        if (preg_match('#^/equipements/details/(\d+)$#', $path, $matches)) {
            self::callController('EquipementsController', 'details', [$matches[1]]);
            return;
        }
        
        if ($path === '/equipements/assigner' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            self::callController('EquipementsController', 'assigner');
            return;
        }
        
        if (preg_match('#^/equipements/liberer/(\d+)$#', $path, $matches)) {
            self::callController('EquipementsController', 'liberer', [$matches[1]]);
            return;
        }
        
        // Si aucune route ne correspond
        self::notFound($path);
    }
    
    private static function callController($controller, $method, $params = []) {
        $controllerFile = __DIR__ . '/../app/controllers/' . $controller . '.php';
        
        if (file_exists($controllerFile)) {
            require_once($controllerFile);
            
            if (class_exists($controller) && method_exists($controller, $method)) {
                $controllerInstance = new $controller();
                call_user_func_array([$controllerInstance, $method], $params);
                return;
            }
        }
        
        self::notFound("Controller: $controller, Method: $method");
    }
    
    private static function notFound($path) {
        http_response_code(404);
        echo "404 Not Found - Page non trouvée: " . htmlspecialchars($path);
        exit;
    }
}
?>