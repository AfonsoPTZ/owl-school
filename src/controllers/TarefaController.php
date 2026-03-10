<?php

namespace App\Controllers;

use App\Models\Tarefa;
use App\Repositories\TarefaRepository;

class TarefaController
{
    private TarefaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new TarefaRepository($conn);
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

        $titulo       = $_POST['titulo'] ?? '';
        $descricao    = $_POST['descricao'] ?? '';
        $data_entrega = $_POST['data_entrega'] ?? '';

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID não informado.'
            ]);
            return;
        }

        $deletou = $this->repository->delete((int) $id);

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $id           = $_POST['id'] ?? null;
        $titulo       = $_POST['titulo'] ?? '';
        $descricao    = $_POST['descricao'] ?? '';
        $data_entrega = $_POST['data_entrega'] ?? '';

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID não informado.'
            ]);
            return;
        }

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