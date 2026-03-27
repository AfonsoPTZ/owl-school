<?php

namespace App\DTOs;

class ChamadaDTO
{
    public string $data;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->data = $this->string($dados['data'] ?? null);
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}
