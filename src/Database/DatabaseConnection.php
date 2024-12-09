<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use mysqli;

class DatabaseConnection
{
    private static ?mysqli $connection = null;

    /**
     * @throws Exception
     */
    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'] ?? 'db';
            $port = $_ENV['DB_PORT'] ?? 3306;
            $database = $_ENV['DB_DATABASE'] ?? 'world';
            $username = $_ENV['DB_USERNAME'] ?? 'my_user';
            $password = $_ENV['DB_PASSWORD'] ?? 'my_password';

            try {
                $connection = new mysqli($host, $username, $password, $database, (int)$port);

                if ($connection->connect_error) {
                    throw new Exception('Database connection failed: ' . $connection->connect_error);
                }

                self::$connection = $connection;
            } catch (Exception $e) {
                throw new Exception('Failed to connect to the database: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function closeConnection(): void
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
