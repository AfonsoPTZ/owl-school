<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/conexao.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

// Arquivo temporário para auth - você pode criar um AuthController depois
require_once __DIR__ . '/../../api/auth.php';
