<?php
declare(strict_types=1);

namespace App\models;

use PDO;
use RuntimeException;


/**
 * Responsible only for establishing a validated PDO connection.
 * Fails fast if any required environment variable is missing.
 */
final class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $host = getenv('MYSQL_HOST');
        $db   = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER');
        $pass = getenv('MYSQL_PASSWORD');

        // ---- strict validation -------------------------------------------------
        foreach (['MYSQL_HOST' => $host, 'MYSQL_DATABASE' => $db,
                     'MYSQL_USER' => $user, 'MYSQL_PASSWORD' => $pass] as $var => $value) {
            if ($value === false || $value === '' ) {
                throw new RuntimeException("Missing or empty environment variable: {$var}");
            }
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $host,
            $db
        );

        $this->pdo = new PDO(
            $dsn,
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}