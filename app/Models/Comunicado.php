<?php

namespace App\Models;

class Comunicado
{
    public ?int $id;
    public string $titulo;
    public string $corpo;

    public function __construct(
        string $titulo,
        string $corpo,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->corpo = $corpo;
    }
}
