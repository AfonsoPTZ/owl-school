<?php

namespace App\Validators;

class AgendaValidator
{
    public function validateDayOfWeek($day)
    {
        $validDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];

        if (!in_array($day, $validDays)) {
            return [
                'success' => false,
                'message' => 'Day must be: segunda, terca, quarta, quinta, or sexta.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Day is valid.'
        ];
    }

    public function validateTime($time)
    {
        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return [
                'success' => false,
                'message' => 'Time format must be HH:MM (24-hour format).'
            ];
        }

        return [
            'success' => true,
            'message' => 'Time is valid.'
        ];
    }

    public function validateCreate($diaSemana, $inicio, $fim, $disciplina)
    {
        $dayValidation = $this->validateDayOfWeek($diaSemana);
        if (!$dayValidation['success']) {
            return $dayValidation;
        }

        $inicioValidation = $this->validateTime($inicio);
        if (!$inicioValidation['success']) {
            return $inicioValidation;
        }

        $fimValidation = $this->validateTime($fim);
        if (!$fimValidation['success']) {
            return $fimValidation;
        }

        return [
            'success' => true,
            'message' => 'Schedule data is valid.'
        ];
    }
}
