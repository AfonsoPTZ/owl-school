<?php

namespace App\Models;

class Tarefa
{
    public ?int $id;
    public string $titulo;
    public string $descricao;
    public string $dataEntrega;

    public function __construct(
        string $titulo,
        string $descricao,
        string $dataEntrega,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->dataEntrega = $dataEntrega;
    }
}