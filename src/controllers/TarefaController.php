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
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar tarefa.'
            ]);
        }
    }
}