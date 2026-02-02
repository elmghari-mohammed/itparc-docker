<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

$currentTime = time();
$endOfToday20h = strtotime('today 20:00');
$sessionLifetime = $endOfToday20h - $currentTime;

// Garantir une durée minimale de 1 heure (3600 secondes)
if ($sessionLifetime < 3600) {
    $sessionLifetime = 3600;
}

ini_set('session.gc_maxlifetime', $sessionLifetime);
session_set_cookie_params($sessionLifetime);

// Inclure la configuration
require_once(__DIR__ . '/config/config.php');

// Inclure le routeur
require_once(__DIR__ . '/core/Router.php');

// Démarrer le routage
Router::route();
?>