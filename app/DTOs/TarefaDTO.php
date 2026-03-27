<?php

namespace App\DTOs;

class TarefaDTO
{
    public string $titulo;
    public string $descricao;
    public string $data_entrega;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->titulo = $this->string($dados['titulo'] ?? null);
        $this->descricao = $this->string($dados['descricao'] ?? null);
        $this->data_entrega = $this->string($dados['data_entrega'] ?? null);
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}