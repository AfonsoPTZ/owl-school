<?php

namespace App\Services;

class AuthService
{
    public function validarLogin(array $dados): array
    {
        if (empty(trim($dados['email'] ?? '')) || empty(trim($dados['senha'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }
}
