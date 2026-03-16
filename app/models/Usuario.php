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
}
