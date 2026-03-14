<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../db/conexao.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'tipo_usuario' => $_SESSION['tipo_usuario']
    ]
]);
