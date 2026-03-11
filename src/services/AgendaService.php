<?php

namespace App\Services;

use App\Validators\AgendaValidator;

class AgendaService
{
    private AgendaValidator $validator;

    public function __construct()
    {
        $this->validator = new AgendaValidator();
    }

    public function validarCreate(array $dados): array
    {
        if (empty(trim($dados['dia_semana'] ?? '')) || empty(trim($dados['inicio'] ?? '')) || empty(trim($dados['fim'] ?? '')) || empty(trim($dados['disciplina'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $diaSemana = $dados['dia_semana'];
        $inicio = $dados['inicio'];
        $fim = $dados['fim'];
        $disciplina = $dados['disciplina'];

        return $this->validator->validateCreate($diaSemana, $inicio, $fim, $disciplina);
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
        if (empty(trim($dados['dia_semana'] ?? '')) || empty(trim($dados['inicio'] ?? '')) || empty(trim($dados['fim'] ?? '')) || empty(trim($dados['disciplina'] ?? ''))) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $diaSemana = $dados['dia_semana'];
        $inicio = $dados['inicio'];
        $fim = $dados['fim'];
        $disciplina = $dados['disciplina'];

        return $this->validator->validateCreate($diaSemana, $inicio, $fim, $disciplina);
    }
}
