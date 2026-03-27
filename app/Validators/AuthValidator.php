<?php

namespace App\Validators;

use App\DTOs\AuthDTO;

class AuthValidator
{
    public function validateLogin(AuthDTO $dto): array
    {
        if ($this->isBlank($dto->email) || $this->isBlank($dto->senha)) {
            return $this->error('Preencha todos os campos obrigatórios.');
        }

        if (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Formato de email inválido.');
        }

        if (!preg_match('/@teste\.com$/', $dto->email)) {
            return $this->error('Email deve terminar com @teste.com.');
        }

        if (strlen($dto->senha) !== 6) {
            return $this->error('Senha deve ter exatamente 6 caracteres.');
        }

        if (!ctype_digit($dto->senha)) {
            return $this->error('Senha deve conter apenas números (0-9).');
        }

        return ['success' => true];
    }

    private function isBlank(?string $value): bool
    {
        return empty(trim($value ?? ''));
    }

    private function error(string $message, int $status = 422): array
    {
        return [
            'success' => false,
            'message' => $message,
            'status' => $status
        ];
    }
}