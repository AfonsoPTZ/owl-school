<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

use App\Controllers\AdvertenciaController;

$controller = new AdvertenciaController($conn);
$controller->update();
