<?php

namespace App\Controllers;

use App\Models\Prova;
use App\Repositories\ProvaRepository;
use App\Services\ProvaService;
use App\Utils\Logger;

class ProvaController
{
    private ProvaRepository $repository;
    private ProvaService $service;

    public function __construct($conn)
    {
        $this->repository = new ProvaRepository($conn);
        $this->service = new ProvaService();
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

            $titulo = $_POST['titulo'];
            $data = $_POST['data'];

            $prova = new Prova($titulo, $data);

            $criou = $this->repository->create($prova);

            if ($criou) {
                Logger::info("Test created: $titulo on $data");
                echo json_encode([
                    'success' => true,
                    'message' => 'Prova criada com sucesso.',
                    'id' => $prova->id
                ]);
                return;
            }

            Logger::error("Failed to create test: $titulo");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar prova.'
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

            $provas = $this->repository->findAll();

            Logger::info("Tests listed: " . count($provas) . " found");
            echo json_encode([
                'success' => true,
                'provas' => $provas,
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
            $titulo = $_POST['titulo'];
            $data = $_POST['data'];

            $prova = new Prova($titulo, $data, (int)$id);

            $atualizou = $this->repository->update($prova);

            if ($atualizou) {
                Logger::info("Test updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Prova atualizada com sucesso.'
                ]);
                return;
            }

            Logger::warning('Test not found for update: ID ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar prova.'
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
                'message' => 'Prova deletada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar prova.'
        ]);
    }
}
