<?php

namespace App\Repositories;

use App\Models\Comunicado;

class ComunicadoRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO comunicado (titulo, corpo) VALUES (?, ?)"
        );

        $stmt->bind_param(
            "ss",
            $comunicado->titulo,
            $comunicado->corpo
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $comunicado->id = $this->conn->insert_id;
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
            "DELETE FROM comunicado WHERE id = ?"
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
            "SELECT id, titulo, corpo FROM comunicado ORDER BY id DESC"
        );

        $stmt->execute();

        $resultado = $stmt->get_result();
        $comunicados = [];

        while ($linha = $resultado->fetch_assoc()) {
            $comunicados[] = $linha;
        }

        $stmt->close();
        return $comunicados;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE comunicado SET titulo = ?, corpo = ? WHERE id = ?"
        );

        $titulo = $comunicado->titulo;
        $corpo = $comunicado->corpo;
        $id = $comunicado->id;

        $stmt->bind_param("ssi", $titulo, $corpo, $id);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
