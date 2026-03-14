<?php

namespace App\Validators;

use App\DTOs\ChamadaDTO;

class ChamadaValidator
{
    private function validateDate(string $date): array
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Formato de data de chamada deve ser YYYY-MM-DD.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateCreate(ChamadaDTO $dto): array
    {
        if (empty(trim($dto->data ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data);
    }

    public function validateUpdate(ChamadaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->data ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data);
    }

    public function validateDelete(ChamadaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }
}
