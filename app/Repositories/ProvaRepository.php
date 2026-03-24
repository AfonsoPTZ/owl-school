<?php

namespace App\Repositories;

use App\Models\Prova;

class ProvaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(Prova $prova): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova (titulo, data) VALUES (?, ?)"
        );

        if (!$stmt->execute([
            $prova->titulo,
            $prova->data
        ])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $prova->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova WHERE id = ?"
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
            "SELECT id, titulo, data FROM prova ORDER BY data DESC, id DESC"
        );

        if (!$stmt) {
            return [];
        }

        if (!$stmt->execute()) {
            return [];
        }

        $provas = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $provas[] = $linha;
        }

        return $provas;
    }

    public function update(Prova $prova): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE prova SET titulo = ?, data = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->execute([
            $prova->titulo,
            $prova->data,
            $prova->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
