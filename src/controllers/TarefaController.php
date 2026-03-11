<?php

namespace App\Controllers;

use App\Models\Tarefa;
use App\Repositories\TarefaRepository;
use App\Services\TarefaService;

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        // Validar dados com o service
        $validacao = $this->service->validarCreate($_POST);
        if (!$validacao['success']) {
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
            echo json_encode([
                'success' => true,
                'message' => 'Tarefa criada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar tarefa.'
        ]);
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

        // Validar dados com o service
        $validacao = $this->service->validarDelete($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $deletou = $this->repository->delete((int) $_POST['id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Tarefa excluída com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Nenhuma tarefa encontrada para excluir.'
        ]);
    }

    /* ============================== */
    /* READ / INDEX */
    /* ============================== */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $tarefas = $this->repository->findAll();

        echo json_encode([
            'success'      => true,
            'tarefas'      => $tarefas,
            'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
        ]);
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        // Validar dados com o service
        $validacao = $this->service->validarUpdate($_POST);
        if (!$validacao['success']) {
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
            echo json_encode([
                'success' => true,
                'message' => 'Tarefa atualizada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Nenhum elemento encontrado para atualizar.'
        ]);
    }
}