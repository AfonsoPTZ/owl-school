<?php

namespace App\Services;

use App\Validators\ProvaValidator;

class ProvaService
{
    private ProvaValidator $validator;

    public function __construct()
    {
        $this->validator = new ProvaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['data'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $data = $dados['data'];

        return $this->validator->validateCreate($titulo, $data);
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
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['data'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $data = $dados['data'];

        return $this->validator->validateCreate($titulo, $data);
    }
}
