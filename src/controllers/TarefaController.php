<?php

namespace App\Controllers;

use App\Models\Tarefa;
use App\Repositories\TarefaRepository;
use App\Services\TarefaService;
use App\Utils\Logger;

class TarefaController
{
    private TarefaRepository $repository;
    private TarefaService $service;

    public function __construct($conn)
    {
        $this->repository = new TarefaRepository($conn);
        $this->service = new TarefaService();
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
                    'message' => 'Invalid method.'
                ]);
                return;
            }

            $validacao = $this->service->validarCreate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in create');
                echo json_encode($validacao);
                return;
            }

            $titulo       = $_POST['titulo'];
            $descricao    = $_POST['descricao'];
            $data_entrega = $_POST['data_entrega'];

            $tarefa = new Tarefa(
                $titulo,
                $descricao,
                $data_entrega
            );

            $criou = $this->repository->create($tarefa);

            if ($criou) {
                Logger::info("Task created: $titulo");
                echo json_encode([
                    'success' => true,
                    'message' => 'Task created successfully.'
                ]);
                return;
            }

            Logger::error("Failed to create task: $titulo");
            echo json_encode([
                'success' => false,
                'message' => 'Error creating task.'
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
    /* DELETE */
    /* ============================== */
    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                Logger::warning('Invalid method in delete');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid method.'
                ]);
                return;
            }

            $validacao = $this->service->validarDelete($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in delete');
                echo json_encode($validacao);
                return;
            }

            $deletou = $this->repository->delete((int) $_POST['id']);

            if ($deletou) {
                Logger::info("Task deleted: ID " . $_POST['id']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Task deleted successfully.'
                ]);
                return;
            }

            Logger::warning('Task not found for delete: ID ' . $_POST['id']);
            echo json_encode([
                'success' => false,
                'message' => 'Task not found.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in delete: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* READ / INDEX */
    /* ============================== */
    public function index()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                Logger::warning('Invalid method in index');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid method.'
                ]);
                return;
            }

            $tarefas = $this->repository->findAll();
            Logger::info("Tasks listed: " . count($tarefas) . " found");

            echo json_encode([
                'success'      => true,
                'tarefas'      => $tarefas,
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
                    'message' => 'Invalid method.'
                ]);
                return;
            }

            $validacao = $this->service->validarUpdate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in update');
                echo json_encode($validacao);
                return;
            }

            $id           = $_POST['id'];
            $titulo       = $_POST['titulo'];
            $descricao    = $_POST['descricao'];
            $data_entrega = $_POST['data_entrega'];

            $tarefa = new Tarefa(
                $titulo,
                $descricao,
                $data_entrega,
                (int) $id
            );

            $atualizou = $this->repository->update($tarefa);

            if ($atualizou) {
                Logger::info("Task updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Task updated successfully.'
                ]);
                return;
            }

            Logger::warning("Task not found for update: ID $id");
            echo json_encode([
                'success' => false,
                'message' => 'Task not found.'
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
}
