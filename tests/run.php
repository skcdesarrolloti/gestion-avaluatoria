<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';
use App\Core\Session;
use App\Core\Http;
use App\Services\AppraisalValidator;
use App\Services\AuthService;
use App\Services\RateLimiter;
use App\Models\FuncionarioRepository;
use App\Models\ValuationStandardRepository;

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
    $_SERVER['SCRIPT_NAME'] = '/public/index.php';
    putenv('APP_BASE_PATH');
    expect(Http::basePath() === '/public' && url('assets/app.css') === '/public/assets/app.css', 'base path public detectado');
    putenv('APP_BASE_PATH=/avaluatoria');
    expect(Http::basePath() === '/avaluatoria', 'base path configurado prevalece');
    putenv('APP_BASE_PATH');
    unset($_SERVER['SCRIPT_NAME']);
    $db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    fixture($db);
    $db->exec("CREATE TABLE valuation_standard_categories (
        code TEXT PRIMARY KEY, name TEXT, group_type TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_standards (
        slug TEXT PRIMARY KEY, category_code TEXT, standard_code TEXT, title TEXT, kind TEXT, sector_code TEXT,
        source_filename TEXT, storage_filename TEXT, summary TEXT, file_size_bytes INTEGER,
        imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("INSERT INTO valuation_standard_categories VALUES
        ('A', 'Normas Técnicas Generales', 'general', 0, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_standards VALUES
        ('nts-s04-codigo-conducta', 'A', 'NTS S04', 'Código de conducta', 'NTS', 'S04',
        '01 NTS S04 Codigo conducta.pdf', 'unit-test-norma-inexistente.pdf', '', NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $standards = (new ValuationStandardRepository($db))->categoriesWithStandards();
    expect($standards[0]['code'] === 'A', 'categoria general disponible');
    expect($standards[0]['standards'][0]['standard_code'] === 'NTS S04', 'norma tecnica agrupada');
    expect($standards[0]['standards'][0]['has_file'] === false, 'PDF privado no se presume importado');
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
