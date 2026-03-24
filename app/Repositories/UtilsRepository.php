<?php

namespace App\Repositories;

class UtilsRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
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
        $alunos = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $alunos[] = $linha;
        }

        return $alunos;
    }
}
