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
    /* GET NAME */
    /* ============================== */
    public function getName()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

