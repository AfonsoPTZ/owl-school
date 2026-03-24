<?php

/**
 * Configuração centralizada de Sessão
 * 
 * Aplicado globalmente em index.php
 * Parâmetros de segurança:
 * - lifetime: 0 = expira quando browser fecha
 * - path: / = válido em todo domínio
 * - httponly: true = não acessível via JavaScript (XSS protection)
 * - samesite: Lax = proteção contra CSRF
 * - secure: false = HTTP (true em produção HTTPS)
 */

// Configure antes de iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,           // Expira quando browser fecha
        'path' => '/',             // Disponível em todo o domínio
        'httponly' => true,        // JavaScript não acessa (XSS protection)
        'samesite' => 'Lax',       // CSRF protection
        // 'secure' => false       // Set true em produção HTTPS
    ]);
    
    session_start();
}

