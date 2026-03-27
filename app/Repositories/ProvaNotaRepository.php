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

    public function create(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prova_nota (prova_id, aluno_id, nota) VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            $provaNota->provaId,
            $provaNota->alunoId,
            $provaNota->nota
        ]);
    }

    public function delete(int $provaId, int $alunoId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM prova_nota WHERE prova_id = ? AND aluno_id = ?"
        );

        $stmt->execute([$provaId, $alunoId]);

        return $stmt->rowCount() > 0;
    }

    public function findByProva(int $provaId): array
    {
        $stmtProva = $this->conn->prepare("SELECT titulo FROM prova WHERE id = ?");
        $stmtProva->execute([$provaId]);
        $titulo_prova = '';
        
        if ($row = $stmtProva->fetch(\PDO::FETCH_ASSOC)) {
            $titulo_prova = $row['titulo'];
        }

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

        $stmt->execute([$provaId]);
        $notas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($notas as &$nota) {
            $nota['titulo_prova'] = $titulo_prova;
        }

        return $notas;
    }

    public function update(ProvaNota $provaNota): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE prova_nota SET nota = ? WHERE prova_id = ? AND aluno_id = ?"
        );

        $stmt->execute([
            $provaNota->nota,
            $provaNota->provaId,
            $provaNota->alunoId
        ]);

        return $stmt->rowCount() > 0;
    }
}
