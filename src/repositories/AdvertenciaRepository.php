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

    /* ============================== */
    /* CREATE  */
    /* ============================== */
    public function createWithAluno(Advertencia $advertencia, int $aluno_id): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO advertencia (titulo, descricao) VALUES (?, ?)"
        );

        $stmt->bind_param(
            "ss",
            $advertencia->titulo,
            $advertencia->descricao
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $advertencia->id = $this->conn->insert_id;

            // Inserir a relação na tabela aluno_advertencia
            $stmt_relacao = $this->conn->prepare(
                "INSERT INTO aluno_advertencia (advertencia_id, aluno_id) VALUES (?, ?)"
            );

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

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM advertencia WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
    }

    /* ============================== */
    /* READ / FIND ALL */
    /* ============================== */
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

        $stmt->execute();

        $resultado = $stmt->get_result();
        $advertencias = [];

        while ($linha = $resultado->fetch_assoc()) {
            $advertencias[] = $linha;
        }

        $stmt->close();
        return $advertencias;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Advertencia $advertencia): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE advertencia SET titulo = ?, descricao = ? WHERE id = ?"
        );

        $titulo = $advertencia->titulo;
        $descricao = $advertencia->descricao;
        $id = $advertencia->id;

        $stmt->bind_param("ssi", $titulo, $descricao, $id);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
