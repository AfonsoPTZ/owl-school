<?php

require_once __DIR__ . '/../../../db/conexao.php';

use App\Controllers\UtilsAlunoController;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();

$controller = new UtilsAlunoController($conn);

// Rotear por action ou pela função chamada
$action = $_GET['action'] ?? $_POST['action'] ?? 'getNomeResponsavel';

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
