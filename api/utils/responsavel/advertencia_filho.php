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
    advertencia.id AS id,
    advertencia.titulo AS titulo,
    advertencia.descricao AS descricao,
    usuario.nome AS aluno_nome
  FROM aluno_responsavel
  JOIN aluno_advertencia
    ON aluno_advertencia.aluno_id = aluno_responsavel.aluno_id
  JOIN advertencia
    ON advertencia.id = aluno_advertencia.advertencia_id
  JOIN usuario
    ON usuario.id = aluno_responsavel.aluno_id
  WHERE aluno_responsavel.responsavel_id = ?
  ORDER BY advertencia.id DESC
");
$stmt->bind_param("i", $responsavelId);
$stmt->execute();


$resultado = $stmt->get_result();



if (!$resultado) {
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao listar advertências: ' . $conn->error
  ]);
  exit;
}


$advertencias = [];


while ($linha = $resultado->fetch_assoc()) {
  $advertencias[] = $linha;
}


echo json_encode([
  'success' => true,
  'advertencias' => $advertencias
]);


$stmt->close();
$conn->close();