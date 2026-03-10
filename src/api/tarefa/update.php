<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

header('Content-Type: application/json');

use App\Controllers\TarefaController;

$controller = new TarefaController($conn);
$controller->update();