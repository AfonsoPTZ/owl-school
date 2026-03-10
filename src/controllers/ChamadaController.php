<?php

namespace App\Controllers;

use App\Models\Chamada;
use App\Repositories\ChamadaRepository;

class ChamadaController
{
    private ChamadaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new ChamadaRepository($conn);
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

        $data = $_POST['data'] ?? '';

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'Data é obrigatória.'
            ]);
            return;
        }

        $chamada = new Chamada($data);

        $criou = $this->repository->create($chamada);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Chamada criada com sucesso.',
                'id' => $chamada->id
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar chamada.'
        ]);
    }

    /* ============================== */
    /* READ */
    /* ============================== */
    public function read()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $chamadas = $this->repository->findAll();

        echo json_encode([
            'success' => true,
            'chamadas' => $chamadas,
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

        $id = $_POST['id'] ?? '';
        $data = $_POST['data'] ?? '';

        if (empty($id) || empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID e data são obrigatórios.'
            ]);
            return;
        }

        $chamada = new Chamada($data, (int)$id);

        $atualizou = $this->repository->update($chamada);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Chamada atualizada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar chamada.'
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

        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID é obrigatório.'
            ]);
            return;
        }

        $deletou = $this->repository->delete((int)$id);

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
