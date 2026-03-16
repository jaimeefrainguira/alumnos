<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Cuota extends Model
{
    private ?string $yearColumn = null;
    private ?string $amountColumn = null;

    public function getByYear(int $year): ?array
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();

        $stmt = $this->db->prepare(sprintf(
            'SELECT id, %s AS anio, %s AS valor FROM cuotas WHERE %s = :anio LIMIT 1',
            $yearColumn,
            $amountColumn,
            $yearColumn
        ));
        $stmt->execute(['anio' => $year]);

        return $stmt->fetch() ?: null;
    }

    public function upsert(int $year, float $value): bool
    {
        $yearColumn = $this->resolveYearColumn();
        $amountColumn = $this->resolveAmountColumn();

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

    private function firstExistingColumn(array $candidates): ?string
    {
        $stmt = $this->db->query('SHOW COLUMNS FROM cuotas');
        $columns = array_map(static fn (array $row): string => (string) ($row['Field'] ?? ''), $stmt->fetchAll());

        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
