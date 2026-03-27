<?php

namespace App\Validators;

use App\DTOs\ProvaNotaDTO;

class ProvaNotaValidator
{
    public function validateCreate(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId || $dto->nota === '') {
            return $this->error('Preencha todos os campos obrigatorios.');
        }

        return $this->validateGrade($dto->nota);
    }

    public function validateUpdate(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId || $dto->nota === '') {
            return $this->error('Preencha todos os campos obrigatorios.');
        }

        return $this->validateGrade($dto->nota);
    }

    public function validateDelete(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId) {
            return $this->error('ID da prova e do aluno sao obrigatorios.');
        }

        return ['success' => true];
    }

    private function validateGrade($nota): array
    {
        if (!is_numeric($nota)) {
            return $this->error('Nota deve ser um numero.');
        }

        $notaFloat = (float) $nota;

        if ($notaFloat < 0 || $notaFloat > 100) {
            return $this->error('Nota deve estar entre 0 e 100.');
        }

        return ['success' => true];
    }

    private function error(string $message, int $status = 422): array
    {
        return [
            'success' => false,
            'message' => $message,
            'status' => $status
        ];
    }
}
