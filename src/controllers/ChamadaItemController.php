<?php

namespace App\Controllers;

use App\Models\ChamadaItem;
use App\Repositories\ChamadaItemRepository;
use App\Utils\Logger;
use App\DTOs\ChamadaItemDTO;

class ChamadaItemController
{
    private ChamadaItemRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new ChamadaItemRepository($conn);
    }

    /* ============================== */
    /* INDEX / READ BY CHAMADA */
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
                Logger::warning('Missing chamada_id in index');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID da chamada é obrigatório.'
                ]);
                return;
            }

            $items = $this->repository->findByChamada((int)$chamadaId);

            Logger::info("Chamada items listed: " . count($items) . " found for chamada " . $chamadaId);
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

            $dto = new ChamadaItemDTO($_POST);

            if (empty($dto->chamada_id) || empty($dto->aluno_id) || $dto->status === '') {
                Logger::warning('Validation failed in create - missing required fields');
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos os campos são obrigatórios.'
                ]);
                return;
            }

            $chamadaItem = new ChamadaItem((int)$dto->chamada_id, (int)$dto->aluno_id, $dto->status);

            $criou = $this->repository->create($chamadaItem);

            if ($criou) {
                Logger::info("Chamada item created: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada criado com sucesso.'
                ]);
                return;
            }

            Logger::error("Failed to create chamada item: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
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

            $dto = new ChamadaItemDTO($_POST);

            if (empty($dto->chamada_id) || empty($dto->aluno_id) || $dto->status === '') {
                Logger::warning('Validation failed in update - missing required fields');
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos os campos são obrigatórios.'
                ]);
                return;
            }

            $chamadaItem = new ChamadaItem((int)$dto->chamada_id, (int)$dto->aluno_id, $dto->status);

            $atualizou = $this->repository->update($chamadaItem);

            if ($atualizou) {
                Logger::info("Chamada item updated: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada atualizado com sucesso.'
                ]);
                return;
            }

            Logger::warning("Chamada item not found for update: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
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

            $dto = new ChamadaItemDTO($_POST);

            if (empty($dto->chamada_id) || empty($dto->aluno_id)) {
                Logger::warning('Validation failed in delete - missing required fields');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID da chamada e ID do aluno são obrigatórios.'
                ]);
                return;
            }

            $deletou = $this->repository->delete((int)$dto->chamada_id, (int)$dto->aluno_id);

            if ($deletou) {
                Logger::info("Chamada item deleted: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item de chamada deletado com sucesso.'
                ]);
                return;
            }

            Logger::warning("Chamada item not found for delete: chamada " . $dto->chamada_id . " aluno " . $dto->aluno_id);
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
