<?php

namespace App\Repositories;

use App\Models\ProvaNota;

class ProvaNotaRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* CREATE */
    /* ============================== */
    public function create(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova_nota (prova_id, aluno_id, nota) VALUES (?, ?, ?)"
        );

        if (!$stmt) return false;

        return $stmt->execute([
            $provaNota->provaId,
            $provaNota->alunoId,
            $provaNota->nota
        ]);
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $provaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova_nota WHERE prova_id = ? AND aluno_id = ?"
        );

        if (!$stmt) return false;

        $stmt->execute([$provaId, $alunoId]);

        return $stmt->rowCount() > 0;
    }

    /* ============================== */
    /* READ / FIND BY PROVA */
    /* ============================== */
    public function findByProva(int $provaId): array
    {
        // Buscar o título da prova
        $stmtProva = $this->conn->prepare("SELECT titulo FROM prova WHERE id = ?");
        if (!$stmtProva) return [];

        $stmtProva->execute([$provaId]);
        $titulo_prova = '';
        
        if ($row = $stmtProva->fetch(\PDO::FETCH_ASSOC)) {
            $titulo_prova = $row['titulo'];
        }

        // Buscar todos os alunos com suas notas (LEFT JOIN para mostrar alunos sem nota também)
        $stmt = $this->conn->prepare(
            "SELECT
                aluno.usuario_id AS aluno_id,
                usuario.nome AS aluno_nome,
                prova_nota.nota
            FROM aluno
            JOIN usuario
                ON usuario.id = aluno.usuario_id
            LEFT JOIN prova_nota
                ON prova_nota.aluno_id = aluno.usuario_id
                AND prova_nota.prova_id = ?
            ORDER BY usuario.nome"
        );

        if (!$stmt) return [];

        $stmt->execute([$provaId]);

        $notas = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $linha['titulo_prova'] = $titulo_prova;
            $notas[] = $linha;
        }

        return $notas;
    }

    /* ============================== */
    /* UPDATE */
    /* ============================== */
    public function update(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE prova_nota SET nota = ? WHERE prova_id = ? AND aluno_id = ?"
        );

        if (!$stmt) return false;

        return $stmt->execute([
            $provaNota->nota,
            $provaNota->provaId,
            $provaNota->alunoId
        ]);
    }
}
