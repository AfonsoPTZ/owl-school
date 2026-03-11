<?php

namespace App\Services;

use App\Validators\AdvertenciaValidator;
use App\DTOs\AdvertenciaDTO;
use App\Models\Advertencia;
use App\Repositories\AdvertenciaRepository;

class AdvertenciaService
{
    private AdvertenciaValidator $validator;
    private AdvertenciaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new AdvertenciaValidator();
        $this->repository = new AdvertenciaRepository($conn);
    }

    public function create(AdvertenciaDTO $dto): array
    {
        if (empty($dto->titulo) || empty($dto->descricao) || empty($dto->aluno_id)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->descricao);
        if (!$validacao['success']) {
            return $validacao;
        }

        $advertencia = new Advertencia($dto->titulo, $dto->descricao);
        $criou = $this->repository->createWithAluno($advertencia, $dto->aluno_id);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar advertência."];
        }

        return ["success" => true, "message" => "Advertência criada com sucesso.", "id" => $advertencia->id];
    }

    public function findAll(): array
    {
        $advertencias = $this->repository->findAll();
        return ["success" => true, "advertencias" => $advertencias];
    }

    public function update(AdvertenciaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dto->titulo) || empty($dto->descricao)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->descricao);
        if (!$validacao['success']) {
            return $validacao;
        }

        $advertencia = new Advertencia($dto->titulo, $dto->descricao, $dto->id);
        $atualizou = $this->repository->update($advertencia);

        if (!$atualizou) {
            return ["success" => false, "message" => "Advertencia not found."];
        }

        return ["success" => true, "message" => "Advertência atualizada com sucesso."];
    }

    public function delete(AdvertenciaDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return ["success" => false, "message" => "Advertencia not found."];
        }

        return ["success" => true, "message" => "Advertência deletada com sucesso."];
    }
}
