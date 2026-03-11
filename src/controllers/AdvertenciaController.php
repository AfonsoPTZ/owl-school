<?php

namespace App\Controllers;

use App\Models\Advertencia;
use App\Repositories\AdvertenciaRepository;
use App\Services\AdvertenciaService;
use App\Utils\Logger;

class AdvertenciaController
{
    private AdvertenciaRepository $repository;
    private AdvertenciaService $service;

    public function __construct($conn)
    {
        $this->repository = new AdvertenciaRepository($conn);
        $this->service = new AdvertenciaService();
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
            $descricao = $_POST['descricao'];
            $aluno_id = $_POST['aluno_id'];

            $advertencia = new Advertencia($titulo, $descricao);

            $criou = $this->repository->createWithAluno($advertencia, (int)$aluno_id);

            if ($criou) {
                Logger::info("Warning created: $titulo for student $aluno_id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Advertência criada com sucesso.',
                    'id' => $advertencia->id
                ]);
                return;
            }

            Logger::error("Failed to create warning: $titulo");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar advertência.'
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

            $advertencias = $this->repository->findAll();

            Logger::info("Warnings listed: " . count($advertencias) . " found");
            echo json_encode([
                'success' => true,
                'advertencias' => $advertencias,
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
            $descricao = $_POST['descricao'];

            $advertencia = new Advertencia($titulo, $descricao, (int)$id);

            $atualizou = $this->repository->update($advertencia);

            if ($atualizou) {
                Logger::info("Warning updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Advertência atualizada com sucesso.'
                ]);
                return;
            }

            Logger::warning('Warning not found for update: ID ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar advertência.'
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
                'message' => 'Advertência deletada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar advertência.'
        ]);
    }
}
