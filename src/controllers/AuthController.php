<?php

namespace App\Controllers;

use App\Repositories\AuthRepository;

class AuthController
{
    private AuthRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new AuthRepository($conn);
    }

    /* ============================== */
    /* LOGIN */
    /* ============================== */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email e senha são obrigatórios.'
            ]);
            return;
        }

        $usuario = $this->repository->authenticate($email, $senha);

        if (!$usuario) {
            echo json_encode([
                'success' => false,
                'message' => 'Email ou senha incorretos.'
            ]);
            return;
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
    }

    /* ============================== */
    /* LOGOUT */
    /* ============================== */
    public function logout()
    {
        session_unset();
        session_destroy();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: /owl-school/public/index.php");
        exit;
    }
}
