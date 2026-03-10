<?php

namespace App\Controllers;

use App\Models\Prova;
use App\Repositories\ProvaRepository;

class ProvaController
{
    private ProvaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new ProvaRepository($conn);
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
        $data = $_POST['data'] ?? '';

        if (empty($titulo) || empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'Título e data são obrigatórios.'
            ]);
            return;
        }

        $prova = new Prova($titulo, $data);

        $criou = $this->repository->create($prova);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Prova criada com sucesso.',
                'id' => $prova->id
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar prova.'
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

        $provas = $this->repository->findAll();

        echo json_encode([
            'success' => true,
            'provas' => $provas,
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
        $data = $_POST['data'] ?? '';

        if (empty($id) || empty($titulo) || empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID, título e data são obrigatórios.'
            ]);
            return;
        }

        $prova = new Prova($titulo, $data, (int)$id);

        $atualizou = $this->repository->update($prova);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Prova atualizada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar prova.'
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
