<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../db/conexao.php';

use App\Controllers\AuthController;

header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$controller = new AuthController($conn);
$controller->login();