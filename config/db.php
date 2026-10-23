<?php

$env = static fn(string $k, string $default = '') => $_ENV[$k] ?? (getenv($k) !== false ? getenv($k) : $default);

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf('mysql:host=%s;dbname=%s', $env('DB_HOST', '127.0.0.1'), $env('DB_NAME', 'kkkk')),
    'username' => $env('DB_USER', 'root'),
    'password' => $env('DB_PASSWORD', 'test'),
    'charset' => 'utf8mb4',

    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
