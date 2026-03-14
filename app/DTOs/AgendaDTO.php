<?php

namespace App\DTOs;

class AgendaDTO
{
    public string $diaSemana;
    public string $inicio;
    public string $fim;
    public string $disciplina;
    public ?int $id;

    public function __construct(array $dados)
    {
        $this->diaSemana = trim($dados['dia_semana'] ?? $dados['diaSemana'] ?? '');
        $this->inicio = trim($dados['inicio'] ?? '');
        $this->fim = trim($dados['fim'] ?? '');
        $this->disciplina = trim($dados['disciplina'] ?? '');
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }
}
