<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Controllers\AdvertenciaController;
use App\Middleware\AuthMiddleware;

AuthMiddleware::requireLogin();
AuthMiddleware::requireRole('admin');

$controller = new AdvertenciaController($conn);
$controller->read();
