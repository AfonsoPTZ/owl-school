<?php

namespace App\DTOs;

class AIDTO
{
    public string $pergunta;

    public function __construct(array $data)
    {
        $this->pergunta = trim($data['pergunta'] ?? '');
    }

    public function toArray(): array
    {
        return [
            'pergunta' => $this->pergunta
        ];
    }
}