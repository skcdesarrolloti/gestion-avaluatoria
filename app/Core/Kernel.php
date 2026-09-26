<?php
declare(strict_types=1);
namespace App\Core;
use App\Controllers\{AppraisalController, AppraisalLegalController, AppraisalSubjectController, AuthController, DiagnosticController, IgacTypologyController, IfrsStandardController, ValuationGlossaryController, InternationalStandardController, LegalFrameworkController, MaintenanceController, MasterDataController, StandardController, UrbanNormativeLibraryController, ValuationController};
use App\Database\Migrator;
use App\Models\{AppraisalLegalRepository, AppraisalRepository, AppraisalSectorMidasFileRepository, AppraisalSubjectRepository, AppraiserRepository, FuncionarioRepository, GeoMasterRepository, IgacTypologyRepository, IfrsStandardRepository, InternationalStandardRepository, LegalDocumentRepository, ValuationGlossaryRepository, ValuationStandardRepository};
use App\Services\AuthService;
final class Kernel
{
    public function run(): void
    {
        Session::start();
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: same-origin');
        header('Cache-Control: no-store');
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $base = Http::basePath();
        if ($base !== '' && $path !== $base && !str_starts_with($path, $base . '/')) {
            throw new HttpException(404, 'Página no encontrada.');
        }
        $path = '/' . trim(substr($path, strlen($base)), '/');
        $method = $_SERVER['REQUEST_METHOD'];
        $routes = require BASE_PATH . '/routes/web.php';
        $matchedPath = false;
        foreach ($routes as [$verb, $pattern, $controller, $action, $protected]) {
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $matchedPath = true;
            if ($method !== $verb) {
                continue;
            }
            if ($method === 'POST' && !($controller === 'sectorMidas' && $action === 'consult')) {
                if (in_array($action, ['import', 'importFile'], true)
                    && in_array($controller, ['standards', 'legal', 'international', 'ifrs', 'urbanNorms'], true)
                    && $this->uploadLikelyExceededPostLimit()) {
                    $flash = match ($controller) {
                        'legal' => 'legal_import',
                        'international' => 'international_import',
                        'ifrs' => 'ifrs_import',
                        'urbanNorms' => 'urban_norm_import',
                        default => 'standards_import',
                    };
                    $route = match ($controller) {
                        'legal' => 'marco-juridico-valuatorio',
                        'international' => 'normas-internacionales-valuacion',
                        'ifrs' => 'normas-niif',
                        'urbanNorms' => 'normatividad-urbana',
                        default => 'normas-tecnicas-sectoriales',
                    };
                    Session::flash($flash, json_encode([
                        'ok' => false,
                        'message' => 'La carga superó el límite post_max_size de PHP. Sube menos archivos por lote o aumenta el límite en el hosting.',
                    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                    Http::redirect($route);
                }
                if ($controller === 'subjectPh' && $action === 'upload' && $this->uploadLikelyExceededPostLimit()) { Session::flash('ph_error', 'La carga superó el límite post_max_size de PHP. Sube menos soportes por lote o comprímelos en un ZIP menor.'); Http::redirect('avaluos/' . (string) ($matches[1] ?? '') . '/bien-sujeto#ph'); }
                try {
                    Session::csrf();
                } catch (HttpException $error) {
                    if ($controller === 'auth' && $action === 'attempt') {
                        Session::refreshCsrf();
                        Session::flash('login_error', $error->getMessage());
                        Session::flash('login_username', $this->postedUsername());
                        Http::redirect('login');
                    }
                    throw $error;
                }
            }
            if ($controller === 'auth' && $action === 'login') {
                view('auth/login', $this->loginData());
                return;
            }
            if ($controller === 'auth' && $action === 'logout') {
                Session::logout();
                Http::redirect('login');
            }
            if ($controller === 'diagnostics') {
                (new DiagnosticController())->$action(...array_slice($matches, 1));
                return;
            }
            if ($protected && empty($_SESSION['user'])) {
                if (Http::wantsJson()) {
                    throw new HttpException(401, 'Inicia sesión para continuar.');
                }
                Http::redirect('login');
            }
            try {
                $auth = $this->authService();
            } catch (\Throwable $error) {
                if ($controller === 'auth' && $action === 'attempt') {
                    $this->loginInfrastructureError($error);
                    Http::redirect('login');
                }
                throw $error;
            }
            $user = $protected ? $auth->current() : null;
            if ($protected && !$user) {
                if (Http::wantsJson()) {
                    throw new HttpException(401, 'Tu sesión venció. Conserva tus cambios e inicia sesión de nuevo.');
                }
                Http::redirect('login');
            }
            if ($protected) {
                Database::assertSeparate();
                $db = Database::connection();
                if (Env::bool('AUTO_MIGRATE', true)) (new Migrator($db, BASE_PATH . '/database/migrations'))->run();
            }
            $instance = match ($controller) {
                'auth' => new AuthController($auth),
                'ifrs' => new IfrsStandardController(new IfrsStandardRepository($db)),
                'urbanNorms' => new UrbanNormativeLibraryController(new \App\Models\UrbanNormativeRepository($db)),
                'typologies' => new IgacTypologyController(new IgacTypologyRepository()),
                'glossary' => new ValuationGlossaryController(new ValuationGlossaryRepository($db), $user),
                'international' => new InternationalStandardController(new InternationalStandardRepository($db)),
                'legal' => new LegalFrameworkController(new LegalDocumentRepository($db)),
                'maintenance' => new MaintenanceController($db, $user),
                'masters' => new MasterDataController(new AppraiserRepository($db)),
                'standards' => new StandardController(new ValuationStandardRepository($db)),
                'reportNotes' => new \App\Controllers\AppraisalReportNoteController(
                    new AppraisalRepository($db), new \App\Models\AppraisalReportNoteRepository($db), $user),
                'sector' => new \App\Controllers\AppraisalSectorController(new AppraisalRepository($db),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db), new AppraisalSubjectRepository($db),
                    new \App\Models\NeighborhoodSectorRepository($db), new \App\Models\SectorBankRepository($db),
                    new GeoMasterRepository($db), new AppraisalSectorMidasFileRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db), $user),
                'sectorMidas' => new \App\Controllers\AppraisalSectorMidasController(new AppraisalRepository($db),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db),
                    new AppraisalSubjectRepository($db), new \App\Models\SectorBankRepository($db), new AppraisalSectorMidasFileRepository($db), $user),
                'legalCharacteristics' => new AppraisalLegalController(new AppraisalRepository($db),
                    new AppraisalLegalRepository($db), new AppraisalSubjectRepository($db), $user, new \App\Models\AppraisalReportNoteRepository($db)),
                'urbanNormative' => new \App\Controllers\AppraisalUrbanNormController(new AppraisalRepository($db),
                    new \App\Models\AppraisalUrbanNormRepository($db), new \App\Models\UrbanNormativeRepository($db), new AppraisalSubjectRepository($db), $user, new \App\Models\AppraisalReportNoteRepository($db)),
                'valuationMethodology' => new \App\Controllers\AppraisalValuationMethodologyController(new AppraisalRepository($db), new AppraisalSubjectRepository($db), new \App\Models\AppraisalPhRepository($db), new \App\Services\AppraisalComparableSearchGuide(), $user),
                'subject' => new AppraisalSubjectController(new AppraisalRepository($db), $user,
                    new IgacTypologyRepository(), new AppraisalSubjectRepository($db), new GeoMasterRepository($db),
                    new \App\Models\AppraisalPhRepository($db), new \App\Models\AppraisalObsolescenceRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db)),
                'subjectMidas' => new \App\Controllers\AppraisalSubjectMidasController(new AppraisalRepository($db), new AppraisalSubjectRepository($db), new \App\Models\AppraisalUrbanNormRepository($db), $user),
                'subjectPh' => new \App\Controllers\AppraisalPhController(new AppraisalRepository($db), new \App\Models\AppraisalPhRepository($db), $user),
                'obsolescence' => new \App\Controllers\AppraisalObsolescenceController(new AppraisalRepository($db), new \App\Models\AppraisalObsolescenceRepository($db), $user),
                'valuations' => new ValuationController(),
                default => new AppraisalController(new AppraisalRepository($db), $user, new AppraiserRepository($db), new IgacTypologyRepository(),
                    new \App\Models\AppraisalPhRepository($db), new AppraisalSubjectRepository($db),
                    new \App\Models\AppraisalObsolescenceRepository($db), new \App\Services\AppraisalDossierNumberer($db),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db), new AppraisalLegalRepository($db),
                    new \App\Models\AppraisalUrbanNormRepository($db)),
            };
            $instance->$action(...array_slice($matches, 1));
            return;
        }
        throw new HttpException($matchedPath ? 405 : 404, $matchedPath ? 'Método no permitido.' : 'Página no encontrada.');
    }
    private function postedUsername(): string
    {
        $username = $_POST['username'] ?? '';
        return is_string($username) ? substr($username, 0, 190) : '';
    }
    private function uploadLikelyExceededPostLimit(): bool
    {
        $length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($length <= 0 || $_POST || $_FILES) return false;
        $limit = $this->iniBytes((string) ini_get('post_max_size'));
        return $limit > 0 && $length > $limit;
    }
    private function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') return 0;
        $bytes = (int) $value;
        return match (strtolower(substr($value, -1))) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }
    private function authService(): AuthService
    {
        return new AuthService(new FuncionarioRepository(Database::connection('auth')));
    }
    private function loginInfrastructureError(\Throwable $error): void
    {
        error_log('Gestion avaluatoria login auth ' . get_class($error) . ' code=' . $error->getCode()
            . ' at ' . basename($error->getFile()) . ':' . $error->getLine());
        Session::flash('login_error', $this->loginErrorMessage($error));
        Session::flash('login_username', $this->postedUsername());
    }
    private function loginErrorMessage(\Throwable $error): string
    {
        $text = $error->getMessage();
        if ($error instanceof \PDOException) {
            if (str_contains($text, '42S02') || str_contains($text, 'Base table or view not found')) {
                return 'No se encontró la tabla de funcionarios. Revisa AUTH_TABLE.';
            }
            if (str_contains($text, '42S22') || str_contains($text, 'Unknown column')) {
                return 'Faltan columnas de funcionarios. Revisa AUTH_USER_COLUMN y AUTH_PASSWORD_COLUMN.';
            }
            return 'No se pudo conectar a la base de funcionarios. Revisa AUTH_DB_* en producción.';
        }
        if (str_contains($text, 'Falta configurar la base de datos')) {
            return 'Falta configurar AUTH_DB_DATABASE en producción.';
        }
        if (str_contains($text, 'Configuracion de base invalida')) {
            return 'La configuración AUTH_DB_* contiene un valor inválido.';
        }
        if ($error instanceof \InvalidArgumentException) {
            return 'AUTH_TABLE o las columnas AUTH_* tienen un nombre inválido.';
        }
        return 'No se pudo verificar el acceso. Revisa la conexión de funcionarios.';
    }
    private function loginData(): array
    {
        return ['title' => 'Iniciar sesión', 'error' => Session::pullFlash('login_error'),
            'username' => Session::pullFlash('login_username')];
    }
}
