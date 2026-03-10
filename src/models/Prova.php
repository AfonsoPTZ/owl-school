<?php

namespace App\Models;

class Prova
{
    public ?int $id;
    public string $titulo;
    public string $data;

    public function __construct(
        string $titulo,
        string $data,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->data = $data;
    }
}
