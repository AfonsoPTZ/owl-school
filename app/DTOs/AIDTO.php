<?php

namespace App\DTOs;

class AIDTO
{
    public string $pergunta;

    public function __construct(array $data)
    {
        $this->pergunta = $this->string($data['pergunta'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'pergunta' => $this->pergunta
        ];
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}