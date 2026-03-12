<?php

namespace App\Controllers;

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

    /* ============================== */
    /* INDEX / READ */
    /* ============================== */
    public function index(): void
    {
        try {
            $provaId = $_GET['prova_id'] ?? '';

            if (empty($provaId)) {
                $this->json([
                    'success' => false,
                    'message' => 'ID da prova é obrigatório.',
                    'status' => 422
                ], 422);
                return;
            }

            $result = $this->service->findByProva((int)$provaId);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'index');
        }
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(): void
    {
        try {
            $dto = new ProvaNotaDTO($_POST);
            $result = $this->service->create($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'create');
        }
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(): void
    {
        try {
            $dto = new ProvaNotaDTO($_POST);
            $result = $this->service->update($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'update');
        }
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(): void
    {
        try {
            $dto = new ProvaNotaDTO($_POST);
            $result = $this->service->delete($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'delete');
        }
    }
}
