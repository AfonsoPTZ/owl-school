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

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, data FROM chamada ORDER BY data DESC, id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(Chamada $chamada): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE chamada SET data = ? WHERE id = ?"
        );

        $stmt->execute([
            $chamada->data,
            $chamada->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
