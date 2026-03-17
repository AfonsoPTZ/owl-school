<?php

namespace App\DTOs;

class AIDTO
{
    public string $pergunta;

    public function __construct(array $dados)
    {
        $this->pergunta = trim($dados['pergunta'] ?? '');
    }
}