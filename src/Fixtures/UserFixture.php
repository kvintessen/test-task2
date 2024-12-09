<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Database\DatabaseConnection;
use Exception;
class UserFixture implements FixtureInterface
{
    /**
     * @throws Exception
     */
    public static function load(): void
    {
        $db = DatabaseConnection::getConnection();

        $tableCheckQuery = <<<SQL
SHOW TABLES LIKE 'users'
SQL;
        $result = $db->query($tableCheckQuery);

        if ($result->num_rows === 0) {
            $createTableQuery = <<<SQL
    CREATE TABLE users (
        u_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(100) NOT NULL UNIQUE,
        age INT NOT NULL,
        gender ENUM('male', 'female') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
    SQL;

            if (!$db->query($createTableQuery)) {
                throw new Exception('Error creating table: ' . $db->error);
            }

            $users = [
                ['name' => 'Alice',   'email' => 'alice@example.com',   'phone' => '+1234567890', 'age' => 25, 'gender' => 'female'],
                ['name' => 'Bob',     'email' => 'bob@example.com',     'phone' => '+1234567891', 'age' => 30, 'gender' => 'male'],
                ['name' => 'Charlie', 'email' => 'charlie@example.com', 'phone' => '+1234567892', 'age' => 35, 'gender' => 'male'],
                ['name' => 'Diana',   'email' => 'diana@example.com',   'phone' => '+1234567893', 'age' => 28, 'gender' => 'female'],
                ['name' => 'Edward',  'email' => 'edward@example.com',  'phone' => '+1234567894', 'age' => 40, 'gender' => 'male'],
            ];

            foreach ($users as $user) {
                $insertQuery = "INSERT INTO users (name, email, phone, age, gender) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($insertQuery);
                $stmt->bind_param('sssis', $user['name'], $user['email'], $user['phone'], $user['age'], $user['gender']);

                if (!$stmt->execute()) {
                    throw new Exception('Error inserting data: ' . $stmt->error);
                }
            }
        }
    }
}