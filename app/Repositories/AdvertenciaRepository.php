<?php

namespace App\Repositories;

use App\Models\Advertencia;

class AdvertenciaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function createWithAluno(Advertencia $advertencia, int $aluno_id): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO advertencia (titulo, descricao) VALUES (?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ss",
            $advertencia->titulo,
            $advertencia->descricao
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $advertencia->id = $this->conn->insert_id;

            $stmt_relacao = $this->conn->prepare(
                "INSERT INTO aluno_advertencia (advertencia_id, aluno_id) VALUES (?, ?)"
            );

            if (!$stmt_relacao) {
                $stmt->close();
                return false;
            }

            $stmt_relacao->bind_param(
                "ii",
                $advertencia->id,
                $aluno_id
            );

            $relacao_executou = $stmt_relacao->execute();
            $stmt_relacao->close();
            $stmt->close();
            return $relacao_executou;
        }

        $stmt->close();
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

        $stmt->bind_param("i", $id);

        $executou = $stmt->execute();

        if (!$executou) {
            $stmt->close();
            return false;
        }

        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
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

        $executou = $stmt->execute();

        if (!$executou) {
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $advertencias = [];

        while ($linha = $resultado->fetch_assoc()) {
            $advertencias[] = $linha;
        }

        $stmt->close();
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

        $stmt->bind_param(
            "ssi",
            $advertencia->titulo,
            $advertencia->descricao,
            $advertencia->id
        );

        $executou = $stmt->execute();

        if (!$executou) {
            $stmt->close();
            return false;
        }

        $atualizou = $stmt->affected_rows > 0;

        $stmt->close();
        return $atualizou;
    }
}
