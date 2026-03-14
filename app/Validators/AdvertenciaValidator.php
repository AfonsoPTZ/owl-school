<?php

namespace App\Validators;

use App\DTOs\AdvertenciaDTO;

class AdvertenciaValidator
{
    public function validateCreate(AdvertenciaDTO $dto): array
    {
        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->descricao ?? '')) || empty($dto->aluno_id)) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateUpdate(AdvertenciaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID não fornecido.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->titulo ?? '')) || empty(trim($dto->descricao ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos necessários.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateDelete(AdvertenciaDTO $dto): array
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
