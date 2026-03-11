<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

use App\Controllers\AdvertenciaController;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();
AuthMiddleware::requireRole('admin');

$controller = new AdvertenciaController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $controller->index();
        break;

    case 'POST':
        $controller->create();
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $_POST = is_array($input) ? $input : $_POST;
        $controller->update();
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $_POST = is_array($input) ? $input : $_POST;
        $controller->delete();
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido.'
        ]);
        break;
}
