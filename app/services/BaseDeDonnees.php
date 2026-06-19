<?php

namespace App\Services;

use PDO;
use PDOException;

class BaseDeDonnees
{
    private static ?BaseDeDonnees $instance = null;
    private PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): BaseDeDonnees
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
