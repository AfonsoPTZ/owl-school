<?php

namespace App\Repositories;

use App\Models\Prova;

class ProvaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(Prova $prova): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova (titulo, data) VALUES (?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ss",
            $prova->titulo,
            $prova->data
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $prova->id = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        $stmt->close();
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
            "SELECT id, titulo, data FROM prova ORDER BY data DESC, id DESC"
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
        $provas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $provas[] = $linha;
        }

        $stmt->close();
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

        $stmt->bind_param(
            "ssi",
            $prova->titulo,
            $prova->data,
            $prova->id
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
