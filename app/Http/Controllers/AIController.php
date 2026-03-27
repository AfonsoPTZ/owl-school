<?php

namespace App\Http\Controllers;

use App\DTOs\AIDTO;
use App\Services\AI\AIService;

/**
 * AIController - Orquestrador de requisições de IA
 * 
 * Responsabilidades:
 * - Receber requisições de chat/pergunta
 * - Encaminhar para AIService
 * - Retornar respostas formatadas
 */
class AIController extends BaseController
{
    private AIService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AIService($conn);
    }

    /**
     * Chat - Endpoint principal para processar perguntas
     */
    public function index(): void
    {
        $this->executeAction(function () {
            $data = $this->getInputData();
            $dto = new AIDTO($data);
            return $this->service->chat($dto);
        }, 'chat');
    }

    /**
     * Create - Alias para POST requests
     */
    public function create(): void
    {
        $this->index();
    }

    /**
     * Recupera dados do request (JSON ou FormData)
     */
    private function getInputData(): array
    {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true) ?? [];

        if (empty($data)) {
            $data = $_POST;
        }

        return $data;
    }

    /**
     * Executa ação com tratamento de erro
     */
    private function executeAction(callable $callback, string $action): void
    {
        try {
            $result = $callback();
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, $action);
        }
    }
}