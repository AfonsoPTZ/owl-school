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
  SELECT usuario.nome AS nome_filho
  FROM aluno_responsavel
  JOIN aluno 
    ON aluno.usuario_id = aluno_responsavel.aluno_id
  JOIN usuario 
    ON usuario.id = aluno.usuario_id
  WHERE aluno_responsavel.responsavel_id = ?
  LIMIT 1
");
$stmt->bind_param("i", $responsavelId);
$stmt->execute();


$resultado = $stmt->get_result();


if (!$resultado) {
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao listar mome dp filho: ' . $conn->error
  ]);
  exit;
}


while ($linha = $resultado->fetch_assoc()) {
  $nomeFilho = $linha['nome_filho'];
}


if ($nomeFilho) {
  echo json_encode([
    'success' => true,
    'nome_filho' => $nomeFilho
  ]);

} else {
  echo json_encode([
    'success' => false,
    'message' => 'Filho não encontrado.'
  ]);
}


$stmt->close();
$conn->close();