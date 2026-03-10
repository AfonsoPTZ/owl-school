<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

session_start();

header('Content-Type: application/json; charset=utf-8');

use App\Controllers\TarefaController;

$controller = new TarefaController($conn);
$controller->index();