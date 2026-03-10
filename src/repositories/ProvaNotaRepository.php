<?php

namespace App\Repositories;

use App\Models\ProvaNota;

class ProvaNotaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova_nota (prova_id, aluno_id, nota) VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "iid",
            $provaNota->provaId,
            $provaNota->alunoId,
            $provaNota->nota
        );

        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $provaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova_nota WHERE prova_id = ? AND aluno_id = ?"
        );

        $stmt->bind_param("ii", $provaId, $alunoId);
        $stmt->execute();

        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
    }

    /* ============================== */
    /* READ / FIND BY PROVA */
    /* ============================== */
    public function findByProva(int $provaId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                prova_nota.prova_id,
                prova_nota.aluno_id,
                usuario.nome AS aluno_nome,
                prova_nota.nota
            FROM prova_nota
            JOIN usuario
                ON usuario.id = prova_nota.aluno_id
            WHERE prova_nota.prova_id = ?
            ORDER BY usuario.nome"
        );

        $stmt->bind_param("i", $provaId);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $notas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $notas[] = $linha;
        }

        $stmt->close();
        return $notas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE prova_nota SET nota = ? WHERE prova_id = ? AND aluno_id = ?"
        );

        $nota = $provaNota->nota;
        $provaId = $provaNota->provaId;
        $alunoId = $provaNota->alunoId;

        $stmt->bind_param("dii", $nota, $provaId, $alunoId);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
