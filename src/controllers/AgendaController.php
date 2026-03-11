<?php

namespace App\Controllers;

use App\Models\Agenda;
use App\Repositories\AgendaRepository;
use App\Services\AgendaService;
use App\Utils\Logger;

class AgendaController
{
    private AgendaRepository $repository;
    private AgendaService $service;

    public function __construct($conn)
    {
        $this->repository = new AgendaRepository($conn);
        $this->service = new AgendaService();
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

            $diaSemana = $_POST['dia_semana'];
            $inicio = $_POST['inicio'];
            $fim = $_POST['fim'];
            $disciplina = $_POST['disciplina'];

            $agenda = new Agenda($diaSemana, $inicio, $fim, $disciplina);

            $criou = $this->repository->create($agenda);

            if ($criou) {
                Logger::info("Schedule created: $disciplina on $diaSemana");
                echo json_encode([
                    'success' => true,
                    'message' => 'Horário criado com sucesso.',
                    'id' => $agenda->id
                ]);
                return;
            }

            Logger::error("Failed to create schedule: $disciplina on $diaSemana");
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao criar horário.'
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

            $agendas = $this->repository->findAll();

            // Organizar por dia da semana
            $porDia = [
                'segunda' => [],
                'terca' => [],
                'quarta' => [],
                'quinta' => [],
                'sexta' => []
            ];

            foreach ($agendas as $agenda) {
                if (isset($porDia[$agenda['dia_semana']])) {
                    $porDia[$agenda['dia_semana']][] = $agenda;
                }
            }

            Logger::info("Schedules listed: " . count($agendas) . " found");
            echo json_encode([
                'success' => true,
                'agendas' => $agendas,
                'por_dia' => $porDia,
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
            $diaSemana = $_POST['dia_semana'];
            $inicio = $_POST['inicio'];
            $fim = $_POST['fim'];
            $disciplina = $_POST['disciplina'];

            $agenda = new Agenda($diaSemana, $inicio, $fim, $disciplina, (int)$id);

            $atualizou = $this->repository->update($agenda);

            if ($atualizou) {
                Logger::info("Schedule updated: ID $id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Horário atualizado com sucesso.'
                ]);
                return;
            }

            Logger::warning('Schedule not found for update: ID ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar horário.'
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
                'message' => 'Horário deletado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao deletar horário.'
        ]);
    }
}
