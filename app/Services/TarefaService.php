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
            return $this->response(false, 'Erro ao criar tarefa.', 500);
        }

        return $this->response(true, 'Tarefa criada com sucesso.', 201);
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
            return $this->response(false, 'Tarefa não encontrada para atualização.', 404);
        }

        return $this->response(true, 'Tarefa atualizada com sucesso.', 200);
    }

    public function delete(TarefaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Tarefa não encontrada para exclusão.', 404);
        }

        return $this->response(true, 'Tarefa deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        $tarefas = $this->repository->findAll();

        return [
            'success' => true,
            'tarefas' => $tarefas,
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