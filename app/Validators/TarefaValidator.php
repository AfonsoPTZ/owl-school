<?php

namespace App\Validators;

use App\DTOs\TarefaDTO;

class TarefaValidator
{
    public function validateCreate(TarefaDTO $dto): array
    {
        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->descricao ?? '')) || empty(trim($dto->data_entrega ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data_entrega);
    }

    public function validateUpdate(TarefaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->descricao ?? '')) || empty(trim($dto->data_entrega ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return $this->validateDate($dto->data_entrega);
    }

    public function validateDelete(TarefaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        return [
            'success' => true
        ];
    }

    private function validateDate(string $date): array
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Formato de data de entrega deve ser YYYY-MM-DD.',
                'status'  => 422
            ];
        }

        return [
            'success' => true
        ];
    }
}