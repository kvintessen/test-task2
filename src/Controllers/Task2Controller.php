<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\DatabaseConnection;
use App\Exceptions\DatabaseException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use Exception;

class Task2Controller
{
    public function handle(): void
    {
        try {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            header('Content-Type: application/json');

            $db = DatabaseConnection::getConnection();

            if ($db->connect_error) {
                throw new DatabaseException('Database connection failed: ' . $db->connect_error);
            }

            // Валидация входного параметра
            $catId = isset($_GET['cat_id']) && ctype_digit($_GET['cat_id']) ? (int)$_GET['cat_id'] : null;
            if ($catId === null) {
                throw new ValidationException('Invalid catalog ID provided.');
            }

            $query = <<<SQL
                SELECT 
                    q.*, 
                    u.name AS user_name, 
                    u.gender AS user_gender
                FROM 
                    questions q
                LEFT JOIN 
                    users u ON u.u_id = q.user_id
                WHERE 
                    q.catalog_id = ?
            SQL;

            $stmt = $db->prepare($query);

            if (!$stmt) {
                throw new Exception('Failed to prepare query: ' . $db->error);
            }

            $stmt->bind_param('i', $catId);

            if (!$stmt->execute()) {
                throw new Exception('Failed to execute query: ' . $stmt->error);
            }

            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (empty($rows)) {
                throw new NotFoundException('No data found for the given catalog ID.');
            }

            $response = array_map(function ($row) {
                return [
                    'question' => [
                        'q_id' => $row['q_id'],
                        'text' => $row['text'],
                        'catalog_id' => $row['catalog_id'],
                    ],
                    'user' => [
                        'name' => $row['user_name'],
                        'gender' => $row['user_gender'],
                    ],
                ];
            }, $rows);

            echo json_encode($response);
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