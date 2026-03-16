<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Alumno extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, codigo, nombre, telefono, direccion, fecha_registro FROM alumnos ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO alumnos (codigo, nombre, telefono, direccion) VALUES (:codigo, :nombre, :telefono, :direccion)'
        );

        return $stmt->execute([
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'],
        ]);
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, codigo, nombre, telefono FROM alumnos
             WHERE nombre LIKE :term OR telefono LIKE :term OR codigo LIKE :term
             ORDER BY nombre ASC LIMIT 50'
        );
        $stmt->execute(['term' => '%' . $term . '%']);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, codigo, nombre, telefono, direccion FROM alumnos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function getMorosos(int $anio): array
    {
        $sql = 'SELECT a.id, a.nombre, m.mes, c.valor - COALESCE(SUM(ab.valor),0) AS pendiente
                FROM alumnos a
                CROSS JOIN (SELECT 1 mes UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                            UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) m
                JOIN cuotas c ON c.anio = :anio
                LEFT JOIN abonos ab ON ab.alumno_id = a.id AND ab.anio = :anio AND ab.mes = m.mes
                GROUP BY a.id, a.nombre, m.mes, c.valor
                HAVING pendiente > 0
                ORDER BY a.nombre, m.mes';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['anio' => $anio]);

        return $stmt->fetchAll();
    }
}
