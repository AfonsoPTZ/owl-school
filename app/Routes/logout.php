<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../db/conexao.php';
use App\Http\Controllers\AuthController;

header('Content-Type: application/json; charset=utf-8');

$controller = new AuthController($conn);
$controller->logout();
