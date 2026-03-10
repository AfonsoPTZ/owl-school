<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../db/conexao.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

require_once __DIR__ . '/../../../api/utils/aluno/nota_aluno.php';
