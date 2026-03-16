<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Cuota extends Model
{
    public function getByYear(int $year): ?array
    {
        $stmt = $this->db->prepare('SELECT id, anio, valor FROM cuotas WHERE anio = :anio LIMIT 1');
        $stmt->execute(['anio' => $year]);
        return $stmt->fetch() ?: null;
    }

    public function upsert(int $year, float $value): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cuotas (anio, valor) VALUES (:anio, :valor)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
        );

        return $stmt->execute(['anio' => $year, 'valor' => $value]);
    }
}
