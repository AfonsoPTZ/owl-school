<?php

namespace App\Validators;

use App\DTOs\ComunicadoDTO;

class ComunicadoValidator
{
    public function validateCreate(ComunicadoDTO $dto): array
    {
        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->corpo)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return ['success' => true];
    }

    public function validateUpdate(ComunicadoDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID não fornecido.');
        }

        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->corpo)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return ['success' => true];
    }

    public function validateDelete(ComunicadoDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID não fornecido.');
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
