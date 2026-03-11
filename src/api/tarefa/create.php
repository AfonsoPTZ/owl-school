<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

use App\Controllers\TarefaController;
use App\Middleware\AuthMiddleware;

AuthMiddleware::requireLogin();
AuthMiddleware::requireRole('professor');

$controller = new TarefaController($conn);
$controller->create();