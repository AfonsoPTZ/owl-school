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


$responsavelId = $_SESSION['user_id'];


$stmt = $conn->prepare("
  SELECT
    chamada.data AS data,
    chamada_item.status AS status,
    usuario.nome AS aluno_nome
  FROM aluno_responsavel
  JOIN chamada_item 
    ON chamada_item.aluno_id = aluno_responsavel.aluno_id
  JOIN chamada 
    ON chamada.id = chamada_item.chamada_id
  JOIN usuario 
    ON usuario.id = aluno_responsavel.aluno_id
  WHERE aluno_responsavel.responsavel_id = ?
  ORDER BY chamada.data DESC
");
$stmt->bind_param("i", $responsavelId);
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
