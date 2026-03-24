<?php

namespace App\Repositories;

use App\Models\Advertencia;

class AdvertenciaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function createWithAluno(Advertencia $advertencia, int $aluno_id): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO advertencia (titulo, descricao) VALUES (?, ?)"
        );

        if (!$stmt->execute([
            $advertencia->titulo,
            $advertencia->descricao
        ])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $advertencia->id = $this->conn->lastInsertId();

            $stmt_relacao = $this->conn->prepare(
                "INSERT INTO aluno_advertencia (advertencia_id, aluno_id) VALUES (?, ?)"
            );

            if (!$stmt_relacao) {
                return false;
            }

            return $stmt_relacao->execute([
                $advertencia->id,
                $aluno_id
            ]);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM advertencia WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT 
                advertencia.id,
                advertencia.titulo,
                advertencia.descricao,
                usuario.nome AS aluno_nome
            FROM advertencia
            LEFT JOIN aluno_advertencia
                ON aluno_advertencia.advertencia_id = advertencia.id
            LEFT JOIN usuario
                ON usuario.id = aluno_advertencia.aluno_id
            ORDER BY advertencia.id DESC"
        );

        if (!$stmt) {
            return [];
        }

        if (!$stmt->execute()) {
            return [];
        }

        $advertencias = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $advertencias[] = $linha;
        }

        return $advertencias;
    }

    public function update(Advertencia $advertencia): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE advertencia SET titulo = ?, descricao = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->execute([
            $advertencia->titulo,
            $advertencia->descricao,
            $advertencia->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
