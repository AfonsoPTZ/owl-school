<?php

namespace App\Services;

use App\Validators\ComunicadoValidator;

class ComunicadoService
{
    private ComunicadoValidator $validator;

    public function __construct()
    {
        $this->validator = new ComunicadoValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['corpo'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $corpo = $dados['corpo'];

        return $this->validator->validateCreate($titulo, $corpo);
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
        if (empty(trim($dados['titulo'] ?? '')) || empty(trim($dados['corpo'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $titulo = $dados['titulo'];
        $corpo = $dados['corpo'];

        return $this->validator->validateCreate($titulo, $corpo);
    }
}
