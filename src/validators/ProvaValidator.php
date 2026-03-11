<?php

namespace App\Validators;

class ProvaValidator
{
    public function validateDate($date)
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Test date format must be YYYY-MM-DD.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Test date is valid.'
        ];
    }

    public function validateCreate($titulo, $data)
    {
        $dateValidation = $this->validateDate($data);
        if (!$dateValidation['success']) {
            return $dateValidation;
        }

        return [
            'success' => true,
            'message' => 'Test data is valid.'
        ];
    }
}
