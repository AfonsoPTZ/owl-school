<?php

namespace App\Validators;

use App\DTOs\AuthDTO;

class AuthValidator
{
    public function validateLogin(AuthDTO $dto): array
    {
        if (empty(trim($dto->email ?? '')) || empty(trim($dto->senha ?? ''))) {
            return [
                'success' => false,
                'message' => 'Preencha todos os campos obrigatórios.',
                'status'  => 422
            ];
        }

        $emailValidation = $this->validateEmail($dto->email);
        if (!$emailValidation['success']) {
            return $emailValidation;
        }

        $passwordValidation = $this->validatePassword($dto->senha);
        if (!$passwordValidation['success']) {
            return $passwordValidation;
        }

        return [
            'success' => true
        ];
    }

    private function validateEmail(string $email): array
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Email format is invalid.',
                'status'  => 422
            ];
        }

        if (!preg_match('/@teste\.com$/', $email)) {
            return [
                'success' => false,
                'message' => 'Email must end with @teste.com.',
                'status'  => 422
            ];
        }

        return [
            'success' => true
        ];
    }

    private function validatePassword(string $password): array
    {
        if (strlen($password) !== 6) {
            return [
                'success' => false,
                'message' => 'Password must be exactly 6 characters.',
                'status'  => 422
            ];
        }

        if (!ctype_digit($password)) {
            return [
                'success' => false,
                'message' => 'Password must contain only numbers (0-9).',
                'status'  => 422
            ];
        }

        return [
            'success' => true
        ];
    }
}