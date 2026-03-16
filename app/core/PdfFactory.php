<?php

declare(strict_types=1);

namespace App\Core;

use Dompdf\Dompdf;
use RuntimeException;

final class PdfFactory
{
    public static function make(): Dompdf
    {
        self::loadDompdf();

        return new Dompdf();
    }

    private static function loadDompdf(): void
    {
        if (class_exists(Dompdf::class)) {
            return;
        }

        $autoloaders = [
            __DIR__ . '/../../vendor/autoload.php',
            __DIR__ . '/../../dompdf/autoload.inc.php',
            __DIR__ . '/../../../dompdf/autoload.inc.php',
        ];

        foreach ($autoloaders as $autoloader) {
            if (file_exists($autoloader)) {
                require_once $autoloader;
                if (class_exists(Dompdf::class)) {
                    return;
                }
            }
        }

        throw new RuntimeException('No se pudo cargar Dompdf. Verifica la carpeta /dompdf o ejecuta composer install.');
    }
}

