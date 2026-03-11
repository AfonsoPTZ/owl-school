<?php

namespace App\Services;

use App\Validators\ProvaNotaValidator;
use App\DTOs\ProvaNotaDTO;
use App\Models\ProvaNota;
use App\Repositories\ProvaNotaRepository;

class ProvaNotaService
{
    private ProvaNotaValidator $validator;
    private ProvaNotaRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new ProvaNotaValidator();
        $this->repository = new ProvaNotaRepository($conn);
    }

    public function create(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId || $dto->nota === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->provaId, $dto->alunoId, $dto->nota);
        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $criou = $this->repository->create($provaNota);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar nota."];
        }

        return ["success" => true, "message" => "Nota criada com sucesso."];
    }

    public function findAll(): array
    {
        $provaNotas = $this->repository->findAll();
        return ["success" => true, "provaNotas" => $provaNotas];
    }

    public function update(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId || $dto->nota === '') {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->provaId, $dto->alunoId, $dto->nota);
        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $atualizou = $this->repository->update($provaNota);

        if (!$atualizou) {
            return ["success" => false, "message" => "Prova nota not found."];
        }

        return ["success" => true, "message" => "Nota atualizada com sucesso."];
    }

    public function delete(ProvaNotaDTO $dto): array
    {
        if (!$dto->provaId || !$dto->alunoId) {
            return ["success" => false, "message" => "ID da prova e do aluno são obrigatórios."];
        }

        $deletou = $this->repository->delete((int) $dto->provaId, (int) $dto->alunoId);

        if (!$deletou) {
            return ["success" => false, "message" => "Prova nota not found."];
        }

        return ["success" => true, "message" => "Nota deletada com sucesso."];
    }
}
