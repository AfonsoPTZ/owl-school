<?php

namespace App\Validators;

use App\DTOs\ComunicadoDTO;

class ComunicadoValidator
{
    public function validateCreate(ComunicadoDTO $dto): array
    {
        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->corpo ?? ''))) {
            return [
                'success' => false,
                'message' => 'Fill in all required fields.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateUpdate(ComunicadoDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID not provided.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->corpo ?? ''))) {
            return [
                'success' => false,
                'message' => 'Fill in all required fields.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateDelete(ComunicadoDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID not provided.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }
}
