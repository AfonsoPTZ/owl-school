<?php

namespace App\Repositories;

use App\Models\Agenda;

class AgendaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO horarios_aula (dia_semana, inicio, fim, disciplina) VALUES (?, ?, ?, ?)"
        );

        if (!$stmt->execute([
            $agenda->diaSemana,
            $agenda->inicio,
            $agenda->fim,
            $agenda->disciplina
        ])) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            $agenda->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM horarios_aula WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
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

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(Agenda $agenda): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE horarios_aula SET dia_semana = ?, inicio = ?, fim = ?, disciplina = ? WHERE id = ?"
        );

        $stmt->execute([
            $agenda->diaSemana,
            $agenda->inicio,
            $agenda->fim,
            $agenda->disciplina,
            $agenda->id
        ]);

        return $stmt->rowCount() > 0;
    }
}
