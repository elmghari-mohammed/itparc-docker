<?php
// /core/RouterHelper.php

class RouterHelper {
    public static function getCurrentPage() {
        $base = rtrim(BASE_URL, '/');
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $path = preg_replace("#^$base#", '', $uri);
        $path = '/' . ltrim($path, '/');
        
        // Mapping des routes vers les noms de page
        $routeToPageMap = [
            '/dashboard' => 'accueil',
            '/' => 'accueil',
            '/login' => 'login',
            '/logout' => 'logout',
            '/equipements/liste' => 'equipements-liste',
            '/equipements/gerer' => 'equipements-gerer',
            '/equipements/ajouter' => 'equipements-ajouter',
            '/reclamations/nouvelle' => 'reclamations-nouvelle',
            '/reclamations/suivi' => 'reclamations-suivi',
            '/reclamations/superviser' => 'reclamations-superviser',
            '/reclamations/traiter' => 'reclamations-traiter',
            '/profil/informations' => 'profil-informations',
            '/profil/mot-de-passe' => 'profil-mot-de-passe',
            '/parametres' => 'parametres',
            '/utilisateurs/gerer' => 'utilisateurs-gerer',
            '/utilisateurs/ajouter' => 'utilisateurs-ajouter',
            '/infrastructure'=>'infrastructure-gerer',
            // Ajouter d'autres routes ici si nécessaire
            '/notifications/marquer-toutes-lues' => 'notifications-marquertouteslues',
            '/notifications/marquer-lue/(.+)' => 'notifications-marquerlue'
        ];

        
        // Vérifier les routes paramétrées
        if (preg_match('#^/equipements/details/(\d+)$#', $path)) {
            return 'equipements-details';
        }
        if (preg_match('#^/equipements/liberer/(\d+)$#', $path)) {
            return 'equipements-liste';
        }
        
        // Retourne null si la route n'existe pas (pas de valeur par défaut)
        return $routeToPageMap[$path] ?? null;
    }
    
    public static function getBasePath() {
        return rtrim(BASE_URL, '/');
    }
    
    public static function getFullUrl($path = '') {
        return self::getBasePath() . '/' . ltrim($path, '/');
    }
    
    // Nouvelle méthode pour vérifier si une route existe
    public static function routeExists($path) {
        return self::getCurrentPage() !== null;
    }
}
?>