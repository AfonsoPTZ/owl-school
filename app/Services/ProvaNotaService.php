<?php

namespace App\Services;

use App\Validators\ProvaNotaValidator;
use App\DTOs\ProvaNotaDTO;
use App\Models\ProvaNota;
use App\Repositories\ProvaNotaRepository;

class ProvaNotaService
{
    private ProvaNotaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new ProvaNotaRepository($conn);
    }

    public function create(ProvaNotaDTO $dto): array
    {
        $validator = new ProvaNotaValidator($dto);
        $validacao = $validator->validateCreate();

        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $criou = $this->repository->create($provaNota);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Erro ao criar nota.',
                'status' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Nota criada com sucesso.',
            'status' => 201
        ];
    }

    public function findAll(): array
    {
        return [
            'success' => true,
            'message' => 'FindAll não implementado para ProvaNota. Use findByProva.',
            'status' => 200
        ];
    }

    public function findByProva(int $provaId): array
    {
        $notas = $this->repository->findByProva($provaId);

        // Extrair titulo_prova da primeira nota se existir
        $titulo_prova = '';
        if (!empty($notas)) {
            $titulo_prova = $notas[0]['titulo_prova'] ?? '';
        }

        return [
            'success' => true,
            'titulo_prova' => $titulo_prova,
            'notas' => $notas,
            'status' => 200
        ];
    }

    public function update(ProvaNotaDTO $dto): array
    {
        $validator = new ProvaNotaValidator($dto);
        $validacao = $validator->validateUpdate();

        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $atualizou = $this->repository->update($provaNota);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Nota não encontrada.',
                'status' => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Nota atualizada com sucesso.',
            'status' => 200
        ];
    }

    public function delete(ProvaNotaDTO $dto): array
    {
        $validator = new ProvaNotaValidator($dto);
        $validacao = $validator->validateDelete();

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->provaId, (int) $dto->alunoId);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Nota não encontrada.',
                'status' => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Nota deletada com sucesso.',
            'status' => 200
        ];
    }
}
