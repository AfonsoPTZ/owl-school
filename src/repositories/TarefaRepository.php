<?php

namespace App\Repositories;

use App\Models\Tarefa;

class TarefaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefa (titulo, descricao, data_entrega) VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sss",
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $tarefa->id = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        $stmt->close();
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
            "SELECT id, titulo, descricao, data_entrega FROM tarefa ORDER BY id DESC"
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
        $tarefas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $tarefas[] = $linha;
        }

        $stmt->close();
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

        $stmt->bind_param(
            "sssi",
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega,
            $tarefa->id
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