<?php

namespace App\Repositories;

use App\Models\Agenda;

class AgendaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO horarios_aula (dia_semana, inicio, fim, disciplina) VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssss",
            $agenda->diaSemana,
            $agenda->inicio,
            $agenda->fim,
            $agenda->disciplina
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $agenda->id = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM horarios_aula WHERE id = ?"
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
            "SELECT 
                id,
                dia_semana,
                TIME_FORMAT(inicio, '%H:%i') AS inicio,
                TIME_FORMAT(fim, '%H:%i') AS fim,
                disciplina
            FROM horarios_aula
            ORDER BY FIELD(dia_semana,'segunda','terca','quarta','quinta','sexta'), inicio"
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
        $agendas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $agendas[] = $linha;
        }

        $stmt->close();
        return $agendas;
    }

    public function update(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE horarios_aula SET dia_semana = ?, inicio = ?, fim = ?, disciplina = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssssi",
            $agenda->diaSemana,
            $agenda->inicio,
            $agenda->fim,
            $agenda->disciplina,
            $agenda->id
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
