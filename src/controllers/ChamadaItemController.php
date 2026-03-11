<?php

namespace App\Controllers;

use App\Models\ChamadaItem;
use App\Repositories\ChamadaItemRepository;
use App\Services\ChamadaItemService;

class ChamadaItemController
{
    private ChamadaItemRepository $repository;
    private ChamadaItemService $service;

    public function __construct($conn)
    {
        $this->repository = new ChamadaItemRepository($conn);
        $this->service = new ChamadaItemService();
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

        $chamadaId = $_POST['chamada_id'];
        $alunoId = $_POST['aluno_id'];
        $status = $_POST['status'];

        $chamadaItem = new ChamadaItem((int)$chamadaId, (int)$alunoId, $status);

        $criou = $this->repository->create($chamadaItem);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Item de chamada criado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar item de chamada.'
        ]);
    }

    /* ============================== */
    /* READ BY CHAMADA */
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

        $chamadaId = $_GET['chamada_id'] ?? '';

        if (empty($chamadaId)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID da chamada é obrigatório.'
            ]);
            return;
        }

        $items = $this->repository->findByChamada((int)$chamadaId);

        echo json_encode([
            'success' => true,
            'itens' => $items,
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

        $chamadaId = $_POST['chamada_id'];
        $alunoId = $_POST['aluno_id'];
        $status = $_POST['status'];

        $chamadaItem = new ChamadaItem((int)$chamadaId, (int)$alunoId, $status);

        $atualizou = $this->repository->update($chamadaItem);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Item de chamada atualizado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar item de chamada.'
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

        $deletou = $this->repository->delete((int) $_POST['chamada_id'], (int) $_POST['aluno_id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Item de chamada deletado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar item de chamada.'
        ]);
    }
}
