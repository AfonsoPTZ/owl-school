<?php

namespace App\Http\Middleware;

/**
 * AuthMiddleware - Controle de autenticação e autorização
 * 
 * Nota: Session é inicializada em config/session.php (chamado por index.php)
 * Essa middleware apenas garante que está ativa e verifica permissões
 */
class AuthMiddleware
{
    /**
     * Garante que sessão está ativa (idempotente)
     * Session já foi inicializada com cookies seguros em config/session.php
     */
    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function requireLogin(): void
    {
        self::startSession();

        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            exit;
        }
    }

    public static function requireRole(...$roles): void
    {
        self::startSession();

        if (
            empty($_SESSION['tipo_usuario']) ||
            !in_array($_SESSION['tipo_usuario'], $roles)
        ) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Acesso negado.'
            ]);
            exit;
        }
    }
}
