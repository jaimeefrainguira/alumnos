<?php

return [
    'host' => $_ENV['DB_HOST'] ?? 'sql211.hstn.me',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_NAME'] ?? 'mseet_41403283_alumos',
    'username' => $_ENV['DB_USER'] ?? 'mseet_41403283',
    'password' => $_ENV['DB_PASS'] ?? '4016508a8b',
    'charset' => 'utf8mb4',
];
