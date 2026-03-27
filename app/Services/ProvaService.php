<?php

namespace App\Services;

use App\DTOs\ProvaDTO;
use App\Models\Prova;
use App\Repositories\ProvaRepository;
use App\Validators\ProvaValidator;

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
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $prova = new Prova($dto->titulo, $dto->data);
        $criou = $this->repository->create($prova);

        if (!$criou) {
            return $this->response(false, 'Erro ao criar prova.', 500);
        }

        return $this->response(true, 'Prova criada com sucesso.', 201);
    }

    public function update(ProvaDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $prova = new Prova($dto->titulo, $dto->data, $dto->id);
        $atualizou = $this->repository->update($prova);

        if (!$atualizou) {
            return $this->response(false, 'Prova não encontrada para atualização.', 404);
        }

        return $this->response(true, 'Prova atualizada com sucesso.', 200);
    }

    public function delete(ProvaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Prova não encontrada para exclusão.', 404);
        }

        return $this->response(true, 'Prova deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        $provas = $this->repository->findAll();

        return [
            'success' => true,
            'provas' => $provas,
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