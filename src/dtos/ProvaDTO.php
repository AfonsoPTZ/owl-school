<?php

namespace App\DTOs;

class ProvaDTO
{
    public string $titulo;
    public string $data;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->titulo = trim($dados['titulo'] ?? '');
        $this->data = trim($dados['data'] ?? '');
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
