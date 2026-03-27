<?php

namespace App\Validators;

use App\DTOs\ProvaDTO;

class ProvaValidator
{
    public function validateCreate(ProvaDTO $dto): array
    {
        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->data)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateDate($dto->data);
    }

    public function validateUpdate(ProvaDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID não fornecido.');
        }

        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->data)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateDate($dto->data);
    }

    public function validateDelete(ProvaDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID não fornecido.');
        }

        return ['success' => true];
    }

    private function validateDate(string $date): array
    {
        $format = 'Y-m-d';
        $parsedDate = \DateTime::createFromFormat($format, $date);

        if (!$parsedDate || $parsedDate->format($format) !== $date) {
            return $this->error('Formato de data de prova deve ser YYYY-MM-DD.');
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
