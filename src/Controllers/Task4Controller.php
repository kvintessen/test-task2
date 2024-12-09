<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\DatabaseConnection;
use Exception;
use mysqli;

class Task4Controller
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $db = DatabaseConnection::getConnection();

            $this->showEmployeesWithMaxSalary($db);
            $this->showEmployeesMoreSalaryThan($db);

            $queryListIndex = <<<SQL
CREATE INDEX idx_departament_id ON employees (departament_id);
CREATE INDEX idx_salary ON employees (salary);
CREATE INDEX idx_departament_salary ON employees (departament_id, salary);
SQL;
            echo 'REQUEST';
            echo "<pre>$queryListIndex</pre>";


            DatabaseConnection::closeConnection();

        } catch (Exception $e) {
            http_response_code(500);
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    private function showEmployeesWithMaxSalary(mysqli $db): void
    {
        $queryMaxSalary = <<<SQL
    SELECT 
        e.departament_id,
        MAX(e.salary) AS max_salary
    FROM 
        employees e
    GROUP BY 
        e.departament_id;
    SQL;

        echo 'REQUEST';
        echo "<pre>$queryMaxSalary</pre>";

        $result = $db->query($queryMaxSalary);

        if (!$result) {
            throw new Exception('Query execution failed: ' . $db->error);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        echo 'RESULT: ';
        echo '<pre>';
        foreach ($rows as $row) {
            echo 'Departament ID: ' . $row['departament_id'] . ', Max Salary: ' . $row['max_salary'] . PHP_EOL;
        }
        echo '</pre>';
    }

    /**
     * @throws Exception
     */
    private function showEmployeesMoreSalaryThan(mysqli $db): void
    {
        $querySalaryMoreThan = <<<SQL
SELECT 
    e.id,
    e.name,
    e.lastname,
    e.departament_id,
    e.salary
FROM 
    employees e
WHERE 
    departament_id = 3 AND salary > 90000;
SQL;
        echo 'REQUEST';
        echo "<pre>$querySalaryMoreThan</pre>";

        $result = $db->query($querySalaryMoreThan);

        if (!$result) {
            throw new Exception('Query execution failed: ' . $db->error);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        echo 'RESULT: ';
        echo '<pre>';
        foreach ($rows as $row) {
            echo 'Employee ID: ' . $row['id']
                . ', Name: ' . $row['name']
                . ', Lastname: ' . $row['lastname']
                . ', Departament ID: ' . $row['departament_id']
                . ', Salary: ' . $row['salary']
                . PHP_EOL;
        }
        echo '</pre>';
    }
}