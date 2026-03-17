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

function testQuery($db, $sql, $params = []) {
    echo "QUERY: $sql\n";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $count = $stmt->rowCount();
        echo " - SUCCESS ($count rows)\n";
        return $stmt->fetchAll();
    } catch (Exception $e) {
        echo " - ERROR: " . $e->getMessage() . "\n";
        return null;
    }
}

try {
    $db = Database::getConnection();
    echo "1. DB Connection OK\n";
    
    echo "2. Testing Alumnos:\n";
    testQuery($db, "SELECT * FROM alumnos LIMIT 1");
    
    echo "3. Testing Abonos:\n";
    testQuery($db, "SELECT * FROM abonos LIMIT 1");
    
    echo "4. Testing Cuotas:\n";
    testQuery($db, "SELECT * FROM cuotas LIMIT 1");
    
    echo "5. Detailed Column Check:\n";
    foreach (['usuarios', 'alumnos', 'abonos', 'cuotas'] as $table) {
        echo "   Table '$table': ";
        $stmt = $db->query("SHOW COLUMNS FROM $table");
        $cols = array_map(fn($c) => $c['Field'], $stmt->fetchAll());
        echo implode(", ", $cols) . "\n";
    }

} catch (Exception $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
