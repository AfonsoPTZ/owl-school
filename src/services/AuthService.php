<?php

namespace App\Services;

use App\Validators\AuthValidator;

class AuthService
{
    private AuthValidator $validator;

    public function __construct()
    {
        $this->validator = new AuthValidator();
    }

    public function validarLogin(array $dados): array
    {
        if (empty(trim($dados['email'] ?? '')) || empty(trim($dados['senha'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $email = $dados['email'];
        $senha = $dados['senha'];

        return $this->validator->validateLogin($email, $senha);
    }
}
