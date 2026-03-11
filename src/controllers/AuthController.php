<?php

namespace App\Controllers;

use App\Repositories\AuthRepository;
use App\Services\AuthService;

class AuthController
{
    private AuthRepository $repository;
    private AuthService $service;

    public function __construct($conn)
    {
        $this->repository = new AuthRepository($conn);
        $this->service = new AuthService();
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

        // Validar dados com o service
        $validacao = $this->service->validarLogin($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $email = $_POST['email'];
        $senha = $_POST['senha'];

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
