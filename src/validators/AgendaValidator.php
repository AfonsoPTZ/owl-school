<?php

namespace App\Validators;

use App\DTOs\AgendaDTO;

class AgendaValidator
{
    private function validateDayOfWeek(string $day): array
    {
        $validDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];

        if (!in_array(strtolower($day), $validDays)) {
            return [
                'success' => false,
                'message' => 'Day must be: segunda, terca, quarta, quinta, or sexta.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    private function validateTime(string $time): array
    {
        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return [
                'success' => false,
                'message' => 'Time format must be HH:MM (24-hour format).',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }

    public function validateCreate(AgendaDTO $dto): array
    {
        if (empty(trim($dto->diaSemana ?? '')) || empty(trim($dto->inicio ?? '')) || empty(trim($dto->fim ?? '')) || empty(trim($dto->disciplina ?? ''))) {
            return [
                'success' => false,
                'message' => 'Fill in all required fields.',
                'status'  => 422
            ];
        }

        $dayValidation = $this->validateDayOfWeek($dto->diaSemana);
        if (!$dayValidation['success']) {
            return $dayValidation;
        }

        $inicioValidation = $this->validateTime($dto->inicio);
        if (!$inicioValidation['success']) {
            return $inicioValidation;
        }

        $fimValidation = $this->validateTime($dto->fim);
        if (!$fimValidation['success']) {
            return $fimValidation;
        }

        return ['success' => true];
    }

    public function validateUpdate(AgendaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID not provided.',
                'status'  => 422
            ];
        }

        if (empty(trim($dto->diaSemana ?? '')) || empty(trim($dto->inicio ?? '')) || empty(trim($dto->fim ?? '')) || empty(trim($dto->disciplina ?? ''))) {
            return [
                'success' => false,
                'message' => 'Fill in all required fields.',
                'status'  => 422
            ];
        }

        $dayValidation = $this->validateDayOfWeek($dto->diaSemana);
        if (!$dayValidation['success']) {
            return $dayValidation;
        }

        $inicioValidation = $this->validateTime($dto->inicio);
        if (!$inicioValidation['success']) {
            return $inicioValidation;
        }

        $fimValidation = $this->validateTime($dto->fim);
        if (!$fimValidation['success']) {
            return $fimValidation;
        }

        return ['success' => true];
    }

    public function validateDelete(AgendaDTO $dto): array
    {
        if (empty($dto->id)) {
            return [
                'success' => false,
                'message' => 'ID not provided.',
                'status'  => 422
            ];
        }

        return ['success' => true];
    }
}
