<?php

namespace App\Controllers;

use App\Models\Comunicado;
use App\Repositories\ComunicadoRepository;
use App\Services\ComunicadoService;

class ComunicadoController
{
    private ComunicadoRepository $repository;
    private ComunicadoService $service;

    public function __construct($conn)
    {
        $this->repository = new ComunicadoRepository($conn);
        $this->service = new ComunicadoService();
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
        $corpo = $_POST['corpo'];

        $comunicado = new Comunicado($titulo, $corpo);

        $criou = $this->repository->create($comunicado);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Comunicado criado com sucesso.',
                'id' => $comunicado->id
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar comunicado.'
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

        $comunicados = $this->repository->findAll();

        echo json_encode([
            'success' => true,
            'comunicados' => $comunicados,
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

        $validacao = $this->service->validarUpdate($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $corpo = $_POST['corpo'];

        $comunicado = new Comunicado($titulo, $corpo, (int)$id);

        $atualizou = $this->repository->update($comunicado);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Comunicado atualizado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar comunicado.'
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

        $validacao = $this->service->validarDelete($_POST);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            return;
        }

        $deletou = $this->repository->delete((int) $_POST['id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Comunicado deletado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar comunicado.'
        ]);
    }
}
