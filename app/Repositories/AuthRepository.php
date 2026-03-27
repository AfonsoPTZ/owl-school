<?php

namespace App\Repositories;

class AuthRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, nome, email, senha, tipo_usuario FROM usuario WHERE email = ?"
        );

        $stmt->execute([$email]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }
}
