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
  SELECT usuario.nome AS nome_responsavel
  FROM aluno_responsavel
  JOIN responsavel 
    ON responsavel.usuario_id = aluno_responsavel.responsavel_id
  JOIN usuario 
    ON usuario.id = responsavel.usuario_id
  WHERE aluno_responsavel.aluno_id = ?
  LIMIT 1
");
$stmt->bind_param("i", $alunoId);
$stmt->execute();


$resultado = $stmt->get_result();


if (!$resultado) {
  echo json_encode([
    'success' => false,
    'message' => 'Erro ao listar nome do responsável: ' . $conn->error
  ]);
  exit;
}


while ($linha = $resultado->fetch_assoc()) {
  $nomeResponsavel = $linha['nome_responsavel'];
}


if ($nomeResponsavel) {
  echo json_encode([
    'success' => true,
    'nome_responsavel' => $nomeResponsavel
  ]);

} else {
  echo json_encode([
    'success' => false,
    'message' => 'Responsável não encontrado.'
  ]);
}


$stmt->close();
$conn->close();