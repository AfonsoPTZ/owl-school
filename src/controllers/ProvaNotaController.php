<?php

namespace App\Controllers;

use App\Models\ProvaNota;
use App\Repositories\ProvaNotaRepository;

class ProvaNotaController
{
    private ProvaNotaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new ProvaNotaRepository($conn);
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

        $provaId = $_POST['prova_id'] ?? '';
        $alunoId = $_POST['aluno_id'] ?? '';
        $nota = $_POST['nota'] ?? '';

        if (empty($provaId) || empty($alunoId) || $nota === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Todos os campos são obrigatórios.'
            ]);
            return;
        }

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

        echo json_encode([
            'success' => true,
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

        $provaId = $_POST['prova_id'] ?? '';
        $alunoId = $_POST['aluno_id'] ?? '';
        $nota = $_POST['nota'] ?? '';

        if (empty($provaId) || empty($alunoId) || $nota === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Todos os campos são obrigatórios.'
            ]);
            return;
        }

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

        $provaId = $_POST['prova_id'] ?? '';
        $alunoId = $_POST['aluno_id'] ?? '';

        if (empty($provaId) || empty($alunoId)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID da prova e ID do aluno são obrigatórios.'
            ]);
            return;
        }

        $deletou = $this->repository->delete((int)$provaId, (int)$alunoId);

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
