<?php

namespace App\Controllers;

use App\Models\Advertencia;
use App\Repositories\AdvertenciaRepository;

class AdvertenciaController
{
    private AdvertenciaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new AdvertenciaRepository($conn);
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

        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        if (empty($titulo) || empty($descricao)) {
            echo json_encode([
                'success' => false,
                'message' => 'Título e descrição são obrigatórios.'
            ]);
            return;
        }

        $advertencia = new Advertencia($titulo, $descricao);

        $criou = $this->repository->create($advertencia);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Advertência criada com sucesso.',
                'id' => $advertencia->id
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar advertência.'
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

        $advertencias = $this->repository->findAll();

        echo json_encode([
            'success' => true,
            'advertencias' => $advertencias,
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
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        if (empty($id) || empty($titulo) || empty($descricao)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID, título e descrição são obrigatórios.'
            ]);
            return;
        }

        $advertencia = new Advertencia($titulo, $descricao, (int)$id);

        $atualizou = $this->repository->update($advertencia);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Advertência atualizada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar advertência.'
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
