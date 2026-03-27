<?php

namespace App\Validators;

use App\DTOs\ChamadaDTO;

class ChamadaValidator
{
    public function validateCreate(ChamadaDTO $dto): array
    {
        if ($this->isBlank($dto->data)) {
            return $this->error('Preencha todos os campos necess?rios.');
        }

        return $this->validateDate($dto->data);
    }

    public function validateUpdate(ChamadaDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID n?o fornecido.');
        }

        if ($this->isBlank($dto->data)) {
            return $this->error('Preencha todos os campos necess?rios.');
        }

        return $this->validateDate($dto->data);
    }

    public function validateDelete(ChamadaDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID n?o fornecido.');
        }

        return ['success' => true];
    }

    private function validateDate(string $date): array
    {
        $format = 'Y-m-d';
        $parsedDate = \DateTime::createFromFormat($format, $date);

        if (!$parsedDate || $parsedDate->format($format) !== $date) {
            return $this->error('Formato de data deve ser YYYY-MM-DD.');
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
