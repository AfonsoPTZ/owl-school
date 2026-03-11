<?php

require_once __DIR__ . '/../../db/conexao.php';
use App\Controllers\AuthController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$controller = new AuthController($conn);
$controller->logout();
