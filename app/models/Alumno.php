<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Alumno extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, nombre, telefono, direccion FROM alumnos ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $codigo = $this->nextCode();

        $stmt = $this->db->prepare(
            'INSERT INTO alumnos (codigo, nombre, telefono, direccion) VALUES (:codigo, :nombre, :telefono, :direccion)'
        );

        return $stmt->execute([
            'codigo' => $codigo,
            'nombre' => $data['nombre'],
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE alumnos SET nombre = :nombre, telefono = :telefono, direccion = :direccion WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM alumnos WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        if ($term == '') {
            return $this->all();
        }

        $stmt = $this->db->prepare(
            'SELECT id, nombre, telefono FROM alumnos
             WHERE nombre LIKE :term OR telefono LIKE :term
             ORDER BY nombre ASC LIMIT 50'
        );
        $stmt->execute(['term' => '%' . $term . '%']);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nombre, telefono, direccion FROM alumnos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function getMorosos(int $anio): array
    {
        $alumnos = $this->all();
        if ($alumnos === []) {
            return [];
        }

        $cuotas = (new Cuota())->getByYear($anio);
        if ($cuotas === []) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT alumno_id, mes, SUM(valor) AS total
             FROM abonos
             WHERE (anio = :anio AND mes >= 9) OR (anio = :anio_plus AND mes <= 6)
             GROUP BY alumno_id, mes'
        );
        $stmt->execute(['anio' => $anio, 'anio_plus' => $anio + 1]);

        $pagos = [];
        foreach ($stmt->fetchAll() as $row) {
            $pagos[(int) $row['alumno_id']][(int) $row['mes']] = (float) $row['total'];
        }

        $meses = [
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio'
        ];

        $morosos = [];
        foreach ($alumnos as $alumno) {
            foreach ($cuotas as $mes => $valorCuota) {
                if ($valorCuota <= 0) continue;

                $pagado = (float) ($pagos[(int) $alumno['id']][$mes] ?? 0);
                $pendiente = $valorCuota - $pagado;

                if ($pendiente > 0) {
                    $morosos[] = [
                        'id' => (int) $alumno['id'],
                        'nombre' => $alumno['nombre'],
                        'mes' => $mes,
                        'mes_nombre' => $meses[$mes],
                        'pendiente' => $pendiente,
                    ];
                }
            }
        }

        return $morosos;
    }

    private function nextCode(): string
    {
        $stmt = $this->db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)), 0) AS max_codigo FROM alumnos WHERE codigo LIKE 'ALU-%'");
        $row = $stmt->fetch();
        $next = ((int) ($row['max_codigo'] ?? 0)) + 1;

        return sprintf('ALU-%03d', $next);
    }
}
