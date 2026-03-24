<?php

namespace App\Repositories;

use App\Models\Chamada;

class ChamadaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO chamada (data) VALUES (?)"
        );

        if (!$stmt->execute([$chamada->data])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $chamada->id = $this->conn->lastInsertId();
            return true;
        }

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

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, data FROM chamada ORDER BY data DESC, id DESC"
        );

        if (!$stmt) {
            return [];
        }

        if (!$stmt->execute()) {
            return [];
        }

        $chamadas = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $chamadas[] = $linha;
        }

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

        $stmt->execute([
            $chamada->data,
            $chamada->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
