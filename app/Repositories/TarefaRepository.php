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

        if (!$stmt->execute([
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega
        ])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $tarefa->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM tarefa WHERE id = ?"
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
            "SELECT id, titulo, descricao, data_entrega FROM tarefa ORDER BY id DESC"
        );

        if (!$stmt) {
            return [];
        }

        if (!$stmt->execute()) {
            return [];
        }

        $tarefas = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tarefas[] = $linha;
        }

        return $tarefas;
    }

    public function update(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefa SET titulo = ?, descricao = ?, data_entrega = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->execute([
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega,
            $tarefa->id
        ]);

        return $stmt->rowCount() > 0;
    }
}