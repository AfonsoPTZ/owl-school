<?php

namespace App\Validators;

class TarefaValidator
{
    public function validateDate($date)
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return [
                'success' => false,
                'message' => 'Delivery date format must be YYYY-MM-DD.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Delivery date is valid.'
        ];
    }

    public function validateCreate($titulo, $descricao, $data_entrega)
    {
        $dateValidation = $this->validateDate($data_entrega);
        if (!$dateValidation['success']) {
            return $dateValidation;
        }

        return [
            'success' => true,
            'message' => 'Task data is valid.'
        ];
    }
}
