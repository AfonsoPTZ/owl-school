<?php

namespace App\DTOs;

class ComunicadoDTO
{
    public string $titulo;
    public string $corpo;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->titulo = trim($dados['titulo'] ?? '');
        $this->corpo = trim($dados['corpo'] ?? '');
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
