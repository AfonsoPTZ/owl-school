<?php

namespace App\Repositories;

use App\Models\ProvaNota;

class ProvaNotaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
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

        $stmt->bind_param(
            "iid",
            $provaNota->provaId,
            $provaNota->alunoId,
            $provaNota->nota
        );

        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }

    /* ============================== */
    /* DELETE */
    /* ============================== */
    public function delete(int $provaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova_nota WHERE prova_id = ? AND aluno_id = ?"
        );

        $stmt->bind_param("ii", $provaId, $alunoId);
        $stmt->execute();

        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
    }

    /* ============================== */
    /* READ / FIND BY PROVA */
    /* ============================== */
    public function findByProva(int $provaId): array
    {
        // Buscar o título da prova
        $stmtProva = $this->conn->prepare("SELECT titulo FROM prova WHERE id = ?");
        $stmtProva->bind_param("i", $provaId);
        $stmtProva->execute();
        $resultProva = $stmtProva->get_result();
        $titulo_prova = '';
        
        if ($row = $resultProva->fetch_assoc()) {
            $titulo_prova = $row['titulo'];
        }
        $stmtProva->close();

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

        $stmt->bind_param("i", $provaId);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $notas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $linha['titulo_prova'] = $titulo_prova;
            $notas[] = $linha;
        }

        $stmt->close();
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

        $nota = $provaNota->nota;
        $provaId = $provaNota->provaId;
        $alunoId = $provaNota->alunoId;

        $stmt->bind_param("dii", $nota, $provaId, $alunoId);
        $executou = $stmt->execute();

        $stmt->close();
        return $executou;
    }
}
