<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\DatabaseConnection;
use App\Exceptions\DatabaseException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use Exception;
use mysqli;

class Task1Controller
{
    public function handle(): void
    {
        try {
            $db = DatabaseConnection::getConnection();

            if ($db->connect_error) {
                throw new DatabaseException('Database connection failed: ' . $db->connect_error);
            }

            $id = $_GET['id'] ?? null;

            if (!$id || !ctype_digit($id)) {
                throw new ValidationException('Invalid ID provided.');
            }

            $query = <<<SQL
SELECT * FROM users WHERE u_id = ?
SQL;
            $stmt = $db->prepare($query);

            if (!$stmt) {
                throw new DatabaseException('Failed to prepare statement: ' . $db->error);
            }

            $id = (int)$id;
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new DatabaseException('Query execution failed: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                throw new NotFoundException('User not found.');
            }

            echo 'User: ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8')
                . ' Gender: ' . htmlspecialchars((string)$user['gender'], ENT_QUOTES, 'UTF-8')
                . ' Age: ' . htmlspecialchars((string)$user['age'], ENT_QUOTES, 'UTF-8');

            $stmt->close();
            DatabaseConnection::closeConnection();
        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (NotFoundException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (DatabaseException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            echo json_encode(['error' => 'Internal Server Error']);
        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            echo json_encode(['error' => 'Unexpected error occurred']);
        }
    }
}