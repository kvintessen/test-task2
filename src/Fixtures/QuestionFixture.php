<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Database\DatabaseConnection;
use Exception;

class QuestionFixture implements FixtureInterface
{
    /**
     * @throws Exception
     */
    public static function load(): void
    {
        $db = DatabaseConnection::getConnection();
        $tableCheckQuery = <<<SQL
SHOW TABLES LIKE 'questions'
SQL;
        $result = $db->query($tableCheckQuery);

        if ($result->num_rows === 0) {
            $createTableQuery = <<<SQL
    CREATE TABLE questions (
        q_id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT NOT NULL,
        catalog_id INT NOT NULL,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(u_id)
    )
    SQL;

            if (!$db->query($createTableQuery)) {
                throw new Exception('Error creating table: ' . $db->error);
            }

            $questions = [
                ['text' => 'What is your favorite color?', 'catalog_id' => 1, 'user_id' => 1],
                ['text' => 'How do you spend your free time?', 'catalog_id' => 1, 'user_id' => 2],
                ['text' => 'What is your dream job?', 'catalog_id' => 2, 'user_id' => 3],
                ['text' => 'What is your favorite book?', 'catalog_id' => 2, 'user_id' => 4],
                ['text' => 'Where do you want to travel?', 'catalog_id' => 3, 'user_id' => 5],
            ];

            foreach ($questions as $question) {
                $insertQuery = "INSERT INTO questions (text, catalog_id, user_id) VALUES (?, ?, ?)";
                $stmt = $db->prepare($insertQuery);
                $stmt->bind_param('sii', $question['text'], $question['catalog_id'], $question['user_id']);

                if (!$stmt->execute()) {
                    throw new Exception('Error inserting data: ' . $stmt->error);
                }
            }
        }
    }
}