<?php

namespace App\Http\Controllers;

use App\Repositories\UtilsRepository;

class UtilsController
{
    private UtilsRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new UtilsRepository($conn);
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

        // Converter action para nome de método (ex: getName -> getName)
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
    /* GET NAME */
    /* ============================== */
    public function getName()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $userName = $this->repository->getUserName();

        echo json_encode(['user_name' => $userName]);
    }

    /* ============================== */
    /* GET ALUNO SELECT */
    /* ============================== */
    public function getAlunoSelect()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $alunos = $this->repository->getAllAlunos();

        if (count($alunos) > 0) {
            echo json_encode([
                'success' => true,
                'alunos' => $alunos
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Nenhum aluno encontrado.'
            ]);
        }
    }
}

