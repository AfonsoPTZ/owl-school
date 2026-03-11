<?php
require_once __DIR__ . '/../db/conexao.php';

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


$email = $_POST['email'];
$senha = $_POST['senha'];


$stmt = $conn->prepare("SELECT id, nome, tipo_usuario FROM usuario WHERE email = ? AND senha = ?");
$stmt->bind_param("ss", $email, $senha);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();


if (!$usuario) {
  echo json_encode([
    'success' => false,
    'message' => 'Email ou senha incorretos.'
  ]);
  $stmt->close();
  $conn->close();
  exit;
}

$_SESSION['user_id'] = $usuario['id'];
$_SESSION['user_name'] = $usuario['nome'];
$_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

echo json_encode([
  'success' => true,
  'message' => 'Login realizado com sucesso.',
  'usuario' => [
    'id' => $usuario['id'],
    'nome' => $usuario['nome'],
    'tipo_usuario' => $usuario['tipo_usuario']
  ]
]);


$stmt->close();
$conn->close();