<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = getenv('DB_HOST') ?: 'mysql';
                $port = getenv('DB_PORT') ?: '3306';
                $dbname = getenv('DB_NAME') ?: 'pdgp_db';
                $username = getenv('DB_USER') ?: 'pdgp_user';
                $password = getenv('DB_PASSWORD') ?: 'pdgp_secure_password';

                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }
        }

        return self::$connection;
    }

    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}

