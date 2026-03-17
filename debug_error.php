<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoloader
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
    echo "1. DB Connected successfully.\n";
    
    echo "2. Checking 'cuotas' table structure:\n";
    $stmt = $db->query("SHOW COLUMNS FROM cuotas");
    $cols = $stmt->fetchAll();
    foreach ($cols as $col) {
        $keys = array_keys($col);
        echo "   - " . ($col['Field'] ?? $col['field'] ?? '???') . "\n";
    }
    
    echo "3. Testing Cuota::getByYear(" . date('Y') . "):\n";
    $c = new \App\Models\Cuota();
    $res = $c->getByYear((int)date('Y'));
    echo "   - Result: " . json_encode($res) . "\n";
    
    echo "4. Testing Alumno::all():\n";
    $a = new \App\Models\Alumno();
    $alumnos = $a->all();
    echo "   - Count: " . count($alumnos) . "\n";

} catch (Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack:\n" . $e->getTraceAsString() . "\n";
}
