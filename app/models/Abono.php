<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Abono extends Model
{
    private const MONTHS = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
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
             WHERE anio = :anio
             GROUP BY alumno_id, mes'
        );
        $stmt->execute(['anio' => $anio]);

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
             WHERE anio = :anio
             GROUP BY mes
             ORDER BY mes ASC'
        );
        $stmt->execute(['anio' => $anio]);

        $rows = $stmt->fetchAll();
        $result = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $result[(int) $row['mes']] = (float) $row['total'];
        }

        return $result;
    }

    public function totalPayments(int $anio): float
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(valor),0) AS total FROM abonos WHERE anio = :anio');
        $stmt->execute(['anio' => $anio]);

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
             WHERE alumno_id = :alumno_id AND anio = :anio
             GROUP BY mes'
        );
        $stmt->execute(['alumno_id' => $alumnoId, 'anio' => $anio]);

        $rows = array_fill(1, 12, 0.0);
        foreach ($stmt->fetchAll() as $row) {
            $rows[(int) $row['mes']] = (float) $row['total'];
        }

        return $rows;
    }

    public function detailsByAlumno(int $alumnoId, int $anio): array
    {
        $stmt = $this->db->prepare(
            'SELECT mes, valor, fecha_abono
             FROM abonos
             WHERE alumno_id = :alumno_id AND anio = :anio
             ORDER BY mes ASC, fecha_abono ASC, id ASC'
        );
        $stmt->execute([
            'alumno_id' => $alumnoId,
            'anio' => $anio,
        ]);

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $monthNumber = (int) $row['mes'];
            $grouped[$monthNumber]['mes_numero'] = $monthNumber;
            $grouped[$monthNumber]['mes_nombre'] = self::MONTHS[$monthNumber] ?? (string) $monthNumber;
            $grouped[$monthNumber]['items'][] = [
                'valor' => (float) $row['valor'],
                'fecha_abono' => $row['fecha_abono'],
            ];
        }

        return array_values($grouped);
    }
}
