<?php

namespace App\Controllers;

use App\Repositories\UtilsAlunoRepository;

class UtilsAlunoController
{
    private UtilsAlunoRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new UtilsAlunoRepository($conn);
    }

    /* ============================== */
    /* GET ADVERTENCIAS */
    /* ============================== */
    public function getAdvertencias()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $usuarioId = $_SESSION['user_id'] ?? null;

        if (!$usuarioId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $advertencias = $this->repository->getAdvertencias($usuarioId);

        echo json_encode([
            'success' => true,
            'advertencias' => $advertencias
        ]);
    }

    /* ============================== */
    /* GET FREQUENCIAS */
    /* ============================== */
    public function getFrequencias()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $alunoId = $_SESSION['user_id'] ?? null;

        if (!$alunoId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $frequencias = $this->repository->getFrequencias($alunoId);

        echo json_encode([
            'success' => true,
            'frequencias' => $frequencias
        ]);
    }

    /* ============================== */
    /* GET NOTAS */
    /* ============================== */
    public function getNotas()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $usuarioId = $_SESSION['user_id'] ?? null;

        if (!$usuarioId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $notas = $this->repository->getNotas($usuarioId);

        echo json_encode([
            'success' => true,
            'notas' => $notas
        ]);
    }

    /* ============================== */
    /* GET NOME RESPONSAVEL */
    /* ============================== */
    public function getNomeResponsavel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $alunoId = $_SESSION['user_id'] ?? null;

        if (!$alunoId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $nomeResponsavel = $this->repository->getNomeResponsavel($alunoId);

        if ($nomeResponsavel) {
            echo json_encode([
                'success' => true,
                'nome_responsavel' => $nomeResponsavel
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Responsável não encontrado.'
            ]);
        }
    }
}
