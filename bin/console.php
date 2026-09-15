<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
use App\Core\Database;
use App\Database\Migrator;

try {
    $command = $argv[1] ?? 'help';
    if (!in_array($command, ['install', 'migrate', 'auth:check'], true)) {
        echo "Comandos:\n  install [--create-database]\n  migrate\n  auth:check\n";
        exit($command === 'help' ? 0 : 1);
    }
    Database::assertSeparate();
    if ($command === 'auth:check') {
        $repository = new App\Models\FuncionarioRepository(Database::connection('auth'));
        $repository->checkSchema();
        echo "Conexion y columnas de funcionarios verificadas. No se modificaron usuarios.\n";
        exit(0);
    }
    if ($command === 'install' && in_array('--create-database', $argv, true)) {
        $config = Database::config()['app'];
        $db = Database::connect($config, false);
        $db->exec('CREATE DATABASE IF NOT EXISTS ' . Database::identifier($config['database'])
            . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        echo "Base propia preparada.\n";
    }
    $applied = (new Migrator(Database::connection(), BASE_PATH . '/database/migrations'))->run();
    echo $applied ? implode("\n", $applied) . "\n" : "Base al dia; no hay migraciones pendientes.\n";
} catch (Throwable $error) {
    // PDO errors may contain connection/user details: don't expose credentials on CLI either.
    $message = $error instanceof PDOException
        ? 'No se pudo completar la operacion de BD (codigo ' . $error->getCode() . '). Revisa .env y permisos.'
        : $error->getMessage();
    fwrite(STDERR, $message . "\n");
    exit(1);
}
