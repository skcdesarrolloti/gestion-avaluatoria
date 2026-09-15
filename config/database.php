<?php
declare(strict_types=1);
use App\Core\Env;

$connection = static fn (string $prefix): array => [
    'host' => Env::get($prefix . 'HOST', '127.0.0.1'),
    'port' => Env::get($prefix . 'PORT', '3306'),
    'database' => Env::get($prefix . 'DATABASE'),
    'username' => Env::get($prefix . 'USERNAME'),
    'password' => Env::get($prefix . 'PASSWORD'),
];
return ['app' => $connection('DB_'), 'auth' => $connection('AUTH_DB_')];
