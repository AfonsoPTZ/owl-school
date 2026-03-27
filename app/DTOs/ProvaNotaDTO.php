<?php

namespace App\DTOs;

class ProvaNotaDTO
{
    public int $provaId;
    public int $alunoId;
    public float $nota;

    public function __construct(array $dados)
    {
        $this->provaId = $this->int($dados['provaId'] ?? $dados['prova_id'] ?? null);
        $this->alunoId = $this->int($dados['alunoId'] ?? $dados['aluno_id'] ?? null);
        $this->nota = $this->float($dados['nota'] ?? null);
    }

    private function int(?int $value): int
    {
        return (int) ($value ?? 0);
    }

    private function float(?float $value): float
    {
        return (float) ($value ?? 0);
    }
}
