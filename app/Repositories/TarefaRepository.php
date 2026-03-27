<?php

namespace App\Repositories;

use App\Models\Tarefa;

class TarefaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefa (titulo, descricao, data_entrega) VALUES (?, ?, ?)"
        );

        $executou = $stmt->execute([
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega
        ]);

        if (!$executou || $stmt->rowCount() === 0) {
            return false;
        }

        $tarefa->id = (int) $this->conn->lastInsertId();
        return true;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, titulo, descricao, data_entrega FROM tarefa ORDER BY id DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefa
             SET titulo = ?, descricao = ?, data_entrega = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega,
            $tarefa->id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM tarefa WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}