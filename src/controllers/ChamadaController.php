<?php

namespace App\Controllers;

use App\Models\Chamada;
use App\Repositories\ChamadaRepository;
use App\Services\ChamadaService;
use App\Utils\Logger;

class ChamadaController
{
    private ChamadaRepository $repository;
    private ChamadaService $service;

    public function __construct($conn)
    {
        $this->repository = new ChamadaRepository($conn);
        $this->service = new ChamadaService();
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Logger::warning('Invalid method in create');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarCreate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in create');
                echo json_encode($validacao);
                return;
            }

            $data = $_POST['data'];

            $chamada = new Chamada($data);

            $criou = $this->repository->create($chamada);

            if ($criou) {
                Logger::info("Attendance created: $data");
                echo json_encode([
                    'success' => true,
                    'message' => 'Chamada criada com sucesso.',
                    'id' => $chamada->id
                ]);
                return;
            }

            Logger::error("Failed to create attendance: $data");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar chamada.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in create: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* READ */
    /* ============================== */
    public function index()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                Logger::warning('Invalid method in index');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $chamadas = $this->repository->findAll();

            Logger::info("Attendances listed: " . count($chamadas) . " found");
            echo json_encode([
                'success' => true,
                'chamadas' => $chamadas,
                'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in index: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                Logger::warning('Invalid method in update');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarUpdate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in update');
                echo json_encode($validacao);
                return;
            }

            $id = $_POST['id'];
            $data = $_POST['data'];

            $chamada = new Chamada($data, (int)$id);

            $atualizou = $this->repository->update($chamada);

            if ($atualizou) {
                Logger::info("Attendance updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Chamada atualizada com sucesso.'
                ]);
                return;
            }

            Logger::warning('Attendance not found for update: ID ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar chamada.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in update: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $validacao = $this->service->validarDelete($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $deletou = $this->repository->delete((int) $_POST['id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Chamada deletada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar chamada.'
        ]);
    }
}
