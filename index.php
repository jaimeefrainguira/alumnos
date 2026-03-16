<?php

declare(strict_types=1);

use App\Controllers\AbonoController;
use App\Controllers\AlumnoController;
use App\Controllers\AuthController;
use App\Controllers\CuotaController;
use App\Controllers\PublicController;
use App\Controllers\ReporteController;
use App\Core\Router;

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Lax',
]);

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $relative = str_replace('\\', '/', $relative);
    $file = __DIR__ . '/app/' . strtolower(dirname($relative)) . '/' . basename($relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$router = new Router();

$router->get('/', [PublicController::class, 'home']);
$router->get('/buscar', [PublicController::class, 'search']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->post('/ajustes/credenciales', [AuthController::class, 'updateCredentials']);

$router->get('/dashboard', [ReporteController::class, 'dashboard']);
$router->get('/alumnos', [AlumnoController::class, 'index']);
$router->get('/alumnos/ver', [AlumnoController::class, 'show']);
$router->post('/alumnos', [AlumnoController::class, 'store']);
$router->post('/abonos', [AbonoController::class, 'store']);
$router->get('/abonos/historial', [AbonoController::class, 'history']);
$router->get('/cuotas', [CuotaController::class, 'index']);
$router->post('/cuotas', [CuotaController::class, 'store']);
$router->get('/reportes/alumno', [ReporteController::class, 'alumnoPdf']);
$router->get('/reportes/general', [ReporteController::class, 'generalPdf']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
