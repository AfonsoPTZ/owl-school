<?php

namespace App\Controllers;

use App\Models\Chamada;
use App\Repositories\ChamadaRepository;
use App\Services\ChamadaService;

class ChamadaController
{
    private ChamadaRepository $repository;
    private ChamadaService $service;

    public function __construct($conn)
    {
        $this->repository = new ChamadaRepository($conn);
        $this->service = new ChamadaService();
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

        $data = $_POST['data'];

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
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $data = $_POST['data'];

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
