<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';
use App\Core\Database;
use App\Database\Migrator;
use App\Models\AppraisalRepository;

$port = getenv('GA_TEST_PORT');
if (!$port || !ctype_digit($port) || $port === '3306') {
    throw new RuntimeException('Configura GA_TEST_PORT con el puerto del MySQL local DESECHABLE (distinto de 3306).');
}
$config = ['host' => '127.0.0.1', 'port' => $port, 'database' => 'ga_test_app',
    'username' => getenv('GA_TEST_USER') ?: 'root', 'password' => getenv('GA_TEST_PASSWORD') ?: ''];
$server = Database::connect($config, false);
foreach (['ga_test_app', 'ga_test_auth'] as $name) {
    $check = $server->prepare('SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name = ?');
    $check->execute([$name]);
    if ($check->fetchColumn()) {
        throw new RuntimeException('El entorno de prueba ya existe; utiliza una instancia desechable nueva.');
    }
}
foreach (['ga_test_app', 'ga_test_auth'] as $name) {
    $server->exec('CREATE DATABASE ' . Database::identifier($name) . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}
$app = Database::connect($config);
$auth = Database::connect(array_replace($config, ['database' => 'ga_test_auth']));
fixture($auth);
$directory = BASE_PATH . '/database/migrations';
$first = require $directory . '/202609150001_create_appraisals.php';
$first(new App\Database\Schema($app));
$app->exec("INSERT INTO appraisals (id, owner_id, titulo, created_at, updated_at)
    VALUES ('11111111111111111111111111111111', 1, 'Conservar datos', UTC_TIMESTAMP(), UTC_TIMESTAMP())");
$migrator = new Migrator($app, $directory);
expect(count($migrator->run()) === 2, 'migracion recupera tabla parcial y agrega columna');
expect($migrator->run() === [], 'migracion repetida no duplica cambios');
$repo = new AppraisalRepository($app);
expect($repo->find(str_repeat('1', 32), 1)['titulo'] === 'Conservar datos', 'datos previos preservados al agregar columna');
$id = $repo->create(1);
expectStatus(404, fn () => $repo->find($id, 2), 'lectura de ficha ajena rechazada');
$data = ['titulo' => 'Prueba Bogotá', 'tipo' => 'urbano', 'direccion' => 'Calle 10', 'municipio' => 'Bogotá', 'observaciones' => 'Borrador ñ'];
expect($repo->save($id, 1, 1, $data)['version'] === 2, 'guardado aumenta version');
expectStatus(409, fn () => $repo->save($id, 1, 1, $data), 'version antigua rechazada');
expectStatus(404, fn () => $repo->save($id, 2, 2, $data), 'escritura de ficha ajena rechazada');
expect($repo->find($id, 1)['observaciones'] === 'Borrador ñ', 'persistencia utf8 y recarga');
expect(count($repo->recent(2, 1)) === 0, 'listado aislado por propietario');
// An applied migration must never be silently changed.
$app->exec("UPDATE schema_migrations SET checksum = REPEAT('0', 64) WHERE version = '202609150002_add_observaciones.php'");
$detected = false;
try { $migrator->run(); } catch (RuntimeException $error) { $detected = str_contains($error->getMessage(), 'modificada'); }
expect($detected, 'checksum detecta alteracion');
$restore = $app->prepare('UPDATE schema_migrations SET checksum = ? WHERE version = ?');
$restore->execute([hash_file('sha256', $directory . '/202609150002_add_observaciones.php'), '202609150002_add_observaciones.php']);
report();
