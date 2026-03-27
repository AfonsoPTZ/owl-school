<?php

namespace App\Services;

use App\DTOs\AuthDTO;
use App\Repositories\AuthRepository;
use App\Validators\AuthValidator;

class AuthService
{
    private AuthValidator $validator;
    private AuthRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new AuthValidator();
        $this->repository = new AuthRepository($conn);
    }

    public function login(AuthDTO $dto): array
    {
        try {
            $validacao = $this->validator->validateLogin($dto);

            if (!$validacao['success']) {
                return $validacao;
            }

            $usuario = $this->repository->findByEmail($dto->email);

            if (!$usuario) {
                return $this->response(false, 'Email ou senha incorretos.', 401);
            }

            if ($dto->senha !== $usuario['senha']) {
                return $this->response(false, 'Email ou senha incorretos.', 401);
            }

            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            return [
                'success' => true,
                'message' => 'Login realizado com sucesso.',
                'status' => 200,
                'usuario' => [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'tipo_usuario' => $usuario['tipo_usuario']
                ]
            ];
        } catch (\Throwable $e) {
            return $this->response(false, 'Erro ao realizar login: ' . $e->getMessage(), 500);
        }
    }

    public function logout(): array
    {
        try {
            session_unset();
            session_destroy();

            return [
                'success' => true,
                'status' => 200,
                'redirect' => true
            ];
        } catch (\Throwable $e) {
            return $this->response(false, 'Erro ao fazer logout: ' . $e->getMessage(), 500);
        }
    }

    private function response(bool $success, string $message, int $status): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status
        ];
    }
}