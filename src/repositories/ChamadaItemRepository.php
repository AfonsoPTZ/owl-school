<?php

namespace App\Repositories;

use App\Models\ChamadaItem;

class ChamadaItemRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(ChamadaItem $chamadaItem): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO chamada_item (chamada_id, aluno_id, status) VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iis",
            $chamadaItem->chamadaId,
            $chamadaItem->alunoId,
            $chamadaItem->status
        );

        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }

    public function delete(int $chamadaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM chamada_item WHERE chamada_id = ? AND aluno_id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $chamadaId, $alunoId);

        $executou = $stmt->execute();

        if (!$executou) {
            $stmt->close();
            return false;
        }

        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
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

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $chamadaId);

        $executou = $stmt->execute();

        if (!$executou) {
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $items = [];

        while ($linha = $resultado->fetch_assoc()) {
            $items[] = $linha;
        }

        $stmt->close();
        return $items;
    }

    public function update(ChamadaItem $chamadaItem): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE chamada_item SET status = ? WHERE chamada_id = ? AND aluno_id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sii",
            $chamadaItem->status,
            $chamadaItem->chamadaId,
            $chamadaItem->alunoId
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
