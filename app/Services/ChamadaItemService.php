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
            return [
                'success' => false,
                'message' => 'Erro ao registrar chamada do aluno.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Chamada do aluno registrada com sucesso.',
            'status'  => 201
        ];
    }

    public function findAll(): array
    {
        return [
            'success' => true,
            'chamadaItems' => [],
            'status'  => 200
        ];
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
            return [
                'success' => false,
                'message' => 'Attendance item not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Chamada do aluno atualizada com sucesso.',
            'status'  => 200
        ];
    }

    public function delete(ChamadaItemDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->chamadaId, (int) $dto->alunoId);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Attendance item not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Chamada do aluno deletada com sucesso.',
            'status'  => 200
        ];
    }
}