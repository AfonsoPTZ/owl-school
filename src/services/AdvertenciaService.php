<?php

namespace App\Services;

use App\DTOs\AdvertenciaDTO;
use App\Models\Advertencia;
use App\Repositories\AdvertenciaRepository;
use App\Validators\AdvertenciaValidator;

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
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $advertencia = new Advertencia($dto->titulo, $dto->descricao);
        $criou = $this->repository->createWithAluno($advertencia, $dto->aluno_id);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Error creating warning.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Warning created successfully.',
            'status'  => 201
        ];
    }

    public function update(AdvertenciaDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $advertencia = new Advertencia($dto->titulo, $dto->descricao, $dto->id);
        $atualizou = $this->repository->update($advertencia);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Warning not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Warning updated successfully.',
            'status'  => 200
        ];
    }

    public function delete(AdvertenciaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Warning not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Warning deleted successfully.',
            'status'  => 200
        ];
    }

    public function findAll(): array
    {
        $advertencias = $this->repository->findAll();

        return [
            'success' => true,
            'advertencias' => $advertencias,
            'status'  => 200
        ];
    }
}
