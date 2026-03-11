<?php

namespace App\Validators;

class ChamadaValidator
{
    public function validateDate($date)
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Attendance date format must be YYYY-MM-DD.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Attendance date is valid.'
        ];
    }

    public function validateCreate($data)
    {
        return $this->validateDate($data);
    }
}
