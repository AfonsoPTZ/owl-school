<?php

namespace App\Services;

use App\Validators\TarefaValidator;

class TarefaService
{
    private TarefaValidator $validator;

    public function __construct()
    {
        $this->validator = new TarefaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['descricao'] ?? '')) || empty(trim($dados['data_entrega'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];
        $data_entrega = $dados['data_entrega'];

        return $this->validator->validateCreate($titulo, $descricao, $data_entrega);
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
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['descricao'] ?? '')) || empty(trim($dados['data_entrega'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];
        $data_entrega = $dados['data_entrega'];

        return $this->validator->validateCreate($titulo, $descricao, $data_entrega);
    }
}