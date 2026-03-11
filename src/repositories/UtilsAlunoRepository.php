<?php

namespace App\Repositories;

class UtilsAlunoRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* GET ADVERTENCIAS BY ALUNO */
    /* ============================== */
    public function getAdvertencias(int $alunoId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                aluno_advertencia.advertencia_id AS id,
                advertencia.titulo AS titulo,
                advertencia.descricao AS descricao,
                usuario.nome AS aluno_nome
            FROM aluno
            JOIN aluno_advertencia
                ON aluno_advertencia.aluno_id = aluno.usuario_id
            JOIN advertencia
                ON advertencia.id = aluno_advertencia.advertencia_id
            JOIN usuario
                ON usuario.id = aluno.usuario_id
            WHERE aluno.usuario_id = ?
            ORDER BY advertencia.id DESC"
        );

        $stmt->bind_param("i", $alunoId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $advertencias = [];

        while ($linha = $resultado->fetch_assoc()) {
            $advertencias[] = $linha;
        }

        $stmt->close();
        return $advertencias;
    }

    /* ============================== */
    /* GET FREQUENCIAS BY ALUNO */
    /* ============================== */
    public function getFrequencias(int $alunoId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT 
                chamada.data AS data,
                chamada_item.status AS status
            FROM chamada_item
            JOIN chamada 
                ON chamada.id = chamada_item.chamada_id
            WHERE chamada_item.aluno_id = ?
            ORDER BY chamada.data DESC"
        );

        $stmt->bind_param("i", $alunoId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $frequencias = [];

        while ($linha = $resultado->fetch_assoc()) {
            $frequencias[] = $linha;
        }

        $stmt->close();
        return $frequencias;
    }

    /* ============================== */
    /* GET NOTAS BY ALUNO */
    /* ============================== */
    public function getNotas(int $alunoId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                prova.id AS prova_id,
                prova.titulo AS titulo,
                prova.data AS data,
                prova_nota.nota AS nota
            FROM prova_nota
            JOIN prova 
                ON prova.id = prova_nota.prova_id
            WHERE prova_nota.aluno_id = ?
            ORDER BY prova.data DESC"
        );

        $stmt->bind_param("i", $alunoId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $notas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $notas[] = $linha;
        }

        $stmt->close();
        return $notas;
    }

    /* ============================== */
    /* GET NOME RESPONSAVEL */
    /* ============================== */
    public function getNomeResponsavel(int $alunoId): ?string
    {
        $stmt = $this->conn->prepare(
            "SELECT usuario.nome AS nome_responsavel
            FROM aluno_responsavel
            JOIN responsavel 
                ON responsavel.usuario_id = aluno_responsavel.responsavel_id
            JOIN usuario 
                ON usuario.id = responsavel.usuario_id
            WHERE aluno_responsavel.aluno_id = ?
            LIMIT 1"
        );

        $stmt->bind_param("i", $alunoId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $nomeResponsavel = null;

        if ($linha = $resultado->fetch_assoc()) {
            $nomeResponsavel = $linha['nome_responsavel'];
        }

        $stmt->close();
        return $nomeResponsavel;
    }
}
