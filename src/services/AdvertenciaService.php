<?php

namespace App\Services;

use App\Validators\AdvertenciaValidator;

class AdvertenciaService
{
    private AdvertenciaValidator $validator;

    public function __construct()
    {
        $this->validator = new AdvertenciaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['descricao'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];

        return $this->validator->validateCreate($titulo, $descricao);
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
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['descricao'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];

        return $this->validator->validateCreate($titulo, $descricao);
    }
}
