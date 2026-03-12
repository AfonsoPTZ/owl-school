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

    public function create(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO chamada (data) VALUES (?)"
        );

        if (!$stmt) {
            return false;
        }

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

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM chamada WHERE id = ?"
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
            "SELECT id, data FROM chamada ORDER BY data DESC, id DESC"
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
        $chamadas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $chamadas[] = $linha;
        }

        $stmt->close();
        return $chamadas;
    }

    public function update(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE chamada SET data = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $chamada->data,
            $chamada->id
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
