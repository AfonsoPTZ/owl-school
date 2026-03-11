<?php

namespace App\Controllers;

use App\DTOs\ProvaDTO;
use App\Services\ProvaService;
use App\Utils\Logger;

class ProvaController
{
    private ProvaService $service;

    public function __construct($conn)
    {
        $this->service = new ProvaService($conn);
    }

    public function create(): void
    {
        try {
            $this->ensureMethod('POST', 'create');

            $dto = new ProvaDTO($_POST);

            $result = $this->service->create($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in create');
                $this->json($result, 422);
                return;
            }

            Logger::info("Test created: {$dto->titulo}");
            $this->json([
                'success' => true,
                'message' => 'Prova criada com sucesso.'
            ], 201);
        } catch (\Exception $e) {
            $this->handleException($e, 'create');
        }
    }

    public function index(): void
    {
        try {
            $this->ensureMethod('GET', 'index');

            $result = $this->service->findAll();

            Logger::info('Tests listed: ' . count($result['provas']) . ' found');

            $this->json([
                'success'      => true,
                'provas'       => $result['provas'],
                'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'index');
        }
    }

    public function update(): void
    {
        try {
            $this->ensureMethod('PUT', 'update');

            $dto = new ProvaDTO($_POST);

            $result = $this->service->update($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in update');
                $status = $result['message'] === 'Prova not found.' ? 404 : 422;
                $this->json($result, $status);
                return;
            }

            Logger::info("Test updated: ID {$dto->id}");
            $this->json([
                'success' => true,
                'message' => 'Prova atualizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'update');
        }
    }

    public function delete(): void
    {
        try {
            $this->ensureMethod('DELETE', 'delete');

            $dto = new ProvaDTO($_POST);

            $result = $this->service->delete($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in delete');
                $status = $result['message'] === 'Prova not found.' ? 404 : 422;
                $this->json($result, $status);
                return;
            }

            Logger::info("Test deleted: ID {$dto->id}");
            $this->json([
                'success' => true,
                'message' => 'Prova deletada com sucesso.'
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'delete');
        }
    }

    private function ensureMethod(string $expectedMethod, string $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
            Logger::warning("Invalid method in {$action}");

            $this->json([
                'success' => false,
                'message' => 'Invalid method.'
            ], 405);

            exit;
        }
    }

    private function getInputData(): array
    {
        $rawInput = file_get_contents('php://input');
        $data = [];

        if (!empty($rawInput)) {
            parse_str($rawInput, $data);
        }

        return $data;
    }

    private function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }

    private function handleException(\Exception $e, string $action): void
    {
        Logger::error("Exception in {$action}: " . $e->getMessage());

        $this->json([
            'success' => false,
            'message' => 'Server error.'
        ], 500);
    }
}
