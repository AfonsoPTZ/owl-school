<?php

namespace App\Services;

use App\Validators\AgendaValidator;
use App\DTOs\AgendaDTO;
use App\Models\Agenda;
use App\Repositories\AgendaRepository;

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
        if (empty($dto->diaSemana) || empty($dto->inicio) || empty($dto->fim) || empty($dto->disciplina)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina);
        if (!$validacao['success']) {
            return $validacao;
        }

        $agenda = new Agenda($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina);
        $criou = $this->repository->create($agenda);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar horário."];
        }

        return ["success" => true, "message" => "Horário criado com sucesso.", "id" => $agenda->id];
    }

    public function findAll(): array
    {
        $agendas = $this->repository->findAll();
        return ["success" => true, "agendas" => $agendas];
    }

    public function update(AgendaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dto->diaSemana) || empty($dto->inicio) || empty($dto->fim) || empty($dto->disciplina)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina);
        if (!$validacao['success']) {
            return $validacao;
        }

        $agenda = new Agenda($dto->diaSemana, $dto->inicio, $dto->fim, $dto->disciplina, $dto->id);
        $atualizou = $this->repository->update($agenda);

        if (!$atualizou) {
            return ["success" => false, "message" => "Agenda not found."];
        }

        return ["success" => true, "message" => "Horário atualizado com sucesso."];
    }

    public function delete(AgendaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return ["success" => false, "message" => "Agenda not found."];
        }

        return ["success" => true, "message" => "Horário deletado com sucesso."];
    }
}
