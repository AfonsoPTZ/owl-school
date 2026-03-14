<?php

namespace App\Models;

class ProvaNota
{
    public int $provaId;
    public int $alunoId;
    public float $nota;

    public function __construct(
        int $provaId,
        int $alunoId,
        float $nota
    ) {
        $this->provaId = $provaId;
        $this->alunoId = $alunoId;
        $this->nota = $nota;
    }
}
