<?php
declare(strict_types=1);
namespace App\Core;
use PDO;

final class Database
{
    private static array $connections = [];

    public static function config(): array
    {
        return require BASE_PATH . '/config/database.php';
    }

    public static function connection(string $name = 'app'): PDO
    {
        return self::$connections[$name] ??= self::connect(self::config()[$name]);
    }

    public static function connect(array $config, bool $withDatabase = true): PDO
    {
        foreach (['host', 'port', 'database'] as $key) {
            if (str_contains($config[$key], ';')) {
                throw new \RuntimeException('Configuracion de base invalida.');
            }
        }
        if ($withDatabase && $config['database'] === '') {
            throw new \RuntimeException('Falta configurar la base de datos.');
        }
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        if ($withDatabase) {
            $dsn .= ';dbname=' . $config['database'];
        }
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->exec("SET time_zone = '+00:00'");
        return $pdo;
    }

    public static function identifier(string $name): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $name)) {
            throw new \InvalidArgumentException('Identificador SQL invalido.');
        }
        return '`' . $name . '`';
    }

    public static function assertSeparate(): void
    {
        $config = self::config();
        if ($config['app']['database'] === '' || $config['app']['database'] === $config['auth']['database']) {
            throw new \RuntimeException('Configura una base propia distinta a la de funcionarios.');
        }
    }
}
