<?php

namespace App\Controllers;

use App\Repositories\AuthRepository;
use App\Services\AuthService;
use App\Utils\Logger;

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
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Logger::warning('Invalid method in login');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarLogin($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in login: ' . $validacao['message']);
                echo json_encode($validacao);
                return;
            }

            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $usuario = $this->repository->authenticate($email, $senha);

            if (!$usuario) {
                Logger::warning("Login failed for email: $email");
                echo json_encode([
                    'success' => false,
                    'message' => 'Email ou senha incorretos.'
                ]);
                return;
            }

            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            Logger::info("User logged in: {$usuario['nome']} ({$usuario['tipo_usuario']})");
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso.',
                'usuario' => [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'tipo_usuario' => $usuario['tipo_usuario']
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in login: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* LOGOUT */
    /* ============================== */
    public function logout()
    {
        try {
            session_unset();
            session_destroy();

            Logger::info("User logged out");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }

            header("Location: /owl-school/public/index.php");
            exit;
        } catch (\Exception $e) {
            Logger::error("Exception in logout: " . $e->getMessage());
            
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }
}
