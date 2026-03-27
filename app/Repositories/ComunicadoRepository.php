<?php

namespace App\Repositories;

use App\Models\Comunicado;

class ComunicadoRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO comunicado (titulo, corpo) VALUES (?, ?)"
        );

        if (!$stmt->execute([
            $comunicado->titulo,
            $comunicado->corpo
        ])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $comunicado->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM comunicado WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, titulo, corpo FROM comunicado ORDER BY id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE comunicado SET titulo = ?, corpo = ? WHERE id = ?"
        );

        $stmt->execute([
            $comunicado->titulo,
            $comunicado->corpo,
            $comunicado->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
