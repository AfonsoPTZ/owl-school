<?php

namespace App\Validators;

class ChamadaItemValidator
{
    public function validateStatus($status)
    {
        $validStatus = ['presente', 'ausente', 'justificado'];

        if (!in_array(strtolower($status), $validStatus)) {
            return [
                'success' => false,
                'message' => 'Status must be: presente, ausente, or justificado.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Status is valid.'
        ];
    }

    public function validateCreate($chamada_id, $aluno_id, $status)
    {
        return $this->validateStatus($status);
    }
}
