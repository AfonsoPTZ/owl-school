<?php

namespace App\Http\Controllers;

use App\DTOs\ProvaNotaDTO;
use App\Services\ProvaNotaService;

class ProvaNotaController extends BaseController
{
    private ProvaNotaService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new ProvaNotaService($conn);
    }

    public function index(): void
    {
        $this->executeAction(function() {
            $provaId = $_GET['prova_id'] ?? '';

            if (empty($provaId)) {
                return [
                    'success' => false,
                    'message' => 'ID da prova e obrigatorio.',
                    'status' => 422
                ];
            }

            return $this->service->findByProva((int)$provaId);
        }, 'index');
    }

    public function create(): void
    {
        $this->executeWithDto('create');
    }

    public function update(): void
    {
        $this->executeWithDto('update');
    }

    public function delete(): void
    {
        $this->executeWithDto('delete');
    }

    private function executeWithDto(string $action): void
    {
        $this->executeAction(function () use ($action) {
            $dto = new ProvaNotaDTO($_POST);
            return $this->service->$action($dto);
        }, $action);
    }

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

