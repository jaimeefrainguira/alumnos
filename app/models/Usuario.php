<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Usuario extends Model
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT id, usuario, password, rol FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute(['usuario' => $username]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
        return $stmt->execute(['password' => $hash, 'id' => $userId]);
    }

    public function updateUsername(int $userId, string $newUsername): bool
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET usuario = :usuario WHERE id = :id');
        return $stmt->execute(['usuario' => $newUsername, 'id' => $userId]);
    }
}
