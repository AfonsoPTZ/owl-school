<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/conexao.php';

use App\Http\Controllers\AIController;
use App\Http\Middleware\AuthMiddleware;

header('Content-Type: application/json');

AuthMiddleware::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$_POST = is_array($input) ? $input : [];

$controller = new AIController($conn);
$controller->chat();