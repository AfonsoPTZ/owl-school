<?php

namespace App\DTOs;

class ChamadaItemDTO
{
    public int $chamadaId;
    public int $alunoId;
    public string $status;

    public function __construct(array $dados)
    {
        $this->chamadaId = $this->int($dados['chamadaId'] ?? $dados['chamada_id'] ?? null);
        $this->alunoId = $this->int($dados['alunoId'] ?? $dados['aluno_id'] ?? null);
        $this->status = $this->string($dados['status'] ?? null);
    }

    private function string(?string $value): string
    {
        return trim($value ?? '');
    }

    private function int(?int $value): int
    {
        return (int) ($value ?? 0);
    }
}
