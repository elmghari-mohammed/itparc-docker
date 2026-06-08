<?php
// Configuration de base
define('BASE_URL', '/itparc/');
define('ROOT_PATH', dirname(__DIR__));
define('SESSION_NAME', 'itparc_session');

// Database configuration - uses MYSQL_* env vars (single source of truth)
// No fallback values - credentials must come from environment
define('DB_HOST', getenv('MYSQL_HOST') ?: 'db');
define('DB_PORT', getenv('MYSQL_PORT') ?: '3306');
define('DB_NAME', getenv('MYSQL_DATABASE'));
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASS', getenv('MYSQL_PASSWORD'));
?>
