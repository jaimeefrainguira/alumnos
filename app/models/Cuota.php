<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Cuota extends Model
{
    private ?string $yearColumn = null;
    private ?string $amountColumn = null;

    public function getByYear(int $year): array
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();
        $cols = $this->getTableColumns();
        $hasMonth = in_array('mes', $cols, true);

        $sql = $hasMonth 
            ? sprintf('SELECT mes, %s AS valor FROM cuotas WHERE (%s = :anio AND mes >= 9) OR (%s = :anio_plus AND mes <= 6)', $amountColumn, $yearColumn, $yearColumn)
            : sprintf('SELECT %s AS valor FROM cuotas WHERE %s = :anio LIMIT 1', $amountColumn, $yearColumn);

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['anio' => $year, 'anio_plus' => $year + 1]);

        $rows = $stmt->fetchAll();
        $cuotas = [];

        if ($hasMonth) {
            foreach ($rows as $row) {
                $cuotas[(int) $row['mes']] = (float) $row['valor'];
            }
        } elseif (count($rows) > 0) {
            $valor = (float) $rows[0]['valor'];
            for ($m = 1; $m <= 12; $m++) {
                $cuotas[$m] = $valor;
            }
        }

        return $cuotas;
    }

    public function getByYearMonth(int $year, int $month): ?array
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();
        $cols = $this->getTableColumns();
        $hasMonth = in_array('mes', $cols, true);

        $sql = $hasMonth
            ? sprintf('SELECT id, %s AS anio, mes, %s AS valor FROM cuotas WHERE ((%s = :anio AND mes >= 9) OR (%s = :anio_plus AND mes <= 6)) AND mes = :mes LIMIT 1', $yearColumn, $amountColumn, $yearColumn, $yearColumn)
            : sprintf('SELECT id, %s AS anio, 1 AS mes, %s AS valor FROM cuotas WHERE %s = :anio LIMIT 1', $yearColumn, $amountColumn, $yearColumn);

        $stmt = $this->db->prepare($sql);
        $params = ['anio' => $year, 'anio_plus' => $year + 1];
        if ($hasMonth) {
            $params['mes'] = $month;
        }
        $stmt->execute($params);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(int $year, float $value): bool
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();
        $cols = $this->getTableColumns();

        if (in_array('mes', $cols, true)) {
            // Si el sistema ya soporta meses, el 'upsert' anual 
            // tradicional lo guardamos en el mes 1 como backup o fallback
            return $this->upsertMonth($year, 1, $value);
        }

        $stmt = $this->db->prepare(sprintf(
            'INSERT INTO cuotas (%s, %s) VALUES (:anio, :valor)
             ON DUPLICATE KEY UPDATE %s = VALUES(%s)',
            $yearColumn,
            $amountColumn,
            $amountColumn,
            $amountColumn
        ));

        return $stmt->execute(['anio' => $year, 'valor' => $value]);
    }

    public function upsertMonth(int $year, int $month, float $value): bool
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();
        $cols = $this->getTableColumns();

        if (!in_array('mes', $cols, true)) {
            return $this->upsert($year, $value);
        }

        $stmt = $this->db->prepare(sprintf(
            'INSERT INTO cuotas (%s, mes, %s) VALUES (:anio, :mes, :valor)
             ON DUPLICATE KEY UPDATE %s = VALUES(%s)',
            $yearColumn,
            $amountColumn,
            $amountColumn,
            $amountColumn
        ));

        return $stmt->execute(['anio' => $year, 'mes' => $month, 'valor' => $value]);
    }

    private function resolveYearColumn(): string
    {
        if ($this->yearColumn !== null) {
            return $this->yearColumn;
        }

        $this->yearColumn = $this->firstExistingColumn(['anio', 'year', 'anio_lectivo']) ?? 'anio';

        return $this->yearColumn;
    }

    private function resolveAmountColumn(): string
    {
        if ($this->amountColumn !== null) {
            return $this->amountColumn;
        }

        $this->amountColumn = $this->firstExistingColumn(['valor', 'monto', 'cuota']) ?? 'valor';

        return $this->amountColumn;
    }

    private function getTableColumns(): array
    {
        static $columns = null;
        if ($columns !== null) {
            return $columns;
        }

        try {
            $stmt = $this->db->query('SHOW COLUMNS FROM cuotas');
            $rows = $stmt->fetchAll();
            $columns = array_map(static function (array $row): string {
                return (string) ($row['Field'] ?? $row['field'] ?? '');
            }, $rows);
        } catch (\Throwable $e) {
            $columns = [];
        }

        return $columns;
    }

    private function firstExistingColumn(array $candidates): ?string
    {
        $columns = $this->getTableColumns();

        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
