<?php

namespace App\Validators;

use App\DTOs\ProvaNotaDTO;

class ProvaNotaValidator
{
    private ProvaNotaDTO $dto;

    public function __construct(ProvaNotaDTO $dto)
    {
        $this->dto = $dto;
    }

    public function validateCreate(): array
    {
        if (!$this->dto->provaId || !$this->dto->alunoId || $this->dto->nota === '') {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos obrigatórios.',
                'status' => 422
            ];
        }

        $gradeValidation = $this->validateGrade();
        if (!$gradeValidation['success']) {
            return $gradeValidation;
        }

        return [
            'success' => true,
            'message' => 'Validação realizada com sucesso.',
            'status' => 201
        ];
    }

    public function validateUpdate(): array
    {
        if (!$this->dto->provaId || !$this->dto->alunoId || $this->dto->nota === '') {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos obrigatórios.',
                'status' => 422
            ];
        }

        $gradeValidation = $this->validateGrade();
        if (!$gradeValidation['success']) {
            return $gradeValidation;
        }

        return [
            'success' => true,
            'message' => 'Validação realizada com sucesso.',
            'status' => 200
        ];
    }

    public function validateDelete(): array
    {
        if (!$this->dto->provaId || !$this->dto->alunoId) {
            return [
                'success' => false,
                'message' => 'ID da prova e do aluno são obrigatórios.',
                'status' => 422
            ];
        }

        return [
            'success' => true,
            'message' => 'Validação realizada com sucesso.',
            'status' => 200
        ];
    }

    private function validateGrade(): array
    {
        if (!is_numeric($this->dto->nota)) {
            return [
                'success' => false,
                'message' => 'Nota deve ser um número.',
                'status' => 422
            ];
        }

        $nota = (float) $this->dto->nota;

        if ($nota < 0 || $nota > 100) {
            return [
                'success' => false,
                'message' => 'Nota deve estar entre 0 e 100.',
                'status' => 422
            ];
        }

        return [
            'success' => true,
            'message' => 'Nota válida.',
            'status' => 200
        ];
    }
}
