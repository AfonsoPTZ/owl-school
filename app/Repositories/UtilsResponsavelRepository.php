<?php

namespace App\Repositories;

class UtilsResponsavelRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* GET ALUNO VINCULADO */
    /* ============================== */
    public function getAlunoVinculado(int $responsavelId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                usuario.id,
                usuario.nome
            FROM aluno_responsavel
            JOIN usuario
                ON usuario.id = aluno_responsavel.aluno_id
            WHERE aluno_responsavel.responsavel_id = ?
            LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->execute([$responsavelId]);
        $aluno = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $aluno ?: null;
    }

    public function getAdvertenciasFilho(int $responsavelId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                advertencia.id AS id,
                advertencia.titulo AS titulo,
                advertencia.descricao AS descricao,
                usuario.nome AS aluno_nome
            FROM aluno_responsavel
            JOIN aluno_advertencia
                ON aluno_advertencia.aluno_id = aluno_responsavel.aluno_id
            JOIN advertencia
                ON advertencia.id = aluno_advertencia.advertencia_id
            JOIN usuario
                ON usuario.id = aluno_responsavel.aluno_id
            WHERE aluno_responsavel.responsavel_id = ?
            ORDER BY advertencia.id DESC"
        );

        $stmt->execute([$responsavelId]);
        $advertencias = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $advertencias[] = $linha;
        }

        return $advertencias;
    }

    /* ============================== */
    /* GET FREQUENCIAS FILHO */
    /* ============================== */
    public function getFrequenciasFilho(int $responsavelId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                chamada.data AS data,
                chamada_item.status AS status,
                usuario.nome AS aluno_nome
            FROM aluno_responsavel
            JOIN chamada_item 
                ON chamada_item.aluno_id = aluno_responsavel.aluno_id
            JOIN chamada 
                ON chamada.id = chamada_item.chamada_id
            JOIN usuario 
                ON usuario.id = aluno_responsavel.aluno_id
            WHERE aluno_responsavel.responsavel_id = ?
            ORDER BY chamada.data DESC"
        );

        $stmt->execute([$responsavelId]);
        $frequencias = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $frequencias[] = $linha;
        }

        return $frequencias;
    }

    /* ============================== */
    /* GET NOTAS FILHO */
    /* ============================== */
    public function getNotasFilho(int $responsavelId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                prova.id AS prova_id,
                prova.titulo AS titulo,
                prova.data AS data,
                prova_nota.nota AS nota,
                usuario.nome AS aluno_nome
            FROM aluno_responsavel
            JOIN prova_nota
                ON prova_nota.aluno_id = aluno_responsavel.aluno_id
            JOIN prova 
                ON prova.id = prova_nota.prova_id
            JOIN usuario
                ON usuario.id = aluno_responsavel.aluno_id
            WHERE aluno_responsavel.responsavel_id = ?
            ORDER BY prova.data DESC"
        );

        $stmt->execute([$responsavelId]);
        $notas = [];

        while ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $notas[] = $linha;
        }

        return $notas;
    }

    /* ============================== */
    /* GET NOME FILHO */
    /* ============================== */
    public function getNomeFilho(int $responsavelId): ?string
    {
        $stmt = $this->conn->prepare(
            "SELECT usuario.nome AS nome_filho
            FROM aluno_responsavel
            JOIN aluno 
                ON aluno.usuario_id = aluno_responsavel.aluno_id
            JOIN usuario 
                ON usuario.id = aluno.usuario_id
            WHERE aluno_responsavel.responsavel_id = ?
            LIMIT 1"
        );

        $stmt->execute([$responsavelId]);
        $nomeFilho = null;

        if ($linha = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $nomeFilho = $linha['nome_filho'];
        }

        return $nomeFilho;
    }
}
