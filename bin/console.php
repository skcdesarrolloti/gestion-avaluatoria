<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
use App\Core\Database;
use App\Database\Migrator;
use App\Services\AuthDiagnostics;
use App\Models\ValuationStandardRepository;

try {
    $command = $argv[1] ?? 'help';
    if (!in_array($command, ['install', 'migrate', 'auth:check', 'auth:diagnose', 'standards:import'], true)) {
        echo "Comandos:\n  install [--create-database]\n  migrate\n  auth:check\n  auth:diagnose\n  standards:import <carpeta>\n";
        exit($command === 'help' ? 0 : 1);
    }
    if ($command === 'auth:diagnose') {
        foreach ((new AuthDiagnostics())->run() as $check) {
            echo ($check['ok'] ? 'OK ' : 'ERROR ') . $check['message'] . "\n";
        }
        exit(0);
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
    if ($command === 'standards:import') {
        $source = $argv[2] ?? getenv('NTS_SOURCE_DIR') ?: '';
        if ($source === '') {
            throw new RuntimeException('Indica la carpeta de origen de las normas.');
        }
        $result = (new ValuationStandardRepository(Database::connection()))->importFrom($source);
        echo $applied ? implode("\n", $applied) . "\n" : "Base al dia; no hay migraciones pendientes.\n";
        echo 'PDF copiados: ' . count($result['copied']) . "\n";
        echo 'PDF sin cambios: ' . count($result['skipped']) . "\n";
        echo 'PDF faltantes: ' . count($result['missing']) . "\n";
        foreach ($result['missing'] as $missing) {
            echo "  - $missing\n";
        }
        exit($result['missing'] ? 1 : 0);
    }
    echo $applied ? implode("\n", $applied) . "\n" : "Base al dia; no hay migraciones pendientes.\n";
} catch (Throwable $error) {
    // PDO errors may contain connection/user details: don't expose credentials on CLI either.
    $message = $error instanceof PDOException
        ? 'No se pudo completar la operacion de BD (codigo ' . $error->getCode() . '). Revisa .env y permisos.'
        : $error->getMessage();
    fwrite(STDERR, $message . "\n");
    exit(1);
}
