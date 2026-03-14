<?php

namespace App\Validators;

use App\DTOs\ProvaDTO;

class ProvaValidator
{
    private function validateDate(string $date): array
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Formato de data de prova deve ser YYYY-MM-DD.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateCreate(ProvaDTO $dto): array
    {
        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->data ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data);
    }

    public function validateUpdate(ProvaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->data ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data);
    }

    public function validateDelete(ProvaDTO $dto): array
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
