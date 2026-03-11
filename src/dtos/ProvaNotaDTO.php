<?php

namespace App\DTOs;

class ProvaNotaDTO
{
    public int $provaId;
    public int $alunoId;
    public float $nota;

    public function __construct(array $dados)
    {
        $this->provaId = (int) ($dados['provaId'] ?? 0);
        $this->alunoId = (int) ($dados['alunoId'] ?? 0);
        $this->nota = (float) ($dados['nota'] ?? 0);
    }
}
