<?php

namespace App\Services;

use App\Validators\ProvaNotaValidator;

class ProvaNotaService
{
    private ProvaNotaValidator $validator;

    public function __construct()
    {
        $this->validator = new ProvaNotaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty($dados['prova_id'] ?? '') || empty($dados['aluno_id'] ?? '') || !isset($dados['nota']) || $dados['nota'] === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $prova_id = $dados['prova_id'];
        $aluno_id = $dados['aluno_id'];
        $nota = $dados['nota'];

        return $this->validator->validateCreate($prova_id, $aluno_id, $nota);
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

        $prova_id = $dados['prova_id'];
        $aluno_id = $dados['aluno_id'];
        $nota = $dados['nota'];

        return $this->validator->validateCreate($prova_id, $aluno_id, $nota);
    }
}
