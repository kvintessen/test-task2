<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Task1Controller;
use App\Controllers\Task2Controller;
use App\Controllers\Task3Controller;
use App\Controllers\Task4Controller;
use App\Controllers\Task5Controller;
use App\Database\DatabaseConnection;
use App\Fixtures\EmployeeFixture;
use App\Fixtures\QuestionFixture;
use App\Fixtures\UserFixture;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


try {
    $mysqli = DatabaseConnection::getConnection();

    UserFixture::load();
    QuestionFixture::load();
    EmployeeFixture::load();

    DatabaseConnection::closeConnection();
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Unexpected error occurred']);
}

$routes = [
    '/' => Task1Controller::class,
    '/task1' => Task1Controller::class,
    '/task2' => Task2Controller::class,
    '/task3' => Task3Controller::class,
    '/task4' => Task4Controller::class,
    '/task5' => Task5Controller::class,
];

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (isset($routes[$requestUri])) {
    $controllerClass = $routes[$requestUri];
    $controller = new $controllerClass();
    $controller->handle();
} else {
    http_response_code(404);
    echo "404 Not Found";
}