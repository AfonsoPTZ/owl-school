<?php

namespace App\Services;

use App\Validators\ComunicadoValidator;
use App\DTOs\ComunicadoDTO;
use App\Models\Comunicado;
use App\Repositories\ComunicadoRepository;

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
        if (empty($dto->titulo) || empty($dto->corpo)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->corpo);
        if (!$validacao['success']) {
            return $validacao;
        }

        $comunicado = new Comunicado($dto->titulo, $dto->corpo);
        $criou = $this->repository->create($comunicado);

        if (!$criou) {
            return ["success" => false, "message" => "Erro ao criar comunicado."];
        }

        return ["success" => true, "message" => "Comunicado criado com sucesso.", "id" => $comunicado->id];
    }

    public function findAll(): array
    {
        $comunicados = $this->repository->findAll();
        return ["success" => true, "comunicados" => $comunicados];
    }

    public function update(ComunicadoDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }
        if (empty($dto->titulo) || empty($dto->corpo)) {
            return ["success" => false, "message" => "Preencha todos os campos obrigatórios."];
        }

        $validacao = $this->validator->validateCreate($dto->titulo, $dto->corpo);
        if (!$validacao['success']) {
            return $validacao;
        }

        $comunicado = new Comunicado($dto->titulo, $dto->corpo, $dto->id);
        $atualizou = $this->repository->update($comunicado);

        if (!$atualizou) {
            return ["success" => false, "message" => "Comunicado not found."];
        }

        return ["success" => true, "message" => "Comunicado atualizado com sucesso."];
    }

    public function delete(ComunicadoDTO $dto): array
    {
        if (!$dto->id) {
            return ["success" => false, "message" => "ID não informado."];
        }

        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return ["success" => false, "message" => "Comunicado not found."];
        }

        return ["success" => true, "message" => "Comunicado deletado com sucesso."];
    }
}
