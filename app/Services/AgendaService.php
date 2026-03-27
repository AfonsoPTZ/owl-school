<?php

namespace App\Services;

use App\DTOs\AgendaDTO;
use App\Models\Agenda;
use App\Repositories\AgendaRepository;
use App\Validators\AgendaValidator;

class AgendaService
{
    private AgendaValidator $validator;
    private AgendaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new AgendaValidator();
        $this->repository = new AgendaRepository($conn);
    }

    public function create(AgendaDTO $dto): array
    {
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $agenda = new Agenda($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina);
        $criou = $this->repository->create($agenda);

        if (!$criou) {
            return $this->response(false, 'Erro ao criar agenda.', 500);
        }

        return $this->response(true, 'Agenda criada com sucesso.', 201);
    }

    public function update(AgendaDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $agenda = new Agenda($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina, $dto->id);
        $atualizou = $this->repository->update($agenda);

        if (!$atualizou) {
            return $this->response(false, 'Agenda não encontrada para atualização.', 404);
        }

        return $this->response(true, 'Agenda atualizada com sucesso.', 200);
    }

    public function delete(AgendaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Agenda não encontrada para exclusão.', 404);
        }

        return $this->response(true, 'Agenda deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        $agendas = $this->repository->findAll();
        
        $porDia = [
            'segunda' => [],
            'terca' => [],
            'quarta' => [],
            'quinta' => [],
            'sexta' => []
        ];

        foreach ($agendas as $agenda) {
            $dia = $agenda['dia_semana'];
            if (isset($porDia[$dia])) {
                $porDia[$dia][] = $agenda;
            }
        }

        return [
            'success' => true,
            'por_dia' => $porDia,
            'status' => 200
        ];
    }

    private function response(bool $success, string $message, int $status): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status
        ];
    }
}