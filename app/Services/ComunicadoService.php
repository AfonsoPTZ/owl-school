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
            return $this->response(false, 'Erro ao criar comunicado.', 500);
        }

        return $this->response(true, 'Comunicado criado com sucesso.', 201);
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
            return $this->response(false, 'Comunicado não encontrado para atualização.', 404);
        }

        return $this->response(true, 'Comunicado atualizado com sucesso.', 200);
    }

    public function delete(ComunicadoDTO $dto): array
    {
        $validacao = $this->validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return $this->response(false, 'Comunicado não encontrado para exclusão.', 404);
        }

        return $this->response(true, 'Comunicado deletado com sucesso.', 200);
    }

    public function findAll(): array
    {
        $comunicados = $this->repository->findAll();

        return [
            'success' => true,
            'comunicados' => $comunicados,
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