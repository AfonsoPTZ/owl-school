<?php

namespace App\Http\Controllers;

use App\DTOs\ProvaDTO;
use App\Services\ProvaService;

class ProvaController extends BaseController
{
    private ProvaService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new ProvaService($conn);
    }

    public function create(): void
    {
        try {
            $dto = new ProvaDTO($_POST);
            $result = $this->service->create($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'create');
        }
    }

    public function index(): void
    {
        try {
            $result = $this->service->findAll();

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'index');
        }
    }

    public function update(): void
    {
        try {
            $dto = new ProvaDTO($_POST);
            $result = $this->service->update($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'update');
        }
    }

    public function delete(): void
    {
        try {
            $dto = new ProvaDTO($_POST);
            $result = $this->service->delete($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'delete');
        }
    }
}

