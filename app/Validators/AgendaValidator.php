<?php

namespace App\Validators;

use App\DTOs\AgendaDTO;

class AgendaValidator
{
    public function validateCreate(AgendaDTO $dto): array
    {
        if ($this->isBlank($dto->diaSemana) || $this->isBlank($dto->inicio) || $this->isBlank($dto->fim) || $this->isBlank($dto->disciplina)) {
            return $this->error('Preencha todos os campos necessários.');
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
            return $this->error('ID não fornecido.');
        }

        if ($this->isBlank($dto->diaSemana) || $this->isBlank($dto->inicio) || $this->isBlank($dto->fim) || $this->isBlank($dto->disciplina)) {
            return $this->error('Preencha todos os campos necessários.');
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
            return $this->error('ID não fornecido.');
        }

        return ['success' => true];
    }

    private function validateDayOfWeek(string $day): array
    {
        $validDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];

        if (!in_array(strtolower($day), $validDays)) {
            return $this->error('Dia deve ser: segunda, terca, quarta, quinta ou sexta.');
        }

        return ['success' => true];
    }

    private function validateTime(string $time): array
    {
        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return $this->error('Formato de hora deve ser HH:MM (formato 24 horas).');
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
