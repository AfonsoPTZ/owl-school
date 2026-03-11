  <?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    'success' => false,
    'message' => 'Método inválido.'
  ]);
  exit;
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_SESSION['user_name'])) {
  echo json_encode(['user_name' => $_SESSION['user_name']]);
} else {
  echo json_encode(['user_name' => null]);
}
