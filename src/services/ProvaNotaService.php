<?php

namespace App\Services;

class ProvaNotaService
{
    public function validarCreate(array $dados): array
    {
        if (empty($dados['prova_id'] ?? '') || empty($dados['aluno_id'] ?? '') || !isset($dados['nota']) || $dados['nota'] === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }

    public function validarDelete(array $dados): array
    {
        if (empty($dados['prova_id'] ?? '') || empty($dados['aluno_id'] ?? '')) {
            return ["success" => false, "message" => "ID da prova e do aluno são obrigatórios."];
        }
        return ["success" => true];
    }

    public function validarUpdate(array $dados): array
    {
        if (empty($dados['prova_id'] ?? '') || empty($dados['aluno_id'] ?? '') || !isset($dados['nota']) || $dados['nota'] === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }
}
