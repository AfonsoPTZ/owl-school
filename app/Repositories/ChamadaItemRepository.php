<?php

namespace App\Repositories;

use App\Models\ChamadaItem;

class ChamadaItemRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(ChamadaItem $chamadaItem): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO chamada_item (chamada_id, aluno_id, status) VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            $chamadaItem->chamadaId,
            $chamadaItem->alunoId,
            $chamadaItem->status
        ]);
    }

    public function delete(int $chamadaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM chamada_item WHERE chamada_id = ? AND aluno_id = ?"
        );

        $stmt->execute([$chamadaId, $alunoId]);

        return $stmt->rowCount() > 0;
    }

    public function findByChamada(int $chamadaId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                chamada.id AS chamada_id,
                aluno.usuario_id AS aluno_id,
                usuario.nome AS aluno_nome,
                chamada_item.status AS status,
                chamada.data AS data_chamada
            FROM aluno
            JOIN usuario
                ON usuario.id = aluno.usuario_id
            JOIN chamada
                ON chamada.id = ?
            LEFT JOIN chamada_item
                ON chamada_item.aluno_id = aluno.usuario_id
                AND chamada_item.chamada_id = chamada.id
            ORDER BY usuario.nome"
        );

        $stmt->execute([$chamadaId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(ChamadaItem $chamadaItem): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE chamada_item SET status = ? WHERE chamada_id = ? AND aluno_id = ?"
        );

        $stmt->execute([
            $chamadaItem->status,
            $chamadaItem->chamadaId,
            $chamadaItem->alunoId
        ]);

        return $stmt->rowCount() > 0;
    }
}
