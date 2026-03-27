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
            return $this->response(false, 'Erro ao criar advertência.', 500);
        }

        return $this->response(true, 'Advertência criada com sucesso.', 201);
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
            return $this->response(false, 'Advertência não encontrada para atualização.', 404);
        }

        return $this->response(true, 'Advertência atualizada com sucesso.', 200);
    }

    public function delete(AdvertenciaDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Advertência não encontrada para exclusão.', 404);
        }

        return $this->response(true, 'Advertência deletada com sucesso.', 200);
    }

    public function findAll(): array
    {
        $advertencias = $this->repository->findAll();

        return [
            'success' => true,
            'advertencias' => $advertencias,
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
