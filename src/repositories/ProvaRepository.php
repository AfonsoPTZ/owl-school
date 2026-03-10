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

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(Prova $prova): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova (titulo, data) VALUES (?, ?)"
        );

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

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova WHERE id = ?"
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
            "SELECT id, titulo, data FROM prova ORDER BY data DESC, id DESC"
        );

        $stmt->execute();

        $resultado = $stmt->get_result();
        $provas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $provas[] = $linha;
        }

        $stmt->close();
        return $provas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Prova $prova): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE prova SET titulo = ?, data = ? WHERE id = ?"
        );

        $titulo = $prova->titulo;
        $data = $prova->data;
        $id = $prova->id;

        $stmt->bind_param("ssi", $titulo, $data, $id);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
