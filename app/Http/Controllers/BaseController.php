<?php

namespace App\Http\Controllers;

use App\Utils\Logger;

class BaseController
{
    protected ?\mysqli $conn;

    public function __construct($conn = null)
    {
        $this->conn = $conn;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }

    protected function handleException(\Throwable $e, string $action): void
    {
        Logger::error("Exception in {$action}: " . $e->getMessage());

        $this->json([
            'success' => false,
            'message' => 'Erro no servidor.'
        ], 500);
    }
}
