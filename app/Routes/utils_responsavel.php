<?php

require_once __DIR__ . '/../../db/conexao.php';

use App\Http\Controllers\UtilsResponsavelController;
use App\Http\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();

$controller = new UtilsResponsavelController($conn);

$action = $_GET['action'] ?? 'getNomeFilho';

switch ($action) {
    case 'getNomeFilho':
        $controller->getNomeFilho();
        break;
    case 'getAdvertenciasFilho':
        $controller->getAdvertenciasFilho();
        break;
    case 'getFrequenciasFilho':
        $controller->getFrequenciasFilho();
        break;
    case 'getNotasFilho':
        $controller->getNotasFilho();
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação inválida.'
        ]);
        break;
}


