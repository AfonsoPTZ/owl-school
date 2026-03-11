<?php

namespace App\Services;

use App\Validators\ProvaValidator;
use App\DTOs\ProvaDTO;
use App\Models\Prova;
use App\Repositories\ProvaRepository;

class ProvaService
{
    private ProvaValidator $validator;
    private ProvaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new ProvaValidator();
        $this->repository = new ProvaRepository($conn);
    }

    public function create(ProvaDTO $dto): array
    {
        if (empty($dto->titulo) || empty($dto->data)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->data);
        if (!$validacao['success']) {
            return $validacao;
        }

        $prova = new Prova($dto->titulo, $dto->data);
        $criou = $this->repository->create($prova);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar prova."];
        }

        return ["success" => true, "message" => "Prova criada com sucesso.", "id" => $prova->id];
    }

    public function findAll(): array
    {
        $provas = $this->repository->findAll();
        return ["success" => true, "provas" => $provas];
    }

    public function update(ProvaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dto->titulo) || empty($dto->data)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->data);
        if (!$validacao['success']) {
            return $validacao;
        }

        $prova = new Prova($dto->titulo, $dto->data, $dto->id);
        $atualizou = $this->repository->update($prova);

        if (!$atualizou) {
            return ["success" => false, "message" => "Prova not found."];
        }

        return ["success" => true, "message" => "Prova atualizada com sucesso."];
    }

    public function delete(ProvaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return ["success" => false, "message" => "Prova not found."];
        }

        return ["success" => true, "message" => "Prova deletada com sucesso."];
    }
}
