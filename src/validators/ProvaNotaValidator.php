<?php

namespace App\Validators;

class ProvaNotaValidator
{
    public function validateGrade($nota)
    {
        if (!is_numeric($nota)) {
            return [
                'success' => false,
                'message' => 'Grade must be a number.'
            ];
        }

        $nota = (float) $nota;

        if ($nota < 0 || $nota > 100) {
            return [
                'success' => false,
                'message' => 'Grade must be between 0 and 100.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Grade is valid.'
        ];
    }

    public function validateCreate($prova_id, $aluno_id, $nota)
    {
        return $this->validateGrade($nota);
    }
}
