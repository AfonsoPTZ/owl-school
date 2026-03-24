<?php

namespace App\Http\Controllers;

use App\DTOs\AIDTO;
use App\Services\AI\AIService;

class AIController extends BaseController
{
    private AIService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AIService($conn);
    }

    public function index(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $dto = new AIDTO($data);

            if ($dto->pergunta === '') {
                $this->json([
                    'success' => false,
                    'message' => 'Pergunta obrigatória.'
                ], 400);
                return;
            }

            $result = $this->service->chat($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'AIController::index');
        }
    }

    // Alias para POST requests (HTTP method mapping)
    public function create(): void
    {
        $this->index();
    }
}