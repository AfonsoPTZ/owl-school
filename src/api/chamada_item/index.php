<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../db/conexao.php';

use App\Controllers\ChamadaItemController;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();
AuthMiddleware::requireRole('professor');

$controller = new ChamadaItemController($conn);
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
        $_POST = is_array($input) ? $input : [];
        $controller->update();
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $_POST = is_array($input) ? $input : [];
        $controller->delete();
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido.'
        ]);
        break;
}
