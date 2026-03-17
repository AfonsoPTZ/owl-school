<?php

namespace App\Services;

use App\DTOs\TarefaDTO;
use App\Models\Tarefa;
use App\Repositories\TarefaRepository;
use App\Validators\TarefaValidator;

class TarefaService
{
    private TarefaValidator $validator;
    private TarefaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new TarefaValidator();
        $this->repository = new TarefaRepository($conn);
    }

    public function create(TarefaDTO $dto): array
    {
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $tarefa = new Tarefa(
            $dto->titulo,
            $dto->descricao,
            $dto->data_entrega
        );

        $criou = $this->repository->create($tarefa);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Erro ao criar tarefa.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa criada com sucesso.',
            'status'  => 201
        ];
    }

    public function update(TarefaDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $tarefa = new Tarefa(
            $dto->titulo,
            $dto->descricao,
            $dto->data_entrega,
            $dto->id
        );

        $atualizou = $this->repository->update($tarefa);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Task not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa atualizada com sucesso.',
            'status'  => 200
        ];
    }

    public function delete(TarefaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Task not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa deletada com sucesso.',
            'status'  => 200
        ];
    }

    public function findAll(): array
    {
        $tarefas = $this->repository->findAll();

        return [
            'success' => true,
            'tarefas' => $tarefas,
            'status'  => 200
        ];
    }
}