<?php

namespace App\Controllers;

use App\DTOs\ComunicadoDTO;
use App\Services\ComunicadoService;
use App\Utils\Logger;

class ComunicadoController
{
    private ComunicadoService $service;

    public function __construct($conn)
    {
        $this->service = new ComunicadoService($conn);
    }

    public function create(): void
    {
        try {
            $this->ensureMethod('POST', 'create');

            $dto = new ComunicadoDTO($_POST);

            $result = $this->service->create($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in create');
                $this->json($result, 422);
                return;
            }

            Logger::info("Notice created: {$dto->titulo}");
            $this->json([
                'success' => true,
                'message' => 'Comunicado criado com sucesso.'
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

            Logger::info('Notices listed: ' . count($result['comunicados']) . ' found');

            $this->json([
                'success' => true,
                'comunicados' => $result['comunicados'],
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

            $dto = new ComunicadoDTO($_POST);

            $result = $this->service->update($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in update');
                $status = $result['message'] === 'Comunicado not found.' ? 404 : 422;
                $this->json($result, $status);
                return;
            }

            Logger::info("Notice updated: ID {$dto->id}");
            $this->json([
                'success' => true,
                'message' => 'Comunicado atualizado com sucesso.'
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'update');
        }
    }

    public function delete(): void
    {
        try {
            $this->ensureMethod('DELETE', 'delete');

            $dto = new ComunicadoDTO($_POST);

            $result = $this->service->delete($dto);

            if (!$result['success']) {
                Logger::warning('Validation failed in delete');
                $status = $result['message'] === 'Comunicado not found.' ? 404 : 422;
                $this->json($result, $status);
                return;
            }

            Logger::info("Notice deleted: ID {$dto->id}");
            $this->json([
                'success' => true,
                'message' => 'Comunicado deletado com sucesso.'
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
