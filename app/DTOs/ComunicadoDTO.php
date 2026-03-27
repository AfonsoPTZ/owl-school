<?php

namespace App\DTOs;

class ComunicadoDTO
{
    public string $titulo;
    public string $corpo;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->titulo = $this->string($dados['titulo'] ?? null);
        $this->corpo = $this->string($dados['corpo'] ?? null);
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}
