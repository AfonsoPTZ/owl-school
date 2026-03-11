<?php

require_once __DIR__ . '/../../db/conexao.php';

header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
