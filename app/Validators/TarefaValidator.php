<?php

namespace App\Validators;

use App\DTOs\TarefaDTO;

class TarefaValidator
{
    public function validateCreate(TarefaDTO $dto): array
    {
        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->descricao) || $this->isBlank($dto->data_entrega)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateDate($dto->data_entrega);
    }

    public function validateUpdate(TarefaDTO $dto): array
    {
        if (empty($dto->id)) {
            return $this->error('ID não fornecido.');
        }

        if ($this->isBlank($dto->titulo) || $this->isBlank($dto->descricao) || $this->isBlank($dto->data_entrega)) {
            return $this->error('Preencha todos os campos necessários.');
        }

        return $this->validateDate($dto->data_entrega);
    }

    public function validateDelete(TarefaDTO $dto): array
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
            return $this->error('Formato de data de entrega deve ser YYYY-MM-DD.');
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