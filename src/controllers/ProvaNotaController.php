<?php

namespace App\Controllers;

use App\Models\ProvaNota;
use App\Repositories\ProvaNotaRepository;
use App\Services\ProvaNotaService;

class ProvaNotaController
{
    private ProvaNotaRepository $repository;
    private ProvaNotaService $service;

    public function __construct($conn)
    {
        $this->repository = new ProvaNotaRepository($conn);
        $this->service = new ProvaNotaService();
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

        $provaId = $_POST['prova_id'];
        $alunoId = $_POST['aluno_id'];
        $nota = $_POST['nota'];

        $provaNota = new ProvaNota((int)$provaId, (int)$alunoId, (float)$nota);

        $criou = $this->repository->create($provaNota);

        if ($criou) {
            echo json_encode([
                'success' => true,
                'message' => 'Nota criada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar nota.'
        ]);
    }

    /* ============================== */
    /* READ BY PROVA */
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

        $provaId = $_POST['prova_id'] ?? '';

        if (empty($provaId)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID da prova é obrigatório.'
            ]);
            return;
        }

        $notas = $this->repository->findByProva((int)$provaId);

        $titulo_prova = !empty($notas) ? $notas[0]['titulo_prova'] : 'Prova';

        echo json_encode([
            'success' => true,
            'titulo_prova' => $titulo_prova,
            'notas' => $notas,
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

        $provaId = $_POST['prova_id'];
        $alunoId = $_POST['aluno_id'];
        $nota = $_POST['nota'];

        $provaNota = new ProvaNota((int)$provaId, (int)$alunoId, (float)$nota);

        $atualizou = $this->repository->update($provaNota);

        if ($atualizou) {
            echo json_encode([
                'success' => true,
                'message' => 'Nota atualizada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar nota.'
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

        $deletou = $this->repository->delete((int) $_POST['prova_id'], (int) $_POST['aluno_id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Nota deletada com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar nota.'
        ]);
    }
}
