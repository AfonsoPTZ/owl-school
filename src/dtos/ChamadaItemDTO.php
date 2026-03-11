<?php

namespace App\DTOs;

class ChamadaItemDTO
{
    public $chamada_id;
    public $aluno_id;
    public string $status;

    public function __construct(array $dados)
    {
        $this->chamada_id = (int) ($dados['chamada_id'] ?? 0);
        $this->aluno_id = (int) ($dados['aluno_id'] ?? 0);
        $this->status = trim($dados['status'] ?? '');
    }
}
