<?php

namespace App\Services\AI;

class FollowUpDetector
{
    public function isFollowUp(string $pergunta, ?string $lastIntent): bool
    {
        if ($lastIntent === null) {
            return false;
        }

        $texto = mb_strtolower(trim($pergunta), 'UTF-8');

        $keywords = [
            'qual',
            'quais',
            'quando',
            'onde',
            'por que',
            'porquê',
            'como',
            'me explica',
            'detalha',
            'o primeiro',
            'o segundo',
            'o último',
            'motivo',
            'razão',
            'descrição'
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($texto, $keyword)) {
                return true;
            }
        }

        return mb_strlen($texto) < 15;
    }
}