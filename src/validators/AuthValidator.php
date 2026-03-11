<?php

namespace App\Validators;

class AuthValidator
{
    public function validateEmail($email)
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Email format is invalid.'
            ];
        }

        if (!preg_match('/@teste\.com$/', $email)) {
            return [
                'success' => false,
                'message' => 'Email must end with @teste.com.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Email is valid.'
        ];
    }

    public function validatePassword($password)
    {
        if (strlen($password) !== 6) {
            return [
                'success' => false,
                'message' => 'Password must be exactly 6 characters.'
            ];
        }

        if (!ctype_digit($password)) {
            return [
                'success' => false,
                'message' => 'Password must contain only numbers (0-9).'
            ];
        }

        return [
            'success' => true,
            'message' => 'Password is valid.'
        ];
    }

    public function validateLogin($email, $password)
    {
        $emailValidation = $this->validateEmail($email);
        if (!$emailValidation['success']) {
            return $emailValidation;
        }

        $passwordValidation = $this->validatePassword($password);
        if (!$passwordValidation['success']) {
            return $passwordValidation;
        }

        return [
            'success' => true,
            'message' => 'All fields are valid.'
        ];
    }
}
