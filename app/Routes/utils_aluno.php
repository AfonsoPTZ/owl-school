<?php

require_once __DIR__ . '/../../db/conexao.php';

use App\Http\Controllers\UtilsAlunoController;
use App\Http\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();

$controller = new UtilsAlunoController($conn);

$action = $_GET['action'] ?? 'getNomeResponsavel';

switch ($action) {
    case 'getNomeResponsavel':
        $controller->getNomeResponsavel();
        break;
    case 'getAdvertencias':
        $controller->getAdvertencias();
        break;
    case 'getFrequencias':
        $controller->getFrequencias();
        break;
    case 'getNotas':
        $controller->getNotas();
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação inválida.'
        ]);
        break;
}


