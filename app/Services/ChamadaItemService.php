<?php

namespace App\Services;

use App\DTOs\ChamadaItemDTO;
use App\Models\ChamadaItem;
use App\Repositories\ChamadaItemRepository;
use App\Validators\ChamadaItemValidator;

class ChamadaItemService
{
    private ChamadaItemValidator $validator;
    private ChamadaItemRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new ChamadaItemValidator();
        $this->repository = new ChamadaItemRepository($conn);
    }

    public function create(ChamadaItemDTO $dto): array
    {
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $chamadaItem = new ChamadaItem($dto->chamadaId, $dto->alunoId, $dto->status);
        $criou = $this->repository->create($chamadaItem);

        if (!$criou) {
            return $this->response(false, 'Erro ao registrar chamada do aluno.', 500);
        }

        return $this->response(true, 'Chamada do aluno registrada com sucesso.', 201);
    }

    public function update(ChamadaItemDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $chamadaItem = new ChamadaItem($dto->chamadaId, $dto->alunoId, $dto->status);
        $atualizou = $this->repository->update($chamadaItem);

        if (!$atualizou) {
            return $this->response(false, 'Item de chamada não encontrado para atualização.', 404);
        }

        return $this->response(true, 'Chamada do aluno atualizada com sucesso.', 200);
    }

    public function delete(ChamadaItemDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->chamadaId, (int) $dto->alunoId);

        if (!$deletou) {
            return $this->response(false, 'Item de chamada nao encontrado para exclusao.', 404);
        }

        return $this->response(true, 'Chamada do aluno deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        return [
            'success' => true,
            'chamadaItems' => [],
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