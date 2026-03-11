<?php

namespace App\Controllers;

use App\Models\ProvaNota;
use App\Repositories\ProvaNotaRepository;
use App\Services\ProvaNotaService;
use App\Utils\Logger;

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
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Logger::warning('Invalid method in create');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarCreate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in create');
                echo json_encode($validacao);
                return;
            }

            $provaId = $_POST['prova_id'];
            $alunoId = $_POST['aluno_id'];
            $nota = $_POST['nota'];

            $provaNota = new ProvaNota((int)$provaId, (int)$alunoId, (float)$nota);

            $criou = $this->repository->create($provaNota);

            if ($criou) {
                Logger::info("Test grade created: Test $provaId, Student $alunoId, Grade $nota");
                echo json_encode([
                    'success' => true,
                    'message' => 'Nota criada com sucesso.'
                ]);
                return;
            }

            Logger::error("Failed to create test grade");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar nota.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in create: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* READ BY PROVA */
    /* ============================== */
    public function index()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                Logger::warning('Invalid method in index');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $provaId = $_GET['prova_id'] ?? '';

            if (empty($provaId)) {
                Logger::warning('Missing prova_id parameter');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID da prova é obrigatório.'
                ]);
                return;
            }

            $notas = $this->repository->findByProva((int)$provaId);

            $titulo_prova = !empty($notas) ? $notas[0]['titulo_prova'] : 'Prova';

            Logger::info("Test grades listed: " . count($notas) . " found for test $provaId");
            echo json_encode([
                'success' => true,
                'titulo_prova' => $titulo_prova,
                'notas' => $notas,
                'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in index: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                Logger::warning('Invalid method in update');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarUpdate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in update');
                echo json_encode($validacao);
                return;
            }

            $provaId = $_POST['prova_id'];
            $alunoId = $_POST['aluno_id'];
            $nota = $_POST['nota'];

            $provaNota = new ProvaNota((int)$provaId, (int)$alunoId, (float)$nota);

            $atualizou = $this->repository->update($provaNota);

            if ($atualizou) {
                Logger::info("Test grade updated: Test $provaId, Student $alunoId, Grade $nota");
                echo json_encode([
                    'success' => true,
                    'message' => 'Nota atualizada com sucesso.'
                ]);
                return;
            }

            Logger::warning('Test grade not found for update');
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar nota.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in update: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                Logger::warning('Invalid method in delete');
                echo json_encode([
                    'success' => false,
                    'message' => 'Método inválido.'
                ]);
                return;
            }

            $validacao = $this->service->validarDelete($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validation failed in delete');
                echo json_encode($validacao);
                return;
            }

            $deletou = $this->repository->delete((int) $_POST['prova_id'], (int) $_POST['aluno_id']);

            if ($deletou) {
                Logger::info("Test grade deleted: Test " . $_POST['prova_id'] . ", Student " . $_POST['aluno_id']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Nota deletada com sucesso.'
                ]);
                return;
            }

            Logger::warning('Test grade not found for delete');
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao deletar nota.'
            ]);
        } catch (\Exception $e) {
            Logger::error("Exception in delete: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
    }
}
