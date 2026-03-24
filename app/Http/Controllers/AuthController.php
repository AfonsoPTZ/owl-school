<?php

namespace App\Http\Controllers;

use App\DTOs\AuthDTO;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AuthService($conn);
    }

    public function login(): void
    {
        try {
            $dto = new AuthDTO($_POST);
            $result = $this->service->login($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'login');
        }
    }

    public function logout(): void
    {
        try {
            $result = $this->service->logout();

            if (($result['redirect'] ?? false) === true) {
                header('Location: /owl-school/public/index.html');
                exit;
            }

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'logout');
        }
    }

    public function authme(): void
    {
        try {
            if (empty($_SESSION['user_id'])) {
                http_response_code(401);
                $this->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
                return;
            }

            $this->json([
                'success' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'tipo_usuario' => $_SESSION['tipo_usuario']
                ]
            ]);
        } catch (\Throwable $e) {
            $this->handleException($e, 'authme');
        }
    }
}
