<?php

namespace App\Validators;

class AdvertenciaValidator
{
    public function validateCreate($titulo, $descricao)
    {
        return [
            'success' => true,
            'message' => 'Warning data is valid.'
        ];
    }
}
