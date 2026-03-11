<?php

namespace App\Controllers;

use App\Services\AgendaService;
use App\Utils\Logger;
use App\DTOs\AgendaDTO;

class AgendaController
{
    private AgendaService $service;

    public function __construct($conn)
    {
        $this->service = new AgendaService($conn);
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

            $dto = new AgendaDTO($_POST);

            $result = $this->service->create($dto);
            echo json_encode($result);
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

            $result = $this->service->findAll();
            
            // Organizar por dia da semana
            $porDia = [
                'segunda' => [],
                'terca' => [],
                'quarta' => [],
                'quinta' => [],
                'sexta' => []
            ];

            if (!empty($result['agendas'])) {
                foreach ($result['agendas'] as $agenda) {
                    if (isset($porDia[$agenda['dia_semana']])) {
                        $porDia[$agenda['dia_semana']][] = $agenda;
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'agendas' => $result['agendas'] ?? [],
                'por_dia' => $porDia
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

            $dto = new AgendaDTO($_POST);

            $result = $this->service->update($dto);
            echo json_encode($result);
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

            $dto = new AgendaDTO($_POST);

            $result = $this->service->delete($dto);
            echo json_encode($result);
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

