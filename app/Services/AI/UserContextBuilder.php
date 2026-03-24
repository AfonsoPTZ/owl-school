<?php

namespace App\Services\AI;

use App\Repositories\UtilsResponsavelRepository;

class UserContextBuilder
{
    private UtilsResponsavelRepository $utilsResponsavelRepository;

    public function __construct($conn)
    {
        $this->utilsResponsavelRepository = new UtilsResponsavelRepository($conn);
    }

    public function build(array $authData): array
    {
        $role = $authData['tipo_usuario'] ?? 'desconhecido';
        $userId = $authData['user_id'] ?? null;
        $userName = $authData['user_name'] ?? null;

        $context = [
            'role' => $role,
            'user_id' => $userId,
            'user_name' => $userName,
            'target_type' => 'self',
            'student_id' => null,
            'student_name' => null
        ];

        if ($role === 'responsavel' && $userId) {
            $filho = $this->utilsResponsavelRepository->getAlunoVinculado($userId);

            $context['target_type'] = 'filho';
            $context['student_id'] = $filho['id'] ?? null;
            $context['student_name'] = $filho['nome'] ?? null;
        }

        return $context;
    }
}