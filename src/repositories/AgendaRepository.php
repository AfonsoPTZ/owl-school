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

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO horarios_aula (dia_semana, inicio, fim, disciplina) VALUES (?, ?, ?, ?)"
        );

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

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM horarios_aula WHERE id = ?"
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
            "SELECT 
                id,
                dia_semana,
                TIME_FORMAT(inicio, '%H:%i') AS inicio,
                TIME_FORMAT(fim, '%H:%i') AS fim,
                disciplina
            FROM horarios_aula
            ORDER BY FIELD(dia_semana,'segunda','terca','quarta','quinta','sexta'), inicio"
        );

        $stmt->execute();

        $resultado = $stmt->get_result();
        $agendas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $agendas[] = $linha;
        }

        $stmt->close();
        return $agendas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE horarios_aula SET dia_semana = ?, inicio = ?, fim = ?, disciplina = ? WHERE id = ?"
        );

        $diaSemana = $agenda->diaSemana;
        $inicio = $agenda->inicio;
        $fim = $agenda->fim;
        $disciplina = $agenda->disciplina;
        $id = $agenda->id;

        $stmt->bind_param("ssssi", $diaSemana, $inicio, $fim, $disciplina, $id);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
