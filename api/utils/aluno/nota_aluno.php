<?php
require_once __DIR__ . '/../../../db/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    'success' => false,
    'message' => 'Método inválido.'
  ]);
  exit;
}


$usuarioId = $_SESSION['user_id'];


$stmt = $conn->prepare("
  SELECT
    prova.id AS prova_id,
    prova.titulo AS titulo,
    prova.data AS data,
    prova_nota.nota AS nota
  FROM prova_nota
  JOIN prova 
    ON prova.id = prova_nota.prova_id
  WHERE prova_nota.aluno_id = ?
  ORDER BY prova.data DESC
");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();


$resultado = $stmt->get_result();


if (!$resultado) {
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao listar notas: ' . $conn->error
  ]);
  exit;
}


$notas = [];


while ($linha = $resultado->fetch_assoc()) {
  $notas[] = $linha;
}


echo json_encode([
  'success' => true,
  'notas' => $notas
]);


$stmt->close();
$conn->close();
