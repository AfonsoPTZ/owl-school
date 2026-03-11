<?php

namespace App\Services;

use App\Validators\ChamadaValidator;
use App\DTOs\ChamadaDTO;
use App\Models\Chamada;
use App\Repositories\ChamadaRepository;

class ChamadaService
{
    private ChamadaValidator $validator;
    private ChamadaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new ChamadaValidator();
        $this->repository = new ChamadaRepository($conn);
    }

    public function create(ChamadaDTO $dto): array
    {
        if (empty($dto->data)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->data);
        if (!$validacao['success']) {
            return $validacao;
        }

        $chamada = new Chamada($dto->data);
        $criou = $this->repository->create($chamada);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar chamada."];
        }

        return ["success" => true, "message" => "Chamada criada com sucesso.", "id" => $chamada->id];
    }

    public function findAll(): array
    {
        $chamadas = $this->repository->findAll();
        return ["success" => true, "chamadas" => $chamadas];
    }

    public function update(ChamadaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dto->data)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->data);
        if (!$validacao['success']) {
            return $validacao;
        }

        $chamada = new Chamada($dto->data, $dto->id);
        $atualizou = $this->repository->update($chamada);

        if (!$atualizou) {
            return ["success" => false, "message" => "Chamada not found."];
        }

        return ["success" => true, "message" => "Chamada atualizada com sucesso."];
    }

    public function delete(ChamadaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return ["success" => false, "message" => "Chamada not found."];
        }

        return ["success" => true, "message" => "Chamada deletada com sucesso."];
    }
}
