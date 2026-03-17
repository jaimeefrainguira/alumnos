<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/app/' . strtolower(dirname($relative)) . '/' . basename($relative) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;

try {
    $db = Database::getConnection();
    echo "<h3>Corrigiendo estructura de tabla 'cuotas'...</h3>";

    // 1. Verificar si existe la columna mes
    $stmt = $db->query("SHOW COLUMNS FROM cuotas");
    $cols = array_map(fn($c) => $c['Field'], $stmt->fetchAll());
    
    if (!in_array('mes', $cols)) {
        echo "Añadiendo columna 'mes'...<br>";
        $db->exec("ALTER TABLE cuotas ADD COLUMN mes INT NOT NULL DEFAULT 1 AFTER anio");
    } else {
        echo "La columna 'mes' ya existe.<br>";
    }

    // 2. Ajustar el índice único para permitir múltiples meses por año
    echo "Actualizando índices únicos...<br>";
    try {
        // Intentamos borrar el índice antiguo (puede llamarse anio, unique_anio, etc)
        // Normalmente es el PRIMARY KEY o un UNIQUE KEY sobre el campo del año
        $db->exec("ALTER TABLE cuotas DROP INDEX anio");
    } catch (Exception $e) { /* Ignorar si no existe */ }

    try {
        // Creamos el nuevo índice combinado
        $db->exec("ALTER TABLE cuotas ADD UNIQUE KEY idx_anio_mes (anio, mes)");
        echo "<b>Estructura actualizada correctamente. Ahora puedes definir cuotas diferentes por mes.</b>";
    } catch (Exception $e) {
        if (str_contains($e->getMessage(), 'Duplicate key')) {
            echo "Nota: El índice ya existe o hay datos duplicados que impiden crearlo.<br>";
        } else {
            echo "Error al crear índice: " . $e->getMessage() . "<br>";
        }
    }

} catch (Exception $e) {
    echo "Error fatal: " . $e->getMessage();
}
