<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = require __DIR__ . '/../../config/database.php';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $databaseNames = self::candidateDatabases((string) $config['database']);

        foreach ($databaseNames as $databaseName) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $databaseName,
                $config['charset']
            );

            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password'], $options);
                return self::$connection;
            } catch (PDOException $exception) {
                continue;
            }
        }

        http_response_code(500);
        exit('Error de conexión a base de datos. Revisa DB_NAME en config/database.php o variables de entorno.');

    }

    private static function candidateDatabases(string $database): array
    {
        $candidates = [$database];

        if (str_contains($database, 'alumos')) {
            $candidates[] = str_replace('alumos', 'alumnos', $database);
        }

        return array_values(array_unique(array_filter($candidates)));

    }
}
