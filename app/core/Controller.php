<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit('Vista no encontrada.');
        }

        require __DIR__ . '/../views/layouts/header.php';
        require $viewPath;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function validateCsrfToken(?string $token): bool
    {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $token);
    }

    protected function getAcademicYear(): int
    {
        $year = (int) date('Y');
        $month = (int) date('n');
        // Ciclo Sierra: Sep-Jun. Si estamos en meses 1-8, el ciclo empezó el año pasado. 
        // (En Septiembre empieza el año académico N)
        if ($month <= 8) {
            $year--;
        }
        return $year;
    }
}
