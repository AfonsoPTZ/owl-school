<?php

namespace App\Controllers;

use App\Models\Agenda;
use App\Repositories\AgendaRepository;
use App\Services\AgendaService;

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        // Validar dados com o service
        $validacao = $this->service->validarCreate($_POST);
        if (!$validacao['success']) {
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
            echo json_encode([
                'success' => true,
                'message' => 'Horário criado com sucesso.',
                'id' => $agenda->id
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar horário.'
        ]);
    }

    /* ============================== */
    /* READ */
    /* ============================== */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

        echo json_encode([
            'success' => true,
            'agendas' => $agendas,
            'por_dia' => $porDia,
            'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
        ]);
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode([
                'success' => false,
                'message' => 'Método inválido.'
            ]);
            return;
        }

        $validacao = $this->service->validarUpdate($_POST);
        if (!$validacao['success']) {
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
            echo json_encode([
                'success' => true,
                'message' => 'Horário atualizado com sucesso.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar horário.'
        ]);
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
