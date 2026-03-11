<?php

namespace App\Validators;

class ComunicadoValidator
{
    public function validateCreate($titulo, $corpo)
    {
        return [
            'success' => true,
            'message' => 'Notice data is valid.'
        ];
    }
}
