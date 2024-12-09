<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Database\DatabaseConnection;
use App\Exceptions\DatabaseException;
use Exception;

class EmployeeFixture
{
    /**
     * @throws Exception
     */
    public static function load(): void
    {
        $db = DatabaseConnection::getConnection();

        $tableCheckQuery = "SHOW TABLES LIKE 'employees'";
        $result = $db->query($tableCheckQuery);

        if ($result->num_rows === 0) {

            $createTableQuery = <<<SQL
            CREATE TABLE employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                lastname VARCHAR(100) NOT NULL,
                departament_id INT NOT NULL,
                salary DECIMAL(10, 2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            SQL;

            if (!$db->query($createTableQuery)) {
                throw new Exception('Error creating table: ' . $db->error);
            }

            $indexQueries = [
                "CREATE INDEX idx_departament_id ON employees (departament_id)",
                "CREATE INDEX idx_salary ON employees (salary);",
                "CREATE INDEX idx_departament_salary ON employees (departament_id, salary)",
            ];

            foreach ($indexQueries as $indexQuery) {
                if (!$db->query($indexQuery)) {
                    throw new Exception('Error creating index: ' . $db->error);
                }
            }

            $employees = [
                ['name' => 'Иван',   'lastname' => 'Смирнов',  'departament_id' => 2, 'salary' => 100000],
                ['name' => 'Максим', 'lastname' => 'Петров',   'departament_id' => 2, 'salary' => 90000],
                ['name' => 'Роман',  'lastname' => 'Иванов',   'departament_id' => 3, 'salary' => 95000],
            ];

            foreach ($employees as $employee) {
                $insertQuery = "INSERT INTO employees (name, lastname, departament_id, salary) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($insertQuery);

                if (!$stmt) {
                    throw new Exception('Error preparing query: ' . $db->error);
                }

                $stmt->bind_param(
                    'ssii',
                    $employee['name'],
                    $employee['lastname'],
                    $employee['departament_id'],
                    $employee['salary']
                );

                if (!$stmt->execute()) {
                    throw new DatabaseException('Query execution failed: ' . $stmt->error);
                }
            }
        }
    }
}
