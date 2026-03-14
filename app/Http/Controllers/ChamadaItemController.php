<?php

namespace App\Http\Controllers;

use App\DTOs\ChamadaItemDTO;
use App\Repositories\ChamadaItemRepository;
use App\Services\ChamadaItemService;

class ChamadaItemController extends BaseController
{
    private ChamadaItemRepository $repository;
    private ChamadaItemService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->repository = new ChamadaItemRepository($conn);
        $this->service = new ChamadaItemService($conn);
    }

    public function index(): void
    {
        try {
            $chamadaId = $_GET['chamada_id'] ?? '';

            if (empty($chamadaId)) {
                $this->json([
                    'success' => false,
                    'message' => 'ID de chamada é obrigatório.',
                    'status'  => 422
                ], 422);
                return;
            }

            $items = $this->repository->findByChamada((int)$chamadaId);

            $this->json([
                'success' => true,
                'itens' => $items,
                'status'  => 200
            ], 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'index');
        }
    }

    public function create(): void
    {
        try {
            $dto = new ChamadaItemDTO($_POST);
            $result = $this->service->create($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'create');
        }
    }

    public function update(): void
    {
        try {
            $dto = new ChamadaItemDTO($_POST);
            $result = $this->service->update($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'update');
        }
    }

    public function delete(): void
    {
        try {
            $dto = new ChamadaItemDTO($_POST);
            $result = $this->service->delete($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'delete');
        }
    }
}

