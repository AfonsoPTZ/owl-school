<?php

namespace App\Services\AI;

class ContextManager
{
    public function startSessionIfNeeded(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getConversationContext(): array
    {
        $this->startSessionIfNeeded();

        if (!isset($_SESSION['ai_context'])) {
            $_SESSION['ai_context'] = [
                'last_intent' => null,
                'last_data' => null,
                'last_response' => null
            ];
        }

        return $_SESSION['ai_context'];
    }

    public function saveConversationContext(string $intent, array $data, string $response): void
    {
        $this->startSessionIfNeeded();

        $_SESSION['ai_context'] = [
            'last_intent' => $intent,
            'last_data' => $data,
            'last_response' => $response
        ];
    }

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