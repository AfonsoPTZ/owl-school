<?php

namespace App\Controllers;

use App\Models\ChamadaItem;
use App\Repositories\ChamadaItemRepository;
use App\Services\ChamadaItemService;
use App\Utils\Logger;

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

            $chamadaId = $_POST['chamada_id'];
            $alunoId = $_POST['aluno_id'];
            $status = $_POST['status'];

            $chamadaItem = new ChamadaItem((int)$chamadaId, (int)$alunoId, $status);

            $criou = $this->repository->create($chamadaItem);

            if ($criou) {
                Logger::info("Attendance item created: Chamada $chamadaId, Student $alunoId, Status $status");
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada criado com sucesso.'
                ]);
                return;
            }

            Logger::error("Failed to create attendance item");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar item de chamada.'
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
    /* READ BY CHAMADA */
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

            $chamadaId = $_GET['chamada_id'] ?? '';

            if (empty($chamadaId)) {
                Logger::warning('Missing chamada_id parameter');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID da chamada é obrigatório.'
                ]);
                return;
            }

            $items = $this->repository->findByChamada((int)$chamadaId);

            Logger::info("Attendance items listed: " . count($items) . " found for attendance $chamadaId");
            echo json_encode([
                'success' => true,
                'itens' => $items,
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

            $chamadaId = $_POST['chamada_id'];
            $alunoId = $_POST['aluno_id'];
            $status = $_POST['status'];

            $chamadaItem = new ChamadaItem((int)$chamadaId, (int)$alunoId, $status);

            $atualizou = $this->repository->update($chamadaItem);

            if ($atualizou) {
                Logger::info("Attendance item updated: Chamada $chamadaId, Student $alunoId");
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada atualizado com sucesso.'
                ]);
                return;
            }

            Logger::warning('Attendance item not found for update');
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar item de chamada.'
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

            $deletou = $this->repository->delete((int) $_POST['chamada_id'], (int) $_POST['aluno_id']);

            if ($deletou) {
                Logger::info("Attendance item deleted: Chamada " . $_POST['chamada_id'] . ", Student " . $_POST['aluno_id']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada deletado com sucesso.'
                ]);
                return;
            }

            Logger::warning('Attendance item not found for delete');
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao deletar item de chamada.'
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
