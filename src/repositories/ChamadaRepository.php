<?php

namespace App\Repositories;

use App\Models\Chamada;

class ChamadaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO chamada (data) VALUES (?)"
        );

        $stmt->bind_param(
            "s",
            $chamada->data
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $chamada->id = $this->conn->insert_id;
            $stmt->close();
            return true;
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
            "DELETE FROM chamada WHERE id = ?"
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
            "SELECT id, data FROM chamada ORDER BY data DESC, id DESC"
        );

        $stmt->execute();

        $resultado = $stmt->get_result();
        $chamadas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $chamadas[] = $linha;
        }

        $stmt->close();
        return $chamadas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE chamada SET data = ? WHERE id = ?"
        );

        $data = $chamada->data;
        $id = $chamada->id;

        $stmt->bind_param("si", $data, $id);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
