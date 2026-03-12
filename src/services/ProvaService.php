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
            return [
                'success' => false,
                'message' => 'Error creating test.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Test created successfully.',
            'status'  => 201
        ];
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
            return [
                'success' => false,
                'message' => 'Test not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Test updated successfully.',
            'status'  => 200
        ];
    }

    public function delete(ProvaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Test not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Test deleted successfully.',
            'status'  => 200
        ];
    }

    public function findAll(): array
    {
        $provas = $this->repository->findAll();

        return [
            'success' => true,
            'provas' => $provas,
            'status'  => 200
        ];
    }
}