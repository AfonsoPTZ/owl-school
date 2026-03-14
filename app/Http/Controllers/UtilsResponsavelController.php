<?php

namespace App\Http\Controllers;

use App\Repositories\UtilsResponsavelRepository;

class UtilsResponsavelController
{
    private UtilsResponsavelRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new UtilsResponsavelRepository($conn);
    }

    /* ============================== */
    /* GET ADVERTENCIAS FILHO */
    /* ============================== */
    public function getAdvertenciasFilho()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $responsavelId = $_SESSION['user_id'] ?? null;

        if (!$responsavelId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $advertencias = $this->repository->getAdvertenciasFilho($responsavelId);

        echo json_encode([
            'success' => true,
            'advertencias' => $advertencias
        ]);
    }

    /* ============================== */
    /* GET FREQUENCIAS FILHO */
    /* ============================== */
    public function getFrequenciasFilho()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $responsavelId = $_SESSION['user_id'] ?? null;

        if (!$responsavelId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $frequencias = $this->repository->getFrequenciasFilho($responsavelId);

        echo json_encode([
            'success' => true,
            'frequencias' => $frequencias
        ]);
    }

    /* ============================== */
    /* GET NOTAS FILHO */
    /* ============================== */
    public function getNotasFilho()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $responsavelId = $_SESSION['user_id'] ?? null;

        if (!$responsavelId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $notas = $this->repository->getNotasFilho($responsavelId);

        echo json_encode([
            'success' => true,
            'notas' => $notas
        ]);
    }

    /* ============================== */
    /* GET NOME FILHO */
    /* ============================== */
    public function getNomeFilho()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $responsavelId = $_SESSION['user_id'] ?? null;

        if (!$responsavelId) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ]);
            return;
        }

        $nomeFilho = $this->repository->getNomeFilho($responsavelId);

        if ($nomeFilho) {
            echo json_encode([
                'success' => true,
                'nome_filho' => $nomeFilho
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Filho não encontrado.'
            ]);
        }
    }
}

