<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/conexao.php';

use App\Http\Controllers\UtilsController;
use App\Http\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();

$controller = new UtilsController($conn);

$action = $_GET['action'] ?? 'getName';

switch ($action) {
    case 'getName':
        $controller->getName();
        break;
    case 'getAlunoSelect':
        $controller->getAlunoSelect();
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação inválida.'
        ]);
        break;
}


