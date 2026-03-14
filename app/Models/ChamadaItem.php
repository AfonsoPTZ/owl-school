<?php

namespace App\Models;

class ChamadaItem
{
    public int $chamadaId;
    public int $alunoId;
    public string $status;

    public function __construct(
        int $chamadaId,
        int $alunoId,
        string $status
    ) {
        $this->chamadaId = $chamadaId;
        $this->alunoId = $alunoId;
        $this->status = $status;
    }
}
