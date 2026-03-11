<?php

namespace App\Services;

use App\Validators\TarefaValidator;
use App\DTOs\TarefaDTO;
use App\Models\Tarefa;
use App\Repositories\TarefaRepository;

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
        if (empty($dto->titulo) || empty($dto->descricao) || empty($dto->data_entrega)) {
            return [
                "success" => false,
                "message" => "Fill in all required fields."
            ];
        }

        $validacao = $this->validator->validateCreate(
            $dto->titulo,
            $dto->descricao,
            $dto->data_entrega
        );

        if (!$validacao["success"]) {
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
                "success" => false,
                "message" => "Error creating task."
            ];
        }

        return [
            "success" => true,
            "message" => "Task created successfully."
        ];
    }

    public function delete(TarefaDTO $dto): array
    {
        if (!$dto->id) {
            return [
                "success" => false,
                "message" => "ID not provided."
            ];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                "success" => false,
                "message" => "Task not found."
            ];
        }

        return [
            "success" => true,
            "message" => "Task deleted successfully."
        ];
    }

    public function update(TarefaDTO $dto): array
    {
        if (!$dto->id) {
            return [
                "success" => false,
                "message" => "ID not provided."
            ];
        }

        if (empty($dto->titulo) || empty($dto->descricao) || empty($dto->data_entrega)) {
            return [
                "success" => false,
                "message" => "Fill in all required fields."
            ];
        }

        $validacao = $this->validator->validateCreate(
            $dto->titulo,
            $dto->descricao,
            $dto->data_entrega
        );

        if (!$validacao["success"]) {
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
                "success" => false,
                "message" => "Task not found."
            ];
        }

        return [
            "success" => true,
            "message" => "Task updated successfully."
        ];
    }

    public function findAll(): array
    {
        $tarefas = $this->repository->findAll();

        return [
            "success" => true,
            "tarefas" => $tarefas
        ];
    }
}