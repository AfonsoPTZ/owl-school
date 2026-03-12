<?php

namespace App\Repositories;

class AuthRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
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

        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();
        return $usuario;
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

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();
        return $usuario;
    }
}
