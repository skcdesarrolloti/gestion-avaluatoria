<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';
use App\Core\Session;
use App\Services\AppraisalValidator;
use App\Services\AuthService;
use App\Services\RateLimiter;
use App\Models\FuncionarioRepository;

// All fixtures are in memory; never connect to the configured production database.
foreach (['AUTH_TABLE' => 'wp_jet_cct_funcionarios', 'AUTH_USER_COLUMN' => 'user_others_apss',
    'AUTH_PASSWORD_COLUMN' => 'pass_others_apss', 'AUTH_ALLOWED_ROLES' => '',
    'AUTH_ALLOW_LEGACY_PASSWORDS' => 'false'] as $key => $value) {
    putenv("$key=$value");
}
session_start();
$_SESSION['csrf'] = 'test-token';
try {
    $data = ['titulo' => '', 'tipo' => '', 'direccion' => '', 'municipio' => '', 'observaciones' => '', 'version' => 1];
    expect(AppraisalValidator::validate($data)['titulo'] === '', 'borrador incompleto permitido');
    expectStatus(422, fn () => AppraisalValidator::validate(array_replace($data, ['titulo' => str_repeat('á', 161)])), 'limite Unicode');
    expectStatus(422, fn () => AppraisalValidator::validate(array_replace($data, ['tipo' => 'invalido'])), 'catalogo invalidado');
    expectStatus(422, fn () => AppraisalValidator::validate(array_replace($data, ['version' => '1'])), 'version debe ser entero');
    expectStatus(422, fn () => AppraisalValidator::validate(array_replace($data, ['municipio' => []])), 'rechazo de arrays en campos');
    expectStatus(419, fn () => Session::csrf(), 'CSRF obligatorio');
    $_SERVER['HTTP_X_CSRF_TOKEN'] = 'test-token';
    Session::csrf();
    expect(true, 'CSRF valido');
    $db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    fixture($db);
    $auth = new AuthService(new FuncionarioRepository($db));
    expect(!$auth->attempt('ga_test', 'incorrecta'), 'contraseña incorrecta rechazada');
    expect(!$auth->attempt('inactive', 'Only-test-2026!'), 'funcionario inactivo rechazado');
    expect(!$auth->attempt('legacy', 'only-test-legacy'), 'legacy deshabilitado por defecto');
    expect($auth->attempt('ga_test', 'Only-test-2026!'), 'hash PHP y login de otras apps');
    expect($_SESSION['user']['id'] === 1 && $_SESSION['user']['employee_id'] === 'EMP-101', 'identidades separadas');
    expect(!isset($_SESSION['user']['password']), 'sesion sin contraseña');
    $db->exec("UPDATE wp_jet_cct_funcionarios SET activo = 'No' WHERE _ID = 1");
    expect($auth->current() === null, 'revocacion de actividad en sesion vigente');
    putenv('AUTH_ALLOW_LEGACY_PASSWORDS=true');
    expect($auth->attempt('legacy', 'only-test-legacy'), 'compatibilidad legacy explicita');
    putenv('AUTH_ALLOWED_ROLES=administrador');
    expect(!$auth->attempt('legacy', 'only-test-legacy'), 'restriccion de roles');
    putenv('AUTH_ALLOWED_ROLES=');
    $_SESSION['last_activity'] = time() - 90000;
    expect($auth->current() === null, 'sesion expirada');
    $key = bin2hex(random_bytes(8));
    $limiter = new RateLimiter(__DIR__ . '/.runtime/rate-limits');
    $limiter->consume($key, 2);
    $limiter->consume($key, 2);
    expectStatus(429, fn () => $limiter->consume($key, 2), 'limite persistente de intentos');
    unlink(__DIR__ . '/.runtime/rate-limits/' . hash('sha256', $key) . '.json');
    report();
} finally {
    session_destroy();
}
