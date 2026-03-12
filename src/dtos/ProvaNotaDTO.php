<?php

namespace App\DTOs;

class ProvaNotaDTO
{
    public int $provaId;
    public int $alunoId;
    public float $nota;

    public function __construct(array $dados)
    {
        $this->provaId = (int) ($dados['provaId'] ?? $dados['prova_id'] ?? 0);
        $this->alunoId = (int) ($dados['alunoId'] ?? $dados['aluno_id'] ?? 0);
        $this->nota = (float) ($dados['nota'] ?? 0);
    }
}
