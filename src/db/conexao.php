<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Teste rápido - hardcoded para verificar conexão
$servername = "localhost";
$username   = "root";
$password   = "AfonsoPTZ#6113";
$dbname     = "owl_school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Se chegou aqui, funcionou!
// echo "✅ Conectado com sucesso!";

?>