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


$alunoId = $_SESSION['user_id'];


$stmt = $conn->prepare("
  SELECT 
    chamada.data AS data,
    chamada_item.status AS status
  FROM chamada_item
  JOIN chamada 
    ON chamada.id = chamada_item.chamada_id
  WHERE chamada_item.aluno_id = ?
  ORDER BY chamada.data DESC
");
$stmt->bind_param("i", $alunoId);
$stmt->execute();


$resultado = $stmt->get_result();



if (!$resultado) {
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao listar frequências: ' . $conn->error
  ]);
  exit;
}


$frequencias = [];


while ($linha = $resultado->fetch_assoc()) {
  $frequencias[] = $linha;
}


echo json_encode([
  'success' => true,
  'frequencias' => $frequencias
]);


$stmt->close();
$conn->close();
