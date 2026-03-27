<?php

namespace App\Http\Controllers;

use App\DTOs\AgendaDTO;
use App\Services\AgendaService;

class AgendaController extends BaseController
{
    private AgendaService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AgendaService($conn);
    }

    public function create(): void
    {
        $this->executeWithDto('create');
    }

    public function index(): void
    {
        $this->executeAction(fn() => $this->service->findAll(), 'index');
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
            $dto = new AgendaDTO($_POST);
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

        
    



