<?php

namespace App\DTOs;

class AuthDTO
{
    public ?string $email;
    public ?string $senha;

    public function __construct(array $data)
    {
        $this->email = isset($data['email']) ? $this->string($data['email']) : null;
        $this->senha = isset($data['senha']) ? $this->string($data['senha']) : null;
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}