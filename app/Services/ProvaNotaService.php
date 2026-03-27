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
        $validator = new ProvaNotaValidator();
        $validacao = $validator->validateCreate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $criou = $this->repository->create($provaNota);

        if (!$criou) {
            return $this->response(false, 'Erro ao criar nota.', 500);
        }

        return $this->response(true, 'Nota criada com sucesso.', 201);
    }

    public function update(ProvaNotaDTO $dto): array
    {
        $validator = new ProvaNotaValidator();
        $validacao = $validator->validateUpdate($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $provaNota = new ProvaNota($dto->provaId, $dto->alunoId, $dto->nota);
        $atualizou = $this->repository->update($provaNota);

        if (!$atualizou) {
            return $this->response(false, 'Nota nao encontrada para atualizacao.', 404);
        }

        return $this->response(true, 'Nota atualizada com sucesso.', 200);
    }

    public function delete(ProvaNotaDTO $dto): array
    {
        $validator = new ProvaNotaValidator();
        $validacao = $validator->validateDelete($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        $deletou = $this->repository->delete((int) $dto->provaId, (int) $dto->alunoId);

        if (!$deletou) {
            return $this->response(false, 'Nota nao encontrada para exclusao.', 404);
        }

        return $this->response(true, 'Nota deletada com sucesso.', 200);
    }

    public function findByProva(int $provaId): array
    {
        $notas = $this->repository->findByProva($provaId);

        if (empty($notas)) {
            return [
                'success' => false,
                'message' => 'Nenhuma nota encontrada para esta prova.',
                'status' => 404
            ];
        }

        $titulo_prova = $notas[0]['titulo_prova'] ?? '';

        return [
            'success' => true,
            'titulo_prova' => $titulo_prova,
            'notas' => $notas,
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
