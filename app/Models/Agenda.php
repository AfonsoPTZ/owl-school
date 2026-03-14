<?php

namespace App\Models;

class Agenda
{
    public ?int $id;
    public string $diaSemana;
    public string $inicio;
    public string $fim;
    public string $disciplina;

    public function __construct(
        string $diaSemana,
        string $inicio,
        string $fim,
        string $disciplina,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->diaSemana = $diaSemana;
        $this->inicio = $inicio;
        $this->fim = $fim;
        $this->disciplina = $disciplina;
    }
}
