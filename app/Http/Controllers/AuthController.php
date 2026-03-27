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
        $this->executeWithDto('login');
    }

    public function logout(): void
    {
        $this->executeAction(fn() => $this->service->logout(), 'logout');
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

    private function executeWithDto(string $action): void
    {
        $this->executeAction(function () use ($action) {
            $dto = new AuthDTO($_POST);
            return $this->service->$action($dto);
        }, $action);
    }

    private function executeAction(callable $callback, string $action): void
    {
        try {
            $result = $callback();
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, $action);
        }
    }
}
