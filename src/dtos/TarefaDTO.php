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
        $this->titulo = trim($dados['titulo'] ?? '');
        $this->descricao = trim($dados['descricao'] ?? '');
        $this->data_entrega = trim($dados['data_entrega'] ?? '');
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
