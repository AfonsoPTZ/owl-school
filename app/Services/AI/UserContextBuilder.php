<?php

namespace App\Services\AI;

use App\Repositories\UtilsResponsavelRepository;

/**
 * UserContextBuilder - Constrói o contexto do usuário atual
 * 
 * Prepara informações sobre quem é o usuário para que o Gemini
 * possa adaptar as respostas e as referências corretas.
 * 
 * Exemplos de contexto:
 * - Aluno: role=student, user_id=123, student_name=João
 * - Responsável: role=responsavel, user_id=456, student_id=123, student_name=João (seu filho)
 * - Professor: role=professor, user_id=789
 */
class UserContextBuilder
{
    private UtilsResponsavelRepository $utilsResponsavelRepository;

    public function __construct($conn)
    {
        $this->utilsResponsavelRepository = new UtilsResponsavelRepository($conn);
    }

    /**
     * Constrói contexto do usuário a partir de dados de autenticação
     * 
     * Retorna array com:
     * - role: 'aluno', 'responsavel', 'professor', etc
     * - user_id: ID do usuário logado
     * - user_name: Nome do usuário logado
     * - target_type: 'self' (para o próprio) ou 'filho' (para responsável)
     * - student_id: ID do aluno (mesmo se responsável)
     * - student_name: Nome do aluno (importante para responsável)
     */
    public function build(array $authData): array
    {
        $role = $authData['tipo_usuario'] ?? 'desconhecido';
        $userId = $authData['user_id'] ?? null;
        $userName = $authData['user_name'] ?? null;

        // Contexto base para todos os usuários
        $context = [
            'role' => $role,
            'user_id' => $userId,
            'user_name' => $userName,
            'target_type' => 'self',      // Por padrão, consulta do próprio
            'student_id' => null,
            'student_name' => null
        ];

        // Se for responsável, busca seu filho vinculado
        if ($role === 'responsavel' && $userId) {
            $filho = $this->utilsResponsavelRepository->getAlunoVinculado($userId);

            $context['target_type'] = 'filho';
            $context['student_id'] = $filho['id'] ?? null;
            // Nome do filho (importante para o Gemini personalizr respostas)
            $context['student_name'] = $filho['nome'] ?? null;
        }

        return $context;
    }
}