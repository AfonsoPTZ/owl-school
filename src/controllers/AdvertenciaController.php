<?php

namespace App\Controllers;

use App\Models\Advertencia;
use App\Repositories\AdvertenciaRepository;
use App\Services\AdvertenciaService;

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

        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $aluno_id = $_POST['aluno_id'];

        $advertencia = new Advertencia($titulo, $descricao);

        $criou = $this->repository->createWithAluno($advertencia, (int)$aluno_id);

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
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $validacao = $this->service->validarUpdate($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];

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
