<?php

namespace App\DTOs;

class ChamadaItemDTO
{
    public int $chamadaId;
    public int $alunoId;
    public string $status;

    public function __construct(array $dados)
    {

        $this->chamadaId = (int) ($dados['chamadaId'] ?? $dados['chamada_id'] ?? 0);
        $this->alunoId = (int) ($dados['alunoId'] ?? $dados['aluno_id'] ?? 0);
        $this->status = trim($dados['status'] ?? '');
    }
}
