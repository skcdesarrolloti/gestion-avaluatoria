<?php
declare(strict_types=1);

function expect(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException('FALLO: ' . $label);
    }
    $GLOBALS['checks'][] = $label;
}

function expectStatus(int $status, callable $callback, string $label): void
{
    try {
        $callback();
    } catch (App\Core\HttpException $error) {
        expect($error->status === $status, $label);
        return;
    }
    throw new RuntimeException('FALLO: ' . $label . ' no rechazo la solicitud');
}

function uploadFixture(string $name, string $tmpName, int $error = UPLOAD_ERR_OK): array
{
    return ['name' => [$name], 'tmp_name' => [$tmpName], 'error' => [$error]];
}

function fixture(PDO $db): void
{
    $db->exec('CREATE TABLE wp_jet_cct_funcionarios (_ID INTEGER PRIMARY KEY, id_empleado VARCHAR(80),
        nombre VARCHAR(100), rol VARCHAR(50), activo VARCHAR(10), user_others_apss VARCHAR(100), pass_others_apss VARCHAR(255))');
    $query = $db->prepare('INSERT INTO wp_jet_cct_funcionarios VALUES (?, ?, ?, ?, ?, ?, ?)');
    $query->execute([1, 'EMP-101', 'Prueba Avaluatoria', 'perito', 'Si', 'ga_test', password_hash('Only-test-2026!', PASSWORD_DEFAULT)]);
    $query->execute([2, 'EMP-102', 'Inactivo', 'perito', 'No', 'inactive', password_hash('Only-test-2026!', PASSWORD_DEFAULT)]);
    $query->execute([3, 'EMP-103', 'Legacy', 'perito', 'Si', 'legacy', 'only-test-legacy']);
}

function report(): void
{
    foreach ($GLOBALS['checks'] ?? [] as $check) {
        echo "OK $check\n";
    }
    echo count($GLOBALS['checks'] ?? []) . " verificaciones correctas.\n";
}
