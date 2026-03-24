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
    /* INDEX - Entry point para actions */
    /* ============================== */
    public function index()
    {
        $action = $_POST['action'] ?? $_GET['action'] ?? null;

        if (!$action) {
            echo json_encode([
                'success' => false,
                'message' => 'Ação não especificada.'
            ]);
            return;
        }

        // Converter action para nome de método (ex: getNomeFilho -> getNomeFilho)
        $methodName = $action;

        if (!method_exists($this, $methodName)) {
            echo json_encode([
                'success' => false,
                'message' => "Ação '{$action}' não encontrada."
            ]);
            return;
        }

        $this->$methodName();
    }

    /* ============================== */
    /* GET ADVERTENCIAS FILHO */
    /* ============================== */
    public function getAdvertenciasFilho()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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

