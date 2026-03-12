<?php

namespace App\Services;

use App\DTOs\ChamadaDTO;
use App\Models\Chamada;
use App\Repositories\ChamadaRepository;
use App\Validators\ChamadaValidator;

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
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $chamada = new Chamada($dto->data);
        $criou = $this->repository->create($chamada);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Error creating attendance.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Attendance created successfully.',
            'status'  => 201
        ];
    }

    public function update(ChamadaDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $chamada = new Chamada($dto->data, $dto->id);
        $atualizou = $this->repository->update($chamada);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Attendance not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'status'  => 200
        ];
    }

    public function delete(ChamadaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Attendance not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Attendance deleted successfully.',
            'status'  => 200
        ];
    }

    public function findAll(): array
    {
        $chamadas = $this->repository->findAll();

        return [
            'success' => true,
            'chamadas' => $chamadas,
            'status'  => 200
        ];
    }
}