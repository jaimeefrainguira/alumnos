<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Abono extends Model
{
    private const MONTHS = [
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio'
    ];

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO abonos (alumno_id, mes, anio, valor, fecha_abono)
             VALUES (:alumno_id, :mes, :anio, :valor, :fecha_abono)'
        );

        return $stmt->execute($data);
    }

    public function monthHistory(int $alumnoId, int $mes, int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, valor, fecha_abono
             FROM abonos
             WHERE alumno_id = :alumno_id AND mes = :mes AND anio = :anio
             ORDER BY fecha_abono ASC, id ASC'
        );
        $stmt->execute([
            'alumno_id' => $alumnoId,
            'mes' => $mes,
            'anio' => $anio,
        ]);

        return $stmt->fetchAll();
    }

    public function totalsMatrix(int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT alumno_id, mes, SUM(valor) AS total
             FROM abonos
             WHERE (anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6)
             GROUP BY alumno_id, mes'
        );
        $stmt->execute(['anio' => $anio, 'anio_plus' => $anio + 1]);

        $totals = [];
        foreach ($stmt->fetchAll() as $row) {
            $totals[(int) $row['alumno_id']][(int) $row['mes']] = (float) $row['total'];
        }

        return $totals;
    }

    public function statsByYear(int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT mes, SUM(valor) AS total
             FROM abonos
             WHERE (anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6)
             GROUP BY mes
             ORDER BY CASE WHEN mes >= 9 THEN mes - 12 ELSE mes END ASC'
        );
        $stmt->execute(['anio' => $anio, 'anio_plus' => $anio + 1]);

        $rows = $stmt->fetchAll();
        $result = [9 => 0.0, 10 => 0.0, 11 => 0.0, 12 => 0.0, 1 => 0.0, 2 => 0.0, 3 => 0.0, 4 => 0.0, 5 => 0.0, 6 => 0.0];
        foreach ($rows as $row) {
            $result[(int) $row['mes']] = (float) $row['total'];
        }

        return $result;
    }

    public function totalPayments(int $anio): float
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(valor),0) AS total FROM abonos WHERE (anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6)');
        $stmt->execute(['anio' => $anio, 'anio_plus' => $anio + 1]);

        return (float) $stmt->fetch()['total'];
    }

    public function paymentsToday(): float
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(valor),0) AS total FROM abonos WHERE fecha_abono = :fecha');
        $stmt->execute(['fecha' => date('Y-m-d')]);

        return (float) $stmt->fetch()['total'];
    }

    public function totalsByAlumno(int $alumnoId, int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT mes, SUM(valor) total
             FROM abonos
             WHERE alumno_id = :alumno_id AND ((anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6))
             GROUP BY mes'
        );
        $stmt->execute(['alumno_id' => $alumnoId, 'anio' => $anio, 'anio_plus' => $anio + 1]);

        $rows = [9 => 0.0, 10 => 0.0, 11 => 0.0, 12 => 0.0, 1 => 0.0, 2 => 0.0, 3 => 0.0, 4 => 0.0, 5 => 0.0, 6 => 0.0];
        foreach ($stmt->fetchAll() as $row) {
            $rows[(int) $row['mes']] = (float) $row['total'];
        }

        return $rows;
    }

    public function detailsByAlumno(int $alumnoId, int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, mes, valor, fecha_abono
             FROM abonos
             WHERE alumno_id = :alumno_id AND ((anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6))
             ORDER BY CASE WHEN mes >= 9 THEN mes - 12 ELSE mes END ASC, fecha_abono ASC, id ASC'
        );
        $stmt->execute([
            'alumno_id' => $alumnoId,
            'anio' => $anio,
            'anio_plus' => $anio + 1,
        ]);

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $monthNumber = (int) $row['mes'];
            $grouped[$monthNumber]['mes_numero'] = $monthNumber;
            $grouped[$monthNumber]['mes_nombre'] = self::MONTHS[$monthNumber] ?? (string) $monthNumber;
            $grouped[$monthNumber]['items'][] = [
                'id' => (int) $row['id'],
                'valor' => (float) $row['valor'],
                'fecha_abono' => $row['fecha_abono'],
            ];
        }

        return array_values($grouped);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM abonos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE abonos 
             SET valor = :valor, fecha_abono = :fecha_abono 
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'valor' => $data['valor'],
            'fecha_abono' => $data['fecha_abono'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM abonos WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
    public function deleteByAlumno(int $alumnoId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM abonos WHERE alumno_id = :alumno_id');
        return $stmt->execute(['alumno_id' => $alumnoId]);
    }
}
