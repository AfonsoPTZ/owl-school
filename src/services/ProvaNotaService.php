<?php

namespace App\Services;

class ProvaNotaService
{
    public function validarCreate(array $dados): array
    {
        if (empty($dados['provaId'] ?? '') || empty($dados['alunoId'] ?? '') || !isset($dados['nota']) || $dados['nota'] === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }

    public function validarDelete(array $dados): array
    {
        if (!isset($dados['id']) || empty($dados['id'])) {
            return ["success" => false, "message" => "ID não informado."];
        }
        return ["success" => true];
    }

    public function validarUpdate(array $dados): array
    {
        if (!isset($dados['id']) || empty($dados['id'])) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dados['provaId'] ?? '') || empty($dados['alunoId'] ?? '') || !isset($dados['nota']) || $dados['nota'] === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }
        return ["success" => true];
    }
}
