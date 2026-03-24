<?php

namespace App\Services\AI;

/**
 * ContextManager - Gerencia contexto de conversa e autenticação
 * 
 * Armazena em sessão:
 * - Contexto da conversa: última intenção, dados, resposta (para follow-ups)
 * - Dados de autenticação: user_id, tipo_usuario, user_name
 * 
 * Permite que o sistema saiba:
 * - Quem é o usuário
 * - Qual foi a última pergunta (para follow-ups)
 * - Quais dados foram já buscados (para reutilizar)
 */
class ContextManager
{
    /**
     * Inicia sessão PHP se ainda não estiver ativa
     * Necessário para armazenar contexto entre requisições
     */
    public function startSessionIfNeeded(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Recupera contexto de conversa anterior
     * 
     * Estrutura:
     * {
     *   "last_intent": "consultar_tarefas",
     *   "last_data": [...],
     *   "last_response": "Você tem 3 tarefas..."
     * }
     */
    public function getConversationContext(): array
    {
        $this->startSessionIfNeeded();

        // Se não existir, inicializa com valores vazios
        if (!isset($_SESSION['ai_context'])) {
            $_SESSION['ai_context'] = [
                'last_intent' => null,
                'last_data' => null,
                'last_response' => null
            ];
        }

        return $_SESSION['ai_context'];
    }

    /**
     * Salva o contexto de conversa após responder
     * 
     * Permite que a próxima pergunta saiba o que foi perguntado antes
     * (usado para detectar follow-ups)
     */
    public function saveConversationContext(string $intent, array $data, string $response): void
    {
        $this->startSessionIfNeeded();

        // Sobrescreve o contexto anterior com o novo
        $_SESSION['ai_context'] = [
            'last_intent' => $intent,
            'last_data' => $data,
            'last_response' => $response
        ];
    }

    /**
     * Recupera dados de autenticação da sessão
     * 
     * Estrutura:
     * {
     *   "user_id": 123,
     *   "tipo_usuario": "aluno",
     *   "user_name": "João Silva"
     * }
     */
    public function getAuthData(): array
    {
        $this->startSessionIfNeeded();

        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null
        ];
    }
}