<?php

namespace App\Repositories;

class AuthRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /* ============================== */
    /* AUTHENTICATE USER */
    /* ============================== */
    public function authenticate(string $email, string $senha): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, nome, tipo_usuario FROM usuario WHERE email = ? AND senha = ?"
        );

        $stmt->execute([$email, $senha]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    /* ============================== */
    /* FIND BY EMAIL */
    /* ============================== */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, nome, email, senha, tipo_usuario FROM usuario WHERE email = ?"
        );

        if (!$stmt) return null;

        $stmt->execute([$email]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }
}
