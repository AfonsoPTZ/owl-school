<?php

namespace App\Services;

use App\Validators\ChamadaItemValidator;

class ChamadaItemService
{
    private ChamadaItemValidator $validator;

    public function __construct()
    {
        $this->validator = new ChamadaItemValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty($dados['chamada_id'] ?? '') || empty($dados['aluno_id'] ?? '') || empty(trim($dados['status'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $chamada_id = $dados['chamada_id'];
        $aluno_id = $dados['aluno_id'];
        $status = $dados['status'];

        return $this->validator->validateCreate($chamada_id, $aluno_id, $status);
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

        $chamada_id = $dados['chamada_id'];
        $aluno_id = $dados['aluno_id'];
        $status = $dados['status'];

        return $this->validator->validateCreate($chamada_id, $aluno_id, $status);
    }
}
