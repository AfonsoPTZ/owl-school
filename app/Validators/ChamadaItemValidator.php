<?php

namespace App\Validators;

use App\DTOs\ChamadaItemDTO;

class ChamadaItemValidator
{
    private function validateStatus(string $status): array
    {
        $validStatus = ['presente', 'falta'];

        if (!in_array(strtolower($status), $validStatus)) {
            return [
                'success' => false,
                'message' => 'Status deve ser: presente ou falta.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateCreate(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId || empty(trim($dto->status ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateStatus($dto->status);
    }

    public function validateUpdate(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId || empty(trim($dto->status ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateStatus($dto->status);
    }

    public function validateDelete(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId) {
            return [
                'success' => false,
                'message' => 'ID de chamada e ID de aluno são obrigatórios.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }
}
