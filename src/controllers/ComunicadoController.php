<?php

namespace App\Controllers;

use App\Models\Comunicado;
use App\Repositories\ComunicadoRepository;
use App\Services\ComunicadoService;
use App\Utils\Logger;

class ComunicadoController
{
    private ComunicadoRepository $repository;
    private ComunicadoService $service;

    public function __construct($conn)
    {
        $this->repository = new ComunicadoRepository($conn);
        $this->service = new ComunicadoService();
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

            $titulo = $_POST['titulo'];
            $corpo = $_POST['corpo'];

            $comunicado = new Comunicado($titulo, $corpo);

            $criou = $this->repository->create($comunicado);

            if ($criou) {
                Logger::info("Notice created: $titulo");
                echo json_encode([
                    'success' => true,
                    'message' => 'Comunicado criado com sucesso.',
                    'id' => $comunicado->id
                ]);
                return;
            }

            Logger::error("Failed to create notice: $titulo");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar comunicado.'
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
    /* READ */
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

            $comunicados = $this->repository->findAll();

            Logger::info("Notices listed: " . count($comunicados) . " found");
            echo json_encode([
                'success' => true,
                'comunicados' => $comunicados,
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

            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $corpo = $_POST['corpo'];

            $comunicado = new Comunicado($titulo, $corpo, (int)$id);

            $atualizou = $this->repository->update($comunicado);

            if ($atualizou) {
                Logger::info("Notice updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Comunicado atualizado com sucesso.'
                ]);
                return;
            }

            Logger::warning('Notice not found for update: ID ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar comunicado.'
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
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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

        $deletou = $this->repository->delete((int) $_POST['id']);

        if ($deletou) {
            echo json_encode([
                'success' => true,
                'message' => 'Comunicado deletado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar comunicado.'
        ]);
    }
}
