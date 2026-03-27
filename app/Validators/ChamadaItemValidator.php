<?php

namespace App\Validators;

use App\DTOs\ChamadaItemDTO;

class ChamadaItemValidator
{
    public function validateCreate(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId || $this->isBlank($dto->status)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateStatus($dto->status);
    }

    public function validateUpdate(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId || $this->isBlank($dto->status)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateStatus($dto->status);
    }

    public function validateDelete(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId) {
            return $this->error('ID de chamada e ID de aluno são obrigatórios.');
        }

        return ['success' => true];
    }

    private function validateStatus(string $status): array
    {
        $validStatus = ['presente', 'falta'];

        if (!in_array(strtolower($status), $validStatus)) {
            return $this->error('Status deve ser: presente ou falta.');
        }

        return ['success' => true];
    }

    private function isBlank(?string $value): bool
    {
        return empty(trim($value ?? ''));
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


