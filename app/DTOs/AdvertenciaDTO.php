<?php

namespace App\DTOs;

class AdvertenciaDTO
{
    public string $titulo;
    public string $descricao;
    public ?int $aluno_id;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->titulo = trim($dados['titulo'] ?? '');
        $this->descricao = trim($dados['descricao'] ?? '');
        $this->aluno_id = isset($dados['aluno_id']) ? (int) $dados['aluno_id'] : null;
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
