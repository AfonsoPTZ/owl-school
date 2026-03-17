<?php

namespace App\Services;

use App\DTOs\ComunicadoDTO;
use App\Models\Comunicado;
use App\Repositories\ComunicadoRepository;
use App\Validators\ComunicadoValidator;

class ComunicadoService
{
    private ComunicadoValidator $validator;
    private ComunicadoRepository $repository;

    public function __construct($conn)
    {
        $this->validator = new ComunicadoValidator();
        $this->repository = new ComunicadoRepository($conn);
    }

    public function create(ComunicadoDTO $dto): array
    {
        $validacao = $this->validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $comunicado = new Comunicado($dto->titulo, $dto->corpo);
        $criou = $this->repository->create($comunicado);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Erro ao criar comunicado.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Comunicado criado com sucesso.',
            'status'  => 201
        ];
    }

    public function update(ComunicadoDTO $dto): array
    {
        $validacao = $this->validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $comunicado = new Comunicado($dto->titulo, $dto->corpo, $dto->id);
        $atualizou = $this->repository->update($comunicado);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Notice not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Comunicado atualizado com sucesso.',
            'status'  => 200
        ];
    }

    public function delete(ComunicadoDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Notice not found.',
                'status'  => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Comunicado deletado com sucesso.',
            'status'  => 200
        ];
    }

    public function findAll(): array
    {
        $comunicados = $this->repository->findAll();

        return [
            'success' => true,
            'comunicados' => $comunicados,
            'status'  => 200
        ];
    }
}