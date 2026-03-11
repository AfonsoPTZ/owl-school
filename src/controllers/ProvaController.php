<?php

namespace App\Controllers;

use App\Models\Prova;
use App\Repositories\ProvaRepository;
use App\Services\ProvaService;

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
        $data = $_POST['data'];

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
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $data = $_POST['data'];

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
