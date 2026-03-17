<?php
$config = require 'config/database.php';
try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "SUCCESS: Connected to host {$config['host']}\n";
    $stmt = $pdo->query("SHOW DATABASES");
    echo "DATABASES:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $db) {
        echo "- $db\n";
    }
} catch (Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
}
