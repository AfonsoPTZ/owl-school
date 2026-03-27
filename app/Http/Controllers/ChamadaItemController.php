<?php

namespace App\Http\Controllers;

use App\DTOs\ChamadaItemDTO;
use App\Repositories\ChamadaItemRepository;
use App\Services\ChamadaItemService;

class ChamadaItemController extends BaseController
{
    private ChamadaItemService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new ChamadaItemService($conn);
    }

    public function index(): void
    {
        $this->executeAction(function() {
            $chamadaId = $_GET['chamada_id'] ?? '';

            if (empty($chamadaId)) {
                return [
                    'success' => false,
                    'message' => 'ID de chamada e obrigatorio.',
                    'status' => 422
                ];
            }

            $repository = new ChamadaItemRepository($GLOBALS['conn'] ?? null);
            $items = $repository->findByChamada((int)$chamadaId);

            return [
                'success' => true,
                'itens' => $items,
                'status' => 200
            ];
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
            $dto = new ChamadaItemDTO($_POST);
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

