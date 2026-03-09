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
}