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

    public function create(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO comunicado (titulo, corpo) VALUES (?, ?)"
        );

        if (!$stmt) {
            return false;
        }

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

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM comunicado WHERE id = ?"
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
            "SELECT id, titulo, corpo FROM comunicado ORDER BY id DESC"
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
        $comunicados = [];

        while ($linha = $resultado->fetch_assoc()) {
            $comunicados[] = $linha;
        }

        $stmt->close();
        return $comunicados;
    }

    public function update(Comunicado $comunicado): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE comunicado SET titulo = ?, corpo = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssi",
            $comunicado->titulo,
            $comunicado->corpo,
            $comunicado->id
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
