<?php

namespace App\Models;

class Advertencia
{
    public ?int $id;
    public string $titulo;
    public string $descricao;

    public function __construct(
        string $titulo,
        string $descricao,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
    }
}
