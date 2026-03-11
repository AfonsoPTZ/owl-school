<?php

namespace App\DTOs;

class ChamadaDTO
{
    public string $data;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->data = trim($dados['data'] ?? '');
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
