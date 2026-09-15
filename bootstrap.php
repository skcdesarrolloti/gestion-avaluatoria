<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);
spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});
require BASE_PATH . '/app/Support/helpers.php';
$envFiles = [
    BASE_PATH . '/.env',
    BASE_PATH . '/.env.local',
    dirname(BASE_PATH) . '/.gestion-avaluatoria.env',
];
foreach ($envFiles as $envFile) {
    App\Core\Env::load($envFile);
}
date_default_timezone_set('America/Bogota');
