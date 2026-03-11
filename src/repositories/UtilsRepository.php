<?php

namespace App\Repositories;

class UtilsRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* GET USER NAME (from session) */
    /* ============================== */
    public function getUserName(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    /* ============================== */
    /* GET ALL ALUNOS */
    /* ============================== */
    public function getAllAlunos(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                aluno.usuario_id AS aluno_id,
                usuario.nome AS aluno_nome
            FROM aluno
            JOIN usuario 
                ON usuario.id = aluno.usuario_id
            ORDER BY usuario.nome ASC"
        );

        $stmt->execute();
        $resultado = $stmt->get_result();
        $alunos = [];

        while ($linha = $resultado->fetch_assoc()) {
            $alunos[] = $linha;
        }

        $stmt->close();
        return $alunos;
    }
}
