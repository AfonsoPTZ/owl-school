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

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefa (titulo, descricao, data_entrega) VALUES (?, ?, ?)"
        );

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

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM tarefa WHERE id = ?"
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
            "SELECT id, titulo, descricao, data_entrega FROM tarefa ORDER BY id DESC"
        );

        $stmt->execute();

        $resultado = $stmt->get_result();
        $tarefas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $tarefas[] = $linha;
        }

        $stmt->close();
        return $tarefas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefa SET titulo = ?, descricao = ?, data_entrega = ? WHERE id = ?"
        );

        $titulo      = $tarefa->titulo;
        $descricao   = $tarefa->descricao;
        $dataEntrega = $tarefa->dataEntrega;
        $id          = $tarefa->id;

        $stmt->bind_param("sssi", $titulo, $descricao, $dataEntrega, $id);
        $stmt->execute();

        $atualizou = $stmt->affected_rows > 0;

        $stmt->close();
        return $atualizou;
    }
}