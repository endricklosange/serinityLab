<?php

namespace App\Service;

use PDO;


class PdoConnexionService
{
    private $pdo;

    public function __construct()
    {
        $databaseUrl = $_ENV['DATABASE_URL'];

        $databaseConfig = parse_url($databaseUrl);

        $username = $databaseConfig['user'];
        $password = $databaseConfig['pass'];
        $host = $databaseConfig['host'];
        $port = $databaseConfig['port'];
        $dbName = ltrim($databaseConfig['path'], '/');

        // Établir la connexion PDO
        $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $username, $password);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}