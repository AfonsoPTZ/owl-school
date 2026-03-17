<?php

namespace App\Validators;

use App\DTOs\AIDTO;

class AIValidator
{
    public function validateQuestion(AIDTO $dto): array
    {
        if (empty($dto->pergunta)) {
            return [
                'success' => false,
                'message' => 'Pergunta não pode estar vazia.',
                'status'  => 422
            ];
        }

        if (mb_strlen($dto->pergunta) > 500) {
            return [
                'success' => false,
                'message' => 'Pergunta muito longa.',
                'status'  => 422
            ];
        }

        return [
            'success' => true
        ];
    }
}