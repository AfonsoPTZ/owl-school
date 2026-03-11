<?php

namespace App\Services;

use App\Validators\ChamadaItemValidator;
use App\DTOs\ChamadaItemDTO;
use App\Models\ChamadaItem;
use App\Repositories\ChamadaItemRepository;

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
        if (!$dto->chamadaId || !$dto->alunoId || empty($dto->status)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->chamadaId, $dto->alunoId, $dto->status);
        if (!$validacao['success']) {
            return $validacao;
        }

        $chamadaItem = new ChamadaItem($dto->chamadaId, $dto->alunoId, $dto->status);
        $criou = $this->repository->create($chamadaItem);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar chamada item."];
        }

        return ["success" => true, "message" => "Chamada item criada com sucesso."];
    }

    public function findAll(): array
    {
        $chamadaItems = $this->repository->findAll();
        return ["success" => true, "chamadaItems" => $chamadaItems];
    }

    public function update(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId || empty($dto->status)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->chamadaId, $dto->alunoId, $dto->status);
        if (!$validacao['success']) {
            return $validacao;
        }

        $chamadaItem = new ChamadaItem($dto->chamadaId, $dto->alunoId, $dto->status);
        $atualizou = $this->repository->update($chamadaItem);

        if (!$atualizou) {
            return ["success" => false, "message" => "Chamada item not found."];
        }

        return ["success" => true, "message" => "Chamada item atualizada com sucesso."];
    }

    public function delete(ChamadaItemDTO $dto): array
    {
        if (!$dto->chamadaId || !$dto->alunoId) {
            return ["success" => false, "message" => "ID da chamada e do aluno são obrigatórios."];
        }

        $deletou = $this->repository->delete((int) $dto->chamadaId, (int) $dto->alunoId);

        if (!$deletou) {
            return ["success" => false, "message" => "Chamada item not found."];
        }

        return ["success" => true, "message" => "Chamada item deletada com sucesso."];
    }
}
