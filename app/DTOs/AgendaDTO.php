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
        $this->diaSemana = $this->string($dados['dia_semana'] ?? $dados['diaSemana'] ?? null);
        $this->inicio = $this->string($dados['inicio'] ?? null);
        $this->fim = $this->string($dados['fim'] ?? null);
        $this->disciplina = $this->string($dados['disciplina'] ?? null);
        $this->id = isset($dados['id']) ? (int) $dados['id'] : null;
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }
}
