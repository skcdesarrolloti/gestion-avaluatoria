<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';
use App\Core\Session;
use App\Database\Schema;
use App\Core\Http;
use App\Services\AppraisalValidator;
use App\Services\AuthDiagnostics;
use App\Services\AuthService;
use App\Services\IfrsStandardFileImportService;
use App\Services\InternationalStandardFileImportService;
use App\Services\LegalDocumentFileImportService;
use App\Services\LegalDocumentImportService;
use App\Services\RateLimiter;
use App\Models\AppraiserRepository;
use App\Models\FuncionarioRepository;
use App\Models\IgacTypologyRepository;
use App\Models\IfrsStandardRepository;
use App\Models\InternationalStandardRepository;
use App\Models\LegalDocumentRepository;
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
    Session::refreshCsrf();
    expect($_SESSION['csrf'] !== 'test-token', 'CSRF renovado tras vencimiento');
    Session::flash('login_error', 'Mensaje temporal');
    expect(Session::pullFlash('login_error') === 'Mensaje temporal', 'flash disponible una vez');
    expect(Session::pullFlash('login_error') === '', 'flash se limpia al leer');
    $_SERVER['SCRIPT_NAME'] = '/public/index.php';
    putenv('APP_BASE_PATH');
    expect(Http::basePath() === '/public' && url('assets/app.css') === '/public/assets/app.css', 'base path public detectado');
    expect(str_starts_with(asset_url('assets/app.css'), '/public/assets/app.css?v='), 'assets versionados por archivo');
    putenv('APP_BASE_PATH=/avaluatoria');
    expect(Http::basePath() === '/avaluatoria', 'base path configurado prevalece');
    putenv('APP_BASE_PATH');
    unset($_SERVER['SCRIPT_NAME']);
    putenv('AUTH_DB_DATABASE=');
    $diagnostics = (new AuthDiagnostics())->run();
    expect($diagnostics[array_key_last($diagnostics)]['ok'] === false, 'diagnostico auth falla sin base configurada');
    $db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    fixture($db);
    $db->exec("CREATE TABLE valuation_standard_categories (code TEXT PRIMARY KEY, name TEXT, group_type TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_standards (slug TEXT PRIMARY KEY, category_code TEXT, standard_code TEXT, title TEXT, kind TEXT, sector_code TEXT, source_filename TEXT, storage_filename TEXT, summary TEXT, file_size_bytes INTEGER, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("INSERT INTO valuation_standard_categories VALUES
        ('A', 'Normas Técnicas Generales', 'general', 0, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_standards VALUES
        ('nts-s04-codigo-conducta', 'A', 'NTS S04', 'Código de conducta', 'NTS', 'S04',
        '01 NTS S04 Codigo conducta.pdf', 'unit-test-norma-inexistente.pdf', '', NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("CREATE TABLE valuation_legal_categories (code TEXT PRIMARY KEY, name TEXT, group_type TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_legal_documents (slug TEXT PRIMARY KEY, category_code TEXT, document_code TEXT, title TEXT, document_type TEXT, status TEXT, issued_at TEXT, repealed_at TEXT, source_reference TEXT, summary TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_legal_articles (id INTEGER PRIMARY KEY, document_slug TEXT, category_code TEXT, article_label TEXT, title TEXT, excerpt TEXT, applicability TEXT, status TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_international_groups (code TEXT PRIMARY KEY, name TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_international_standards (slug TEXT PRIMARY KEY, group_code TEXT, standard_code TEXT, title TEXT, applicable_categories TEXT, summary TEXT, effective_from TEXT, status TEXT, source_reference TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_ifrs_groups (code TEXT PRIMARY KEY, name TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_ifrs_standards (slug TEXT PRIMARY KEY, group_code TEXT, standard_code TEXT, title TEXT, applicable_categories TEXT, measurement_focus TEXT, summary TEXT, field_relevance TEXT, source_reference TEXT, status TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_field_considerations (field_key TEXT PRIMARY KEY, field_label TEXT, classification TEXT, normative_basis TEXT, operational_use TEXT, ifrs_relation TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_appraisers (id TEXT PRIMARY KEY, code TEXT UNIQUE, full_name TEXT,
        email TEXT, phone TEXT, raa_number TEXT, raa_categories TEXT, active TEXT, notes TEXT,
        raa_expires_at TEXT, raa_source_filename TEXT, raa_storage_filename TEXT,
        raa_file_size_bytes INTEGER, raa_uploaded_at TEXT, created_at TEXT, updated_at TEXT)");
    $db->exec("INSERT INTO valuation_legal_categories VALUES
        ('A', 'Marco jurídico general', 'general', 0, '2026-09-15 00:00:00', '2026-09-15 00:00:00'),
        ('1', 'Inmuebles urbanos', 'category', 11, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_legal_documents VALUES
        ('ley-388-1997', 'A', 'Ley 388 de 1997', 'Ordenamiento territorial', 'Ley', 'vigente',
        NULL, NULL, 'Fuente oficial', 'Documento fuente', '', '', NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_legal_articles VALUES
        (1, 'ley-388-1997', '1', 'Artículo 61', 'Adquisición de inmuebles',
        'Extracto pertinente para avalúos urbanos.', 'Usar solo cuando la finalidad corresponda.',
        'vigente', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_international_groups VALUES
        ('400', 'Inmuebles', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_international_standards VALUES
        ('ivs-400-real-property', '400', 'IVS 400', 'Real Property Interests', '1,2',
        'Derechos sobre inmuebles.', '2025-01-31', 'vigente', 'IVSC', '', '', NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_ifrs_groups VALUES ('G', 'Medición y valor razonable', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_ifrs_standards VALUES ('ifrs-13-fair-value-measurement', 'G', 'NIIF 13 / IFRS 13', 'Medición del valor razonable', 'A,1,2', 'Valor razonable', 'Marco NIIF para medición.', 'Respalda base/tipo de valor.', 'IFRS Foundation', 'vigente', '', '', NULL, NULL, 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_field_considerations VALUES ('base_valor', 'Base/tipo de valor', 'Normativo directo', 'NTS, IVS 102, NIIF 13, NIC 36', 'Define la base de valor.', 'Campo central cuando aplica NIIF.', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_field_considerations VALUES ('tipo_avaluo', 'Tipo de avalúo', 'Derivado metodológico', 'NTS e IVS según encargo', 'Duplicado legado.', 'Puede activar NIIF.', 2, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $removeDuplicate = require dirname(__DIR__) . '/database/migrations/202609150014_remove_duplicate_appraisal_consideration.php';
    $removeDuplicate(new Schema($db));
    expect($db->query("SELECT COUNT(*) FROM valuation_field_considerations WHERE field_key = 'tipo_avaluo'")->fetchColumn() === 0, 'consideracion duplicada de tipo de avaluo eliminada');
    $appraisers = new AppraiserRepository($db);
    $baseAppraiser = ['id' => '', 'email' => '', 'phone' => '', 'raa_number' => '',
        'raa_categories' => '["1"]', 'notes' => '', 'raa_expires_at' => '2026-12-31',
        'raa_source_filename' => 'raa.pdf', 'raa_storage_filename' => 'raa-test.pdf',
        'raa_file_size_bytes' => 100];
    $appraisers->create(array_replace($baseAppraiser, ['id' => bin2hex(random_bytes(16)), 'code' => '02',
        'full_name' => 'Nassif Abuita', 'active' => 'Si']));
    $appraisers->create(array_replace($baseAppraiser, ['id' => bin2hex(random_bytes(16)), 'code' => '01',
        'full_name' => 'Said', 'active' => 'No']));
    $appraiserRows = $appraisers->all();
    expect(count($appraiserRows) === 2 && $appraiserRows[0]['code'] === '02', 'maestro de peritos ordena activos primero');
    $seedB1 = require dirname(__DIR__) . '/database/migrations/202609150007_seed_b1_urban_legal_bibliography.php';
    $seedB1(new Schema($db));
    $standards = (new ValuationStandardRepository($db))->categoriesWithStandards();
    expect($standards[0]['code'] === 'A', 'categoria general disponible');
    expect($standards[0]['standards'][0]['standard_code'] === 'NTS S04', 'norma tecnica agrupada');
    expect($standards[0]['standards'][0]['has_file'] === false, 'PDF privado no se presume importado');
    $legal = new LegalDocumentRepository($db);
    $legalCategories = $legal->categoriesWithDocuments();
    expect($legalCategories[0]['name'] === 'Marco jurídico general', 'categoria juridica general disponible');
    expect(count($legalCategories[1]['articles']) === 1, 'marco juridico guarda articulos pertinentes por categoria');
    expect($legalCategories[1]['documents'][0]['document_code'] === 'B1-01', 'bibliografia B1 de urbanos sembrada');
    expect($legal->stats($legalCategories)['articles'] === 1, 'marco juridico cuenta articulos sin ley completa');
    $legalDir = sys_get_temp_dir() . '/ga_legal_' . bin2hex(random_bytes(4));
    putenv('LEGAL_STORAGE_DIR=' . $legalDir);
    $tmpLegalPdf = tempnam(sys_get_temp_dir(), 'ga_legal_pdf_');
    file_put_contents($tmpLegalPdf, "%PDF-1.4\n%legal\n");
    $legalImport = (new LegalDocumentImportService($legal))->importUploaded(uploadFixture('Ley 1673 de 2013.pdf', $tmpLegalPdf), 'A', 'vigente');
    expect(count($legalImport['copied']) === 1, 'importacion PDF juridico');
    $tmpB1Pdf = tempnam(sys_get_temp_dir(), 'ga_b1_pdf_');
    file_put_contents($tmpB1Pdf, "%PDF-1.4\n%b1\n");
    (new LegalDocumentImportService($legal))->importUploaded(uploadFixture('Ley 388 de 1997.pdf', $tmpB1Pdf), '1', 'vigente');
    $urbanDocument = $legal->find('b1-01-ley-388-1997');
    expect($urbanDocument['has_file'] && $urbanDocument['source_filename'] === 'Ley 388 de 1997.pdf', 'importacion juridica enlaza PDF con documento B1');
    $tmpCardPdf = tempnam(sys_get_temp_dir(), 'ga_b1_card_pdf_');
    file_put_contents($tmpCardPdf, "%PDF-1.4\n%b1-card\n");
    (new LegalDocumentFileImportService($legal))->importFor(uploadFixture('archivo consultado.pdf', $tmpCardPdf), $legal->find('b1-02-decreto-1170-2015-capitulo-3'));
    $cardDocument = $legal->find('b1-02-decreto-1170-2015-capitulo-3');
    expect($cardDocument['has_file'] && $cardDocument['source_filename'] === 'archivo consultado.pdf', 'importacion juridica por tarjeta no depende del nombre');
    if (class_exists(ZipArchive::class)) {
        $zipPath = tempnam(sys_get_temp_dir(), 'ga_legal_zip_');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::OVERWRITE);
        $zip->addFromString('Decreto 1420 de 1998.pdf', "%PDF-1.4\n%decreto\n");
        $zip->addFromString('carpeta/Resolucion 620 de 2008.pdf', "%PDF-1.4\n%resolucion\n");
        $zip->close();
        $zipImport = (new LegalDocumentImportService($legal))->importUploaded(uploadFixture('normativa-juridica.zip', $zipPath), 'A', 'vigente');
        expect(count($zipImport['copied']) === 2, 'importacion ZIP juridico extrae PDFs');
        unlink($zipPath);
    }
    $legalCategories = $legal->categoriesWithDocuments();
    $storedLegal = $legalCategories[0]['documents'][1];
    expect($storedLegal['has_file'] === true && $storedLegal['document_type'] === 'Ley', 'documento juridico queda consultable');
    unlink(LegalDocumentRepository::storagePath($storedLegal['storage_filename']));
    expect($legal->storageReport()['marked_missing'] === 1, 'diagnostico juridico detecta PDF faltante');
    foreach ($legal->categoriesWithDocuments() as $category) {
        foreach ($category['documents'] as $document) {
            if ($document['has_file']) unlink(LegalDocumentRepository::storagePath($document['storage_filename']));
        }
    }
    rmdir($legalDir);
    putenv('LEGAL_STORAGE_DIR');
    $international = new InternationalStandardRepository($db);
    $ivsGroups = $international->groupsWithStandards();
    expect($ivsGroups[0]['standards'][0]['standard_code'] === 'IVS 400', 'normas internacionales separadas');
    $ivsDir = sys_get_temp_dir() . '/ga_ivs_' . bin2hex(random_bytes(4));
    putenv('IVS_STORAGE_DIR=' . $ivsDir);
    $tmpIvsPdf = tempnam(sys_get_temp_dir(), 'ga_ivs_pdf_');
    file_put_contents($tmpIvsPdf, "%PDF-1.4\n%ivs\n");
    (new InternationalStandardFileImportService($international))->importFor(uploadFixture('IVS 400.pdf', $tmpIvsPdf),
        $international->find('ivs-400-real-property'));
    $ivs400 = $international->find('ivs-400-real-property');
    expect($ivs400['has_file'] && $international->storageReport()['present'] === 1, 'importacion PDF IVS por tarjeta');
    unlink(InternationalStandardRepository::storagePath($ivs400['storage_filename']));
    rmdir($ivsDir);
    putenv('IVS_STORAGE_DIR');
    $ifrs = new IfrsStandardRepository($db);
    expect($ifrs->groupsWithStandards()[0]['standards'][0]['standard_code'] === 'NIIF 13 / IFRS 13', 'normas NIIF separadas');
    expect($ifrs->considerations()[0]['classification'] === 'Normativo directo', 'campos del expediente clasificados');
    $ifrsDir = sys_get_temp_dir() . '/ga_ifrs_' . bin2hex(random_bytes(4));
    putenv('IFRS_STORAGE_DIR=' . $ifrsDir);
    $tmpIfrsPdf = tempnam(sys_get_temp_dir(), 'ga_ifrs_pdf_');
    file_put_contents($tmpIfrsPdf, "%PDF-1.4\n%ifrs\n");
    (new IfrsStandardFileImportService($ifrs))->importFor(uploadFixture('IFRS 13.pdf', $tmpIfrsPdf), $ifrs->find('ifrs-13-fair-value-measurement'));
    $ifrs13 = $ifrs->find('ifrs-13-fair-value-measurement');
    expect($ifrs13['has_file'] && $ifrs->storageReport()['present'] === 1, 'importacion PDF NIIF por tarjeta');
    unlink(IfrsStandardRepository::storagePath($ifrs13['storage_filename']));
    rmdir($ifrsDir);
    putenv('IFRS_STORAGE_DIR');
    $igac = new IgacTypologyRepository();
    expect($igac->stats()['total'] === 202, 'catalogo IGAC contiene 202 tipologias');
    expect(count($igac->byCategory('RESIDENCIALES')) === 23, 'tipologias IGAC agrupadas por categoria');
    expect(count($igac->optionsByCategory()['ANEXOS']) === 136, 'selector IGAC filtra tipologias por categoria');
    $normsDir = sys_get_temp_dir() . '/ga_normas_' . bin2hex(random_bytes(4));
    putenv('NTS_STORAGE_DIR=' . $normsDir);
    expect(ValuationStandardRepository::storageDir() === str_replace('\\', '/', $normsDir), 'carpeta privada de normas configurable');
    $tmpPdf = tempnam(sys_get_temp_dir(), 'ga_pdf_');
    file_put_contents($tmpPdf, "%PDF-1.4\n%test\n");
    $import = (new ValuationStandardRepository($db))->importUploaded(uploadFixture('01 NTS S04 Codigo conducta.pdf', $tmpPdf));
    expect(count($import['copied']) === 1 && $import['missing'] === [], 'importacion PDF por lote');
    $report = (new ValuationStandardRepository($db))->storageReport();
    expect($report['configured'] === true && $report['present'] === 1 && $report['marked_missing'] === 0, 'diagnostico confirma PDF persistido');
    unlink(ValuationStandardRepository::storagePath('unit-test-norma-inexistente.pdf'));
    $report = (new ValuationStandardRepository($db))->storageReport();
    expect($report['marked_missing'] === 1, 'diagnostico detecta BD marcada sin archivo fisico');
    rmdir($normsDir);
    putenv('NTS_STORAGE_DIR');
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
    $blockedDirectory = __DIR__ . '/.runtime/rate-limit-blocked';
    file_put_contents($blockedDirectory, 'blocked');
    (new RateLimiter($blockedDirectory))->consume(bin2hex(random_bytes(8)), 2);
    expect(true, 'rate limit usa temporal si storage falla');
    unlink($blockedDirectory);
    report();
} finally {
    session_destroy();
}
