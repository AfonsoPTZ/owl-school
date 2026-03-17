<?php

namespace App\Http\Controllers;

use App\DTOs\AIDTO;
use App\Services\AIService;

class AIController extends BaseController
{
    private AIService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AIService($conn);
    }

    public function chat(): void
    {
        try {
            $dto = new AIDTO($_POST);
            $result = $this->service->chat($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'chat');
        }
    }
}