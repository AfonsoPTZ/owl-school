<?php

namespace App\DTOs;

class AuthDTO
{
    public ?string $email;
    public ?string $senha;

    public function __construct(array $data)
    {
        $this->email = isset($data['email']) ? trim($data['email']) : null;
        $this->senha = isset($data['senha']) ? trim($data['senha']) : null;
    }
}