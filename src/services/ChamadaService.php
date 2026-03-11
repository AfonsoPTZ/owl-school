<?php

namespace App\Services;

use App\Validators\ChamadaValidator;

class ChamadaService
{
    private ChamadaValidator $validator;

    public function __construct()
    {
        $this->validator = new ChamadaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['data'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $data = $dados['data'];

        return $this->validator->validateCreate($data);
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
        if (empty(trim($dados['data'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $data = $dados['data'];

        return $this->validator->validateCreate($data);
    }
}
