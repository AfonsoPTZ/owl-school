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
            return $this->response(false, 'Erro ao criar chamada.', 500);
        }

        return $this->response(true, 'Chamada criada com sucesso.', 201);
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
            return $this->response(false, 'Chamada não encontrada para atualização.', 404);
        }

        return $this->response(true, 'Chamada atualizada com sucesso.', 200);
    }

    public function delete(ChamadaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Chamada não encontrada para exclusão.', 404);
        }

        return $this->response(true, 'Chamada deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        $chamadas = $this->repository->findAll();

        return [
            'success' => true,
            'chamadas' => $chamadas,
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