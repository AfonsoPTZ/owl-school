<?php

namespace App\Services;

class ChamadaItemService
{
    public function validarCreate(array $dados): array
    {
        if (empty($dados['chamada_id'] ?? '') || empty($dados['aluno_id'] ?? '') || empty(trim($dados['status'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }

    public function validarDelete(array $dados): array
    {
        if (empty($dados['chamada_id'] ?? '') || empty($dados['aluno_id'] ?? '')) {
            return ["success" => false, "message" => "ID da chamada e do aluno são obrigatórios."];
        }
        return ["success" => true];
    }

    public function validarUpdate(array $dados): array
    {
        if (empty($dados['chamada_id'] ?? '') || empty($dados['aluno_id'] ?? '') || empty(trim($dados['status'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }
}
