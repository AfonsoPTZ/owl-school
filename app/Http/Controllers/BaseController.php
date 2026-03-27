<?php

namespace App\Http\Controllers;

use App\Utils\Logger;


class BaseController
{
    protected ?\PDO $conn;

    public function __construct($conn = null)
    {
        $this->conn = $conn;
    }

    /**
     * Retorna resposta JSON com status HTTP
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }

    /**
     * Trata exceção: loga erro e retorna resposta 500
     */
    protected function handleException(\Throwable $e, string $action): void
    {
        Logger::error("Exception in {$action}: " . $e->getMessage());

        $this->json([
            'success' => false,
            'message' => 'Erro no servidor.'
        ], 500);
    }
}
