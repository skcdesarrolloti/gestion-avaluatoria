<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/support.php';
use App\Core\Session;
use App\Database\Schema;
use App\Core\Http;
use App\Services\AppraisalValidator;
use App\Services\AppraisalAttributeInput;
use App\Services\AppraisalChapterZeroInput;
use App\Services\AppraisalSectorInput;
use App\Services\AppraisalMidasReview;
use App\Services\AppraisalMidasSupportUploadService;
use App\Services\AppraisalLegalInput;
use App\Services\AppraisalLegalCertificateStorage;
use App\Services\AppraisalLegalCertificateUploadService;
use App\Services\AppraisalSectorAdvancedPrefill;
use App\Services\AppraisalSectorSectionInput;
use App\Services\AppraisalSectorPrefill;
use App\Services\AuthDiagnostics;
use App\Services\AuthService;
use App\Services\IfrsStandardFileImportService;
use App\Services\InternationalStandardFileImportService;
use App\Services\LegalDocumentFileImportService;
use App\Services\LegalDocumentImportService;
use App\Services\MidasLayerPlan;
use App\Services\MidasGeometry;
use App\Services\MidasWfsLayerAnalyzer;
use App\Services\MidasWfsLayerCatalog;
use App\Services\MidasWfsSearch;
use App\Services\LegalCertificateParser;
use App\Services\RateLimiter;
use App\Controllers\AppraisalController;
use App\Controllers\AppraisalSubjectController;
use App\Models\AppraisalLegalRepository;
use App\Models\AppraisalSubjectRepository;
use App\Models\AppraiserRepository;
use App\Models\FuncionarioRepository;
use App\Models\GeoMasterRepository;
use App\Models\IgacTypologyRepository;
use App\Models\IfrsStandardRepository;
use App\Models\InternationalStandardRepository;
use App\Models\LegalDocumentRepository;
use App\Models\NeighborhoodSectorRepository;
use App\Models\AppraisalSectorSectionRepository;
use App\Models\AppraisalSectorMidasFileRepository;
use App\Models\SectorBankRepository;
use App\Models\ValuationStandardRepository;
use App\Support\AppraisalSectorCatalog;
use App\Support\AppraisalSectorFieldGuidance;
use App\Support\AppraisalLegalCatalog;
use App\Support\AppraisalLegalView;
use App\Support\SectorBankCatalog;

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
    expect(AppraisalValidator::validate(array_replace($data, [
        'tipo_inmueble' => 'edificio', 'subtipo_funcional' => 'edificio_mixto',
    ]))['subtipo_funcional'] === 'edificio_mixto', 'subtipo acorde al tipo de inmueble');
    expectStatus(422, fn () => AppraisalValidator::validate(array_replace($data, [
        'tipo_inmueble' => 'edificio', 'subtipo_funcional' => 'lote_urbano',
    ])), 'subtipo incompatible rechazado');
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
    $db->exec("CREATE TABLE valuation_standards (slug TEXT PRIMARY KEY, category_code TEXT, standard_code TEXT, title TEXT, kind TEXT, sector_code TEXT, source_filename TEXT, storage_filename TEXT, summary TEXT, file_size_bytes INTEGER, pdf_blob BLOB, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("INSERT INTO valuation_standard_categories VALUES
        ('A', 'Normas Técnicas Generales', 'general', 0, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_standards VALUES
        ('nts-s04-codigo-conducta', 'A', 'NTS S04', 'Código de conducta', 'NTS', 'S04',
        '01 NTS S04 Codigo conducta.pdf', 'unit-test-norma-inexistente.pdf', '', NULL, NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("CREATE TABLE valuation_legal_categories (code TEXT PRIMARY KEY, name TEXT, group_type TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_legal_documents (slug TEXT PRIMARY KEY, category_code TEXT, document_code TEXT, title TEXT, document_type TEXT, status TEXT, issued_at TEXT, repealed_at TEXT, source_reference TEXT, summary TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, pdf_blob BLOB, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_legal_articles (id INTEGER PRIMARY KEY, document_slug TEXT, category_code TEXT, article_label TEXT, title TEXT, excerpt TEXT, applicability TEXT, status TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_international_groups (code TEXT PRIMARY KEY, name TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_international_standards (slug TEXT PRIMARY KEY, group_code TEXT, standard_code TEXT, title TEXT, applicable_categories TEXT, summary TEXT, effective_from TEXT, status TEXT, source_reference TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, pdf_blob BLOB, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_ifrs_groups (code TEXT PRIMARY KEY, name TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_ifrs_standards (slug TEXT PRIMARY KEY, group_code TEXT, standard_code TEXT, title TEXT, applicable_categories TEXT, measurement_focus TEXT, summary TEXT, field_relevance TEXT, source_reference TEXT, status TEXT, source_filename TEXT DEFAULT '', storage_filename TEXT DEFAULT '', file_size_bytes INTEGER, pdf_blob BLOB, imported_at TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_field_considerations (field_key TEXT PRIMARY KEY, field_label TEXT, classification TEXT, normative_basis TEXT, operational_use TEXT, ifrs_relation TEXT, sort_order INTEGER, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE valuation_appraisers (id TEXT PRIMARY KEY, code TEXT UNIQUE, full_name TEXT,
        email TEXT, phone TEXT, raa_number TEXT, raa_categories TEXT, active TEXT, notes TEXT,
        raa_expires_at TEXT, raa_source_filename TEXT, raa_storage_filename TEXT,
        raa_file_size_bytes INTEGER, raa_uploaded_at TEXT, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE master_departments (id TEXT PRIMARY KEY, code TEXT, name TEXT UNIQUE,
        active TEXT, created_at TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE master_cities (id TEXT PRIMARY KEY, department_id TEXT, code TEXT, name TEXT,
        active TEXT, created_at TEXT, updated_at TEXT, UNIQUE(department_id, name))");
    $db->exec("CREATE TABLE master_localities (id TEXT PRIMARY KEY, city_id TEXT, name TEXT,
        active TEXT, notes TEXT, created_at TEXT, updated_at TEXT, UNIQUE(city_id, name))");
    $db->exec("CREATE TABLE master_neighborhoods (id TEXT PRIMARY KEY, city_id TEXT, locality_id TEXT, name TEXT,
        commune_ucg TEXT, zone_sector TEXT, active TEXT, notes TEXT, created_at TEXT, updated_at TEXT,
        UNIQUE(city_id, name))");
    $db->exec("CREATE TABLE appraisal_subjects (appraisal_id TEXT PRIMARY KEY, owner_id INTEGER,
        department_id TEXT, department_name TEXT, city_id TEXT, city_name TEXT, neighborhood_id TEXT,
        neighborhood_name TEXT, locality_name TEXT, commune_ucg TEXT, zone_sector TEXT,
        subject_title TEXT, point_reference TEXT, address TEXT, address_certificate TEXT,
        address_midas TEXT, address_tax TEXT, address_deed TEXT, address_other TEXT,
        adopted_source TEXT, adopted_address TEXT, alternate_nomenclature TEXT, horizontal_property TEXT,
        centrality TEXT, immediate_environment TEXT, stratum TEXT, property_registry TEXT,
        cadastral_reference TEXT, registry_office TEXT, urban_license TEXT, permitted_use TEXT,
        urban_treatment TEXT, restrictions TEXT, legal_urban_affectations TEXT, road_condition TEXT,
        access_facility TEXT, transport_connectivity TEXT, loading_unloading TEXT, current_use TEXT,
        main_potential_use TEXT, complementary_potential_uses TEXT, main_complementary_activity TEXT,
        secondary_complementary_activities TEXT, current_occupation TEXT, water_service TEXT,
        energy_service TEXT, gas_service TEXT, sewer_service TEXT, internet_service TEXT,
        service_continuity TEXT, subject_reference_date TEXT, latitude TEXT, longitude TEXT,
        notes TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE appraisal_legal_profiles (
        appraisal_id TEXT PRIMARY KEY, owner_id INTEGER, source_certificate_id TEXT,
        status TEXT, data_json TEXT, annotations_json TEXT, alerts_json TEXT,
        extracted_text TEXT, updated_at TEXT)");
    $db->exec("CREATE TABLE appraisal_legal_certificates (
        id TEXT PRIMARY KEY, appraisal_id TEXT, owner_id INTEGER, source_filename TEXT,
        storage_filename TEXT, mime_type TEXT, file_size_bytes INTEGER, extracted_chars INTEGER,
        analysis_status TEXT, analysis_message TEXT, file_blob BLOB, created_at TEXT)");
    $db->exec("INSERT INTO valuation_legal_categories VALUES
        ('A', 'Marco jurídico general', 'general', 0, '2026-09-15 00:00:00', '2026-09-15 00:00:00'),
        ('1', 'Inmuebles urbanos', 'category', 11, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_legal_documents VALUES
        ('ley-388-1997', 'A', 'Ley 388 de 1997', 'Ordenamiento territorial', 'Ley', 'vigente',
        NULL, NULL, 'Fuente oficial', 'Documento fuente', '', '', NULL, NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_legal_articles VALUES
        (1, 'ley-388-1997', '1', 'Artículo 61', 'Adquisición de inmuebles',
        'Extracto pertinente para avalúos urbanos.', 'Usar solo cuando la finalidad corresponda.',
        'vigente', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_international_groups VALUES
        ('400', 'Inmuebles', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_international_standards VALUES
        ('ivs-400-real-property', '400', 'IVS 400', 'Real Property Interests', '1,2',
        'Derechos sobre inmuebles.', '2025-01-31', 'vigente', 'IVSC', '', '', NULL, NULL, NULL, 1,
        '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_ifrs_groups VALUES ('G', 'Medición y valor razonable', 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
    $db->exec("INSERT INTO valuation_ifrs_standards VALUES ('ifrs-13-fair-value-measurement', 'G', 'NIIF 13 / IFRS 13', 'Medición del valor razonable', 'A,1,2', 'Valor razonable', 'Marco NIIF para medición.', 'Respalda base/tipo de valor.', 'IFRS Foundation', 'vigente', '', '', NULL, NULL, NULL, 1, '2026-09-15 00:00:00', '2026-09-15 00:00:00')");
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
    $geo = new GeoMasterRepository($db);
    $geo->createDepartment(['code' => '05', 'name' => 'Antioquia', 'active' => 'Si']);
    $departmentId = $geo->departments()[0]['id'];
    $geo->createCity(['department_id' => $departmentId, 'code' => '05001', 'name' => 'Medellín', 'active' => 'Si']);
    $cityId = $geo->cities()[0]['id'];
    $geo->createNeighborhood(['city_id' => $cityId, 'locality_name' => 'Zona urbana',
        'name' => 'El Poblado', 'commune_ucg' => 'Comuna 14', 'zone_sector' => 'Mixto',
        'active' => 'Si', 'notes' => '']);
    $neighborhood = $geo->neighborhoods()[0];
    expect($neighborhood['locality_name'] === 'Zona urbana'
        && $neighborhood['commune_ucg'] === 'Comuna 14', 'maestro barrio trae localidad y comuna');
    $subjectRepo = new AppraisalSubjectRepository($db);
    $subjectRepo->save(str_repeat('a', 32), 1, ['neighborhood_id' => $neighborhood['id'],
        'point_reference' => 'Zona residencial consolidada', 'horizontal_property' => 'no',
        'centrality' => 'alta', 'current_use' => 'Residencial']);
    $subject = $subjectRepo->find(str_repeat('a', 32), 1);
    expect($subject['neighborhood_name'] === 'El Poblado' && $subject['locality_name'] === 'Zona urbana'
        && $subject['commune_ucg'] === 'Comuna 14', 'ficha sujeto deriva ubicacion desde barrio');
    $seedStyleNeighborhoodId = 'geo-neigh-crespo-test-00000000';
    $db->prepare('INSERT INTO master_neighborhoods
        (id, city_id, locality_id, name, commune_ucg, zone_sector, active, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$seedStyleNeighborhoodId, $cityId, null, 'Crespo', 'UCG 1',
            'Residencial y servicios aeroportuarios', 'Si', '', date('c'), date('c')]);
    $resolveNeighborhood = static fn (array $input): string =>
        \App\Services\GeoNeighborhoodResolver::id($input, $geo->neighborhoods());
    expect($resolveNeighborhood(['neighborhood_id' => $seedStyleNeighborhoodId]) === $seedStyleNeighborhoodId,
        'sector acepta id barrial semilla no hexadecimal');
    expect($resolveNeighborhood(['neighborhood_query' => 'Crespo']) === $seedStyleNeighborhoodId,
        'sector resuelve barrio por texto cuando la tarjeta no envia id');
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
    $storedLegalAgain = $legal->find((string) $storedLegal['slug']);
    expect($storedLegalAgain['has_file'] === true && $legal->storageReport()['marked_missing'] === 0,
        'documento juridico conserva respaldo interno sin archivo fisico');
    $db->prepare('UPDATE valuation_legal_documents SET pdf_blob = NULL WHERE slug = ?')->execute([$storedLegal['slug']]);
    expect($legal->storageReport()['marked_missing'] === 1, 'diagnostico juridico detecta PDF faltante sin respaldo');
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
    expect($international->find('ivs-400-real-property')['has_file']
        && $international->storageReport()['marked_missing'] === 0, 'IVS conserva PDF desde respaldo interno');
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
    expect($ifrs->find('ifrs-13-fair-value-measurement')['has_file']
        && $ifrs->storageReport()['marked_missing'] === 0, 'NIIF conserva PDF desde respaldo interno');
    rmdir($ifrsDir);
    putenv('IFRS_STORAGE_DIR');
    $igac = new IgacTypologyRepository();
    expect($igac->stats()['total'] === 202, 'catalogo IGAC contiene 202 tipologias');
    expect(count($igac->byCategory('RESIDENCIALES')) === 23, 'tipologias IGAC agrupadas por categoria');
    expect(count($igac->optionsByCategory()['ANEXOS']) === 136, 'selector IGAC filtra tipologias por categoria');
    expect(($igac->optionsByCategory()['RESIDENCIALES'][0]['image'] ?? '') !== '', 'selector IGAC incluye imagen de referencia');
    $unitId = str_repeat('b', 32);
    $_POST = ['units' => [$unitId => ['label' => 'Unidad 1', 'default_label' => 'Unidad 1']]];
    expectStatus(422, fn () => AppraisalChapterZeroInput::unitData([], []), 'nombre generico de unidad rechazado');
    $_POST = ['units' => [$unitId => ['label' => 'Casa principal', 'default_label' => 'Unidad 1']]];
    expect(AppraisalChapterZeroInput::unitData([], [])[0]['label'] === 'Casa principal', 'nombre propio de unidad aceptado');
    $_POST = ['unit_surfaces' => [$unitId => ['area_land_m2' => '123,45',
        'area_built_m2' => '85', 'area_adopted_m2' => '120,50', 'boundary_front' => 'Calle principal']]];
    $surfaceRows = AppraisalChapterZeroInput::unitSurfaceData();
    expect($surfaceRows[0]['area_land_m2'] === '123.45'
        && $surfaceRows[0]['area_built_m2'] === '85.00', 'superficies por unidad normalizadas');
    expect($surfaceRows[0]['area_adopted_m2'] === '120.50'
        && $surfaceRows[0]['boundary_front'] === 'Calle principal', 'superficie adoptada y linderos por unidad');
    $_POST = ['unit_surfaces' => [$unitId => ['area_land_m2' => '-1']]];
    expectStatus(422, fn () => AppraisalChapterZeroInput::unitSurfaceData(), 'superficie negativa rechazada');
    $_POST = ['unit_constructions' => [$unitId => ['construction_type' => 'casa',
        'built_area_adopted_m2' => '85,25', 'construction_year' => '2010',
        'conservation' => ['estructura' => 'B'], 'services' => ['energia' => 'si'],
        'specifics' => ['estructura' => 'Concreto']]]];
    $constructionRows = AppraisalChapterZeroInput::unitConstructionData();
    expect($constructionRows[0]['built_area_adopted_m2'] === '85.25'
        && str_contains($constructionRows[0]['construction_conservation_json'], 'estructura'), 'construccion por unidad normalizada');
    $_POST = ['unit_attributes' => [$unitId => ['items' => ['esquina' => ['value' => 'esquinero',
        'state' => 'bueno', 'impact' => 'positivo_medio', 'evidence' => 'visita',
        'notes' => 'Frente comercial observado'], 'desconocido' => ['value' => 'x']],
        'report_text' => 'Unidad con condición esquinera verificable.']]];
    $attributeRows = AppraisalAttributeInput::unitAttributeData();
    expect(str_contains($attributeRows[0]['special_attributes_json'], 'esquinero')
        && !str_contains($attributeRows[0]['special_attributes_json'], 'desconocido'), 'atributos especiales normalizados');
    $sectorData = AppraisalSectorInput::data(['sector_name' => ' Bruselas ampliado ',
        'services_status' => 'completa', 'connectivity' => 'invalida',
        'sector_report_text' => str_repeat('x', 2500)]);
    expect($sectorData['sector_name'] === 'Bruselas ampliado'
        && $sectorData['services_status'] === 'completa'
        && $sectorData['connectivity'] === ''
        && mb_strlen($sectorData['sector_report_text']) === 2400, 'sector normalizado');
    $advancedSectorData = AppraisalSectorSectionInput::data(['sector_sections' => [
        '03' => ['acueducto' => 'SI', 'internet_operadores' => ['Claro', 'Desconocido']],
        '06' => ['corredor_actividad' => 'PARCIAL', 'vias_detalle' => str_repeat('x', 4100)],
    ]]);
    expect($advancedSectorData['03']['acueducto'] === 'SI'
        && $advancedSectorData['03']['internet_operadores'] === ['Claro']
        && mb_strlen($advancedSectorData['06']['vias_detalle']) === 4000,
        'sector avanzado normaliza secciones heredadas');
    $prefill = AppraisalSectorPrefill::fromSubject(['neighborhood_name' => 'Bruselas',
        'locality_name' => 'Histórica', 'commune_ucg' => 'UCG 1', 'city_name' => 'Cartagena',
        'water_service' => 'si', 'energy_service' => 'si', 'sewer_service' => 'si',
        'internet_service' => 'no_verificado', 'road_condition' => 'via_principal',
        'transport_connectivity' => 'alta', 'current_use' => 'mixto', 'centrality' => 'alta',
        'stratum' => '4']);
    expect($prefill['sector_name'] === 'Bruselas'
        && $prefill['services_status'] === 'completa'
        && $prefill['road_hierarchy'] === 'arterial'
        && str_contains($prefill['sector_report_text'], 'Cartagena'), 'sector precargado desde sujeto');
    $advancedPrefill = AppraisalSectorAdvancedPrefill::sections(['neighborhood_name' => 'Crespo',
        'zone_sector' => 'Residencial y servicios aeroportuarios', 'city_name' => 'Cartagena'], $prefill);
    expect($advancedPrefill['01']['microsector'] === 'Residencial y servicios aeroportuarios'
        && $advancedPrefill['01']['pais'] === 'Colombia'
        && $advancedPrefill['03']['energia_detalle'] !== ''
        && str_contains($advancedPrefill['02']['imagen_satelital_url'], 'maps/search'),
        'sector avanzado hereda datos base del barrio');
    $midas = AppraisalMidasReview::suggestions(['neighborhood_name' => 'Crespo']);
    $mergedMidas = AppraisalMidasReview::mergeEmpty(['05' => ['norma_base' => 'Manual vigente']], $midas);
    expect(($midas['01']['fuente_base_delimitacion'] ?? '') !== ''
        && ($midas['01']['area_hectareas'] ?? '') === '141,70'
        && !isset($midas['03']['energia_detalle'])
        && $mergedMidas['05']['norma_base'] === 'Manual vigente'
        && str_contains((string) $mergedMidas['05']['midas_lectura_manual'], 'Barrio Crespo'),
        'revision MIDAS conserva manual y propone datos');
    $castilloMidas = AppraisalMidasReview::suggestions(['neighborhood_name' => 'Castillogrande',
        'zone_sector' => 'Residencial de alta densidad']);
    expect(($castilloMidas['01']['area_hectareas'] ?? '') === '41,96'
        && ($castilloMidas['01']['perimetro_metros'] ?? '') === '4.358,82'
        && ($castilloMidas['01']['comuna'] ?? '') === 'UCG 1'
        && !isset($castilloMidas['01']['latitud_centro'])
        && !isset($castilloMidas['07']['equipamientos_seleccionados'])
        && str_contains((string) $castilloMidas['05']['midas_lectura_manual'], 'Castillogrande'),
        'revision MIDAS incorpora perfil verificado de Castillogrande');
    expect(isset(AppraisalMidasReview::pending($castilloMidas)['07']['equipamientos_seleccionados'])
        && isset(AppraisalMidasReview::pending($castilloMidas)['11']['detalle_paraderos_transporte']),
        'revision MIDAS no inventa capas no leidas de Castillogrande');
    $genericMidas = AppraisalMidasReview::suggestions(['neighborhood_name' => 'Barrio por verificar']);
    expect((AppraisalMidasReview::stats($genericMidas)['pending'] ?? 0) > 0
        && isset(AppraisalMidasReview::pending($genericMidas)['01']['area_hectareas']),
        'revision MIDAS deja pendientes los datos no cargados automaticamente');
    $midasPlan = MidasLayerPlan::components();
    expect(MidasLayerPlan::missingModuleFields() === []
        && isset($midasPlan['servicios'], $midasPlan['movilidad'], $midasPlan['equipamientos']),
        'plan MIDAS por capas apunta solo a campos existentes');
    $castilloPending = AppraisalMidasReview::pending($castilloMidas);
    expect(isset($castilloPending['03']['aseo_prestadores'])
        && isset($castilloPending['07']['amoblamiento_seleccionado'])
        && isset($castilloPending['13']['externalidades_negativas']),
        'plan MIDAS exige servicios equipamientos y riesgos sin inventar resultados');
    $wfsLayers = MidasWfsLayerCatalog::layers();
    expect(MidasWfsLayerCatalog::missingModuleFields() === []
        && isset($wfsLayers['barrios'], $wfsLayers['paraderos'], $wfsLayers['aseo']),
        'catalogo WFS MIDAS apunta solo a campos existentes');
    $paraderosUrl = MidasWfsLayerCatalog::url('paraderos');
    expect(str_contains($paraderosUrl, 'map=transcaribe')
        && str_contains($paraderosUrl, 'TYPENAME=Transcaribe_Paraderos'),
        'catalogo WFS construye URL de capa tecnica');
    expect(str_contains(MidasWfsLayerCatalog::url('paraderos', 'application/json', [-75.6, 10.3, -75.5, 10.4]),
        'BBOX=-75.6,10.3,-75.5,10.4,EPSG%3A4326') || str_contains(MidasWfsLayerCatalog::url('paraderos',
        'application/json', [-75.6, 10.3, -75.5, 10.4]), 'BBOX=-75.6,10.3,-75.5,10.4,EPSG:4326'),
        'catalogo WFS permite filtrar por caja del barrio');
    $wfsFixture = ['features' => [[
        'properties' => ['barrio' => 'Castillogrande', 'localidad' => 'Histórica y del Caribe Norte',
            'ucg' => 'UCG 1', 'fuente' => 'Decreto 0977 de 2001 (POT) - Acuerdo 006 de 2003',
            'area_ha' => '41.96', 'perimetro_m' => '4358.82'],
        'geometry' => ['type' => 'Polygon', 'coordinates' => [[[-75.6, 10.3], [-75.5, 10.3],
            [-75.5, 10.4], [-75.6, 10.4], [-75.6, 10.3]]]],
    ]]];
    $wfsParsed = MidasWfsSearch::fromFeatureCollection($wfsFixture, 'Castillogrande');
    expect(($wfsParsed['01']['area_hectareas'] ?? '') === '41,96'
        && ($wfsParsed['01']['perimetro_metros'] ?? '') === '4.358,82'
        && ($wfsParsed['05']['norma_base'] ?? '') !== '',
        'busqueda WFS interpreta ficha territorial de MIDAS');
    $inside = ['geometry' => ['type' => 'Point', 'coordinates' => [-75.55, 10.35]]];
    $outside = ['geometry' => ['type' => 'Point', 'coordinates' => [-75.7, 10.45]]];
    expect(MidasGeometry::intersectsFeature($inside, $wfsFixture['features'][0]) === true
        && MidasGeometry::intersectsFeature($outside, $wfsFixture['features'][0]) === false,
        'geometria MIDAS distingue puntos dentro y fuera del barrio');
    $layerParsed = MidasWfsLayerAnalyzer::fromCollections([
        'barrios' => $wfsFixture,
        'paraderos' => ['features' => [$inside + ['properties' => ['nombre' => 'Paradero Bahía']], $outside]],
        'rutas' => ['features' => [[
            'properties' => ['ruta' => 'Ruta C001'],
            'geometry' => ['type' => 'LineString', 'coordinates' => [[-75.59, 10.31], [-75.51, 10.39]]],
        ]]],
        'aseo' => ['features' => [[
            'properties' => ['empresa' => 'Pacaribe'],
            'geometry' => ['type' => 'Polygon', 'coordinates' => [[[-75.58, 10.32], [-75.52, 10.32],
                [-75.52, 10.38], [-75.58, 10.38], [-75.58, 10.32]]]],
        ]]],
        'educacion' => ['features' => [[
            'properties' => ['nombre' => 'Colegio del Sector'],
            'geometry' => ['type' => 'Point', 'coordinates' => [-75.54, 10.36]],
        ]]],
    ], 'Castillogrande');
    expect(($layerParsed['03']['aseo_prestadores'][0] ?? '') === 'Pacaribe'
        && str_contains((string) ($layerParsed['11']['detalle_paraderos_transporte'] ?? ''), '(1)')
        && in_array('Educativo', $layerParsed['07']['equipamientos_seleccionados'] ?? [], true),
        'analizador WFS cruza capas de servicios movilidad y equipamientos');
    expect(count(AppraisalSectorFieldGuidance::legend()) === 4
        && AppraisalSectorFieldGuidance::field('midas_lectura_manual')['mode'] === 'oficial'
        && AppraisalSectorFieldGuidance::field('observacion_localizacion')['mode'] === 'sugerido',
        'guia visual sectorial clasifica campos');
    $splitReview = AppraisalSectorFieldGuidance::splitReview('Dato real. Para el informe, confirma en visita.');
    expect($splitReview[0] === 'Dato real.' && str_starts_with($splitReview[1], 'Para el informe'),
        'guia visual separa dato base de alerta');
    $legalText = "Nro Matrícula: 060-123456\nDepartamento: Bolívar Municipio: Cartagena de Indias\n"
        . "Dirección Actual del Inmueble: Calle 1 No 2-3\nReferencia Catastral: 130010101000000000001000000000\n"
        . "Estado del Folio: ACTIVO\nCertificado generado con el Pin No: ABCD1234\n"
        . "ANOTACION Nro 1 Fecha: 01/01/2020 Doc: ESCRITURA 123 Valor Acto: \$1000000 "
        . "Especificación: COMPRAVENTA.\nANOTACIÓN: Nro 2 Fecha: 02/02/2021 Doc: ESCRITURA 456 "
        . "Valor Acto: \$500000 Especificación: HIPOTECA a favor de Banco.\nANOTACION: Nro 3 "
        . "Fecha: 03/03/2022 Doc: OFICIO 789 Especificación: EMBARGO.";
    $legalParsed = (new LegalCertificateParser())->parse($legalText, 'certificado.txt');
    expect(($legalParsed['data']['matricula_inmobiliaria'] ?? '') === '060-123456'
        && ($legalParsed['data']['municipio'] ?? '') === 'Cartagena de Indias'
        && ($legalParsed['data']['pin'] ?? '') === 'ABCD1234'
        && count($legalParsed['annotations']) === 3
        && ($legalParsed['annotations'][1]['categoria'] ?? '') === 'gravamen'
        && ($legalParsed['annotations'][2]['categoria'] ?? '') === 'medida_cautelar'
        && count($legalParsed['alerts']) >= 2,
        'parser juridico extrae y clasifica certificado');
    expect(str_contains((string) ($legalParsed['data']['reporte_conclusion_entregable'] ?? ''), 'no saneada')
        && str_contains((string) ($legalParsed['data']['reporte_profesional_entregable'] ?? ''), 'No se recomienda'),
        'parser juridico emite diagnostico preliminar para cargas criticas');
    $pairedLegalText = "Nro Matrícula: 060-999999\nDepartamento: BOLIVAR Municipio: CARTAGENA DE INDIAS\n"
        . "Estado del Folio: ACTIVO\nANOTACION Nro 001 Fecha: 01/01/2020 Doc: ESCRITURA 111 "
        . "Especificación: GRAVAMEN: 210 HIPOTECA A FAVOR DE BANCO.\n"
        . "ANOTACION Nro 002 Fecha: 01/01/2022 Doc: ESCRITURA 222 "
        . "Especificación: CANCELACION: 650 CANCELACION HIPOTECA Se cancela anotacion No: 001.";
    $pairedParsed = (new LegalCertificateParser())->parse($pairedLegalText, 'certificado-pareado.txt');
    $pairedConclusion = mb_strtolower((string) ($pairedParsed['data']['reporte_conclusion_entregable'] ?? ''));
    $pairedAlerts = mb_strtolower(implode(' ', $pairedParsed['alerts']));
    expect(($pairedParsed['annotations'][0]['estado_juridico'] ?? '') === 'solucionada'
        && ($pairedParsed['annotations'][0]['cancelada_por'] ?? '') === '002'
        && ($pairedParsed['annotations'][1]['cancelacion_de'] ?? '') === '001'
        && !str_contains($pairedAlerts, 'anotación 001')
        && str_contains($pairedConclusion, 'se cancela con la anotación 002')
        && !str_contains($pairedConclusion, 'no saneada'),
        'parser juridico parea afectaciones canceladas y evita falsas alarmas');
    $radicadoText = "Nro Matrícula: 060-999999\nEstado del Folio: ACTIVO\n"
        . "ANOTACION Nro 032 Fecha: 01/10/2021 Doc: OFICIO 638 JUZGADO OCTAVO CIVIL "
        . "Especificación: MEDIDA CAUTELAR: 0492 DEMANDA EN PROCESO VERBAL RADICADO 1300131030082021 0039 00.\n"
        . "ANOTACION Nro 036 Fecha: 15/12/2023 Doc: OFICIO 900 JUZGADO OCTAVO CIVIL "
        . "Especificación: CANCELACION: 0841 CANCELACION PROVIDENCIA JUDICIAL RADICADO 1300131030082021-0039-00.";
    $radicadoParsed = (new LegalCertificateParser())->parse($radicadoText, 'certificado-radicado.txt');
    expect(($radicadoParsed['annotations'][0]['estado_juridico'] ?? '') === 'solucionada'
        && ($radicadoParsed['annotations'][0]['cancelada_por'] ?? '') === '036'
        && !str_contains(mb_strtolower(implode(' ', $radicadoParsed['alerts'])), 'anotación 032'),
        'parser juridico cierra medidas cautelares por radicado compartido');
    $servitudeText = "Nro Matrícula: 060-999999\nEstado del Folio: ACTIVO\n"
        . "ANOTACION Nro 013 Fecha: 28/02/2002 Doc: ESCRITURA 882 "
        . "Especificación: LIMITACION AL DOMINIO: 0334 SERVIDUMBRE DE ACUEDUCTO ACTIVA PREDIO SIRVIENTE.";
    $servitudeParsed = (new LegalCertificateParser())->parse($servitudeText, 'certificado-servidumbre.txt');
    $servitudeReport = (string) ($servitudeParsed['data']['reporte_afectaciones'] ?? '') . ' '
        . (string) ($servitudeParsed['data']['reporte_profesional_entregable'] ?? '');
    expect(($servitudeParsed['annotations'][0]['descripcion_acto'] ?? '') === 'Servidumbre de acueducto'
        && str_contains($servitudeReport, 'franja afectada')
        && str_contains($servitudeReport, 'restricciones constructivas'),
        'parser juridico incorpora observacion de servidumbre de acueducto al informe');
    $trafficCounts = AppraisalLegalView::trafficCounts([
        ['estado_juridico' => 'vigente', 'requiere_revision' => 'Sí', 'categoria' => 'gravamen'],
        ['estado_juridico' => 'vigente', 'requiere_revision' => 'Sí', 'categoria' => 'propiedad_horizontal'],
        ['estado_juridico' => 'solucionada', 'requiere_revision' => 'No', 'categoria' => 'gravamen', 'cancelada_por' => '002'],
    ]);
    expect($trafficCounts['Rojo'] === 1 && $trafficCounts['Amarillo'] === 1 && $trafficCounts['Verde'] === 1,
        'vista juridica clasifica semaforo rojo amarillo y verde');
    $cleanedParty = AppraisalLegalView::enrich(['personaDe' => 'DTO. ADMINISTRATIVO DE VALORIZACION DISTRITAL '
        . 'OFICINA DE REGISTRO DE INSTRUMENTOS PUBLICOS DE CARTAGENA ORIP '
        . 'OFICINA DE REGISTRO DE INSTRUMENTOS PUBLICOS DE CARTAGENA ORIP'])['personaDe'] ?? '';
    expect($cleanedParty === 'DTO. ADMINISTRATIVO DE VALORIZACION DISTRITAL',
        'vista juridica limpia intervinientes repetidos por OCR');
    $legalMerged = AppraisalLegalInput::mergeEmpty(['matricula_inmobiliaria' => 'manual'], $legalParsed['data']);
    expect($legalMerged['matricula_inmobiliaria'] === 'manual'
        && ($legalMerged['codigo_catastral_actual'] ?? '') !== '', 'juridico conserva dato manual y llena vacios');
    $ctlText = "OFICINA DE REGISTRO DE INSTRUMENTOS PUBLICOS DE CARTAGENA\n"
        . "Nro Matrícula: 060-179788\nCírculo Registral: CARTAGENA\nDepartamento: BOLIVAR\n"
        . "Municipio: CARTAGENA DE INDIAS\nFecha de Apertura: 30-11-1999\nEstado del Folio: ACTIVO\n"
        . "Turno: 2026-123456 PIN: 7A8B9C\nReferencia Catastral: 130010109001000\n"
        . "Dirección Actual del Inmueble: MANZANA 10 LOTE 10-098 CASTILLOGRANDE\n"
        . "Descripción Cabida y Linderos: Contenidos en ESCRITURA Nro 3069 de fecha 30-11-1999 "
        . "en NOTARIA 2 de CARTAGENA con area de 855.00 M2. AREA Y COEFICIENTE AREA - HECTAREAS: "
        . "METROS: CENTIMETROS: AREA PRIVADA - METROS: CENTIMETROS: AREA CONSTRUIDA - METROS: CENTIMETROS.\n"
        . "ANOTACION Nro 1 Fecha: 30-11-1999 Doc: ESCRITURA 3069 NOTARIA 2 DE CARTAGENA "
        . "Valor Acto: $85.500.000 Especificacion: COMPRAVENTA "
        . "PERSONAS QUE INTERVIENEN EN EL ACTO DE: PROMOTORA TERRANOVA S.A. A: CARMEN CAPELLA DE ESCOBAR\n"
        . "ANOTACION Nro 2 Fecha: 20-01-2001 Doc: ESCRITURA 120 Valor Acto: $0 "
        . "Especificacion: REGLAMENTO DE PROPIEDAD HORIZONTAL COEFICIENTE 1.25%";
    $ctlParsed = (new LegalCertificateParser())->parse($ctlText, 'CTL179788.pdf');
    $ctlFound = count(array_filter($ctlParsed['data'], static fn ($value): bool => trim((string) $value) !== ''));
    expect(($ctlParsed['data']['matricula_inmobiliaria'] ?? '') === '060-179788'
        && ($ctlParsed['data']['circulo_registral'] ?? '') === 'CARTAGENA'
        && ($ctlParsed['data']['codigo_catastral_actual'] ?? '') === '130010109001000'
        && str_contains((string) ($ctlParsed['data']['cabida_linderos'] ?? ''), '855.00 M2')
        && ($ctlParsed['data']['titular_actual'] ?? '') === 'CARMEN CAPELLA DE ESCOBAR'
        && ($ctlParsed['data']['reglamento_ph'] ?? '') === 'Sí'
        && ($ctlParsed['data']['check_tradicion'] ?? '') === 'Sí'
        && ($ctlParsed['data']['check_propiedad_horizontal'] ?? '') === 'Sí'
        && ($ctlParsed['data']['semaforo_manual'] ?? '') === 'Atención'
        && str_contains((string) ($ctlParsed['data']['reporte_profesional_entregable'] ?? ''), '060-179788')
        && str_contains((string) ($ctlParsed['data']['reporte_conclusion_entregable'] ?? ''), 'comercializable con condiciones')
        && $ctlFound >= 15,
        'parser juridico lee certificado CTL con campos registrales fisicos y titularidad');
    $ctlOcrText = "Certificado generado con el Pin No: 230912151482354135\n"
        . "Nro Matr?cula: 060-187254 Pagina 1 TURNO: 2023-060-1-132812\n"
        . "CIRCULO REGISTRAL: 060 - CARTAGENA DEPTO: BOLIVAR MUNICIPIO: CARTAGENA DE INDIAS VEREDA: CARTAGENA\n"
        . "FECHA APERTURA: 13-02-2002 RADICACI?N: 2002-800\n"
        . "ESTADO DEL FOLIO:\nACTIVO\nDESCRIPCION: CABIDA Y LINDEROS\n"
        . "CABIDA Y LINDEROS Contenidos en ESCRITURA Nro 3069 de fecha 30-11-1999 en NOTARIA 2 de CARTAGENA "
        . "con area de 855.00 M2.\n"
        . "ANOTACION: Nro 001 Fecha: 07-04-1997 Radicaci?n: 1997-6570 Doc: ESCRITURA 3663 DEL 30-12-1996 "
        . "VALOR ACTO: $ ESPECIFICACION: GRAVAMEN: 210 HIPOTECA PERSONAS QUE INTERVIENEN EN EL ACTO DE: FIDUCOLOMBIA "
        . "A: GRANAHORRAR BANCO COMERCIAL\n"
        . "ANOTACI?N Nro: 002 Fecha: 30-11-1999 Doc: ESCRITURA 3069 VALOR ACTO: $85500000 "
        . "ESPECIFICACION: COMPRAVENTA PERSONAS QUE INTERVIENEN EN EL ACTO DE: PROMOTORA TERRANOVA S.A. "
        . "A: CARMEN CAPELLA DE ESCOBAR";
    $ctlOcrParsed = (new LegalCertificateParser())->parse($ctlOcrText, 'certificado187254.pdf');
    expect(($ctlOcrParsed['data']['matricula_inmobiliaria'] ?? '') === '060-187254'
        && ($ctlOcrParsed['data']['circulo_registral'] ?? '') === '060 - CARTAGENA'
        && ($ctlOcrParsed['data']['departamento'] ?? '') === 'BOLIVAR'
        && ($ctlOcrParsed['data']['municipio'] ?? '') === 'CARTAGENA DE INDIAS'
        && ($ctlOcrParsed['data']['vereda'] ?? '') === 'CARTAGENA'
        && ($ctlOcrParsed['data']['estado_folio'] ?? '') === 'ACTIVO'
        && count($ctlOcrParsed['annotations']) === 2
        && ($ctlOcrParsed['annotations'][0]['categoria'] ?? '') === 'gravamen'
        && ($ctlOcrParsed['annotations'][1]['categoria'] ?? '') === 'tradicion'
        && ($ctlOcrParsed['data']['check_gravamenes'] ?? '') === 'Sí'
        && ($ctlOcrParsed['data']['check_tradicion'] ?? '') === 'Sí',
        'parser juridico tolera etiquetas CTL con OCR parcial y caracteres danados');
    $ctlLongOcrText = "Certificado generado con el Pin No: 230912151482354135\n"
        . "Nro Matr?cula: 060-187254 Pagina 1 TURNO: 2023-060-1-132812\n"
        . "CIRCULO REGISTRAL: 060 - CARTAGENA DEPTO: BOLIVAR MUNICIPIO: CARTAGENA DE INDIAS VEREDA: CARTAGENA\n"
        . "FECHA APERTURA: 13-02-2002 RADICACI?N: 2002-800 CON: ESCRITURA DE: 16-01-2002\n"
        . "CODIGO CATASTRAL:\nCOD CATASTRAL ANT: SIN INFORMACION\nNUPRE:\nESTADO DEL FOLIO:\nACTIVO\n"
        . "DESCRIPCION: CABIDA Y LINDEROS\nContenidos en ESCRITURA Nro 2593 de fecha 30-11-1999 "
        . "en NOTARIA 2 de CARTAGENA GARAJE 24 con area de 13.34M2 con coeficiente de 0.1010%\n"
        . "Tipo Predio: URBANO\nDIRECCION DEL INMUEBLE\n"
        . "1 KR 13 B # 26 - 78 GRAJE 24 EDIF 19 DEL PROYECTO INTEGRADO CHAMBACU R P H\n"
        . "MATRICULA ABIERTA CON BASE EN LA 060 - 187193\nNRO TOTAL DE ANOTACIONES: *27*\n"
        . "ANOTACION: Nro 001 Fecha: 07-04-1997 Radicaci?n: 1997-6570 Doc: ESCRITURA 3663 DEL 30-12-1996 "
        . "VALOR ACTO: $ ESPECIFICACION: GRAVAMEN: 210 HIPOTECA PERSONAS QUE INTERVIENEN EN EL ACTO DE: FIDUCOLOMBIA "
        . "A: GRANAHORRAR BANCO COMERCIAL\n"
        . "ANOTACION: Nro 002 Fecha: 30-11-1999 Doc: ESCRITURA 3069 VALOR ACTO: $85500000 "
        . "ESPECIFICACION: COMPRAVENTA PERSONAS QUE INTERVIENEN EN EL ACTO DE: PROMOTORA TERRANOVA S.A. "
        . "A: CARMEN CAPELLA DE ESCOBAR\n"
        . "ANOTACION: Nro 003 Fecha: 20-01-2001 Doc: ESCRITURA 120 VALOR ACTO: $0 "
        . "ESPECIFICACION: LIMITACION AL DOMINIO: 360 REGLAMENTO PROPIEDAD HORIZONTAL COEFICIENTE 0.1010%\n";
    $ctlLongParsed = (new LegalCertificateParser())->parse($ctlLongOcrText, 'certificado187254.pdf');
    $ctlLongFound = count(array_filter($ctlLongParsed['data'], static fn ($value): bool => trim((string) $value) !== ''));
    expect(($ctlLongParsed['data']['matricula_inmobiliaria'] ?? '') === '060-187254'
        && ($ctlLongParsed['data']['turno'] ?? '') === '2023-060-1-132812'
        && ($ctlLongParsed['data']['fecha_apertura'] ?? '') === '13-02-2002'
        && ($ctlLongParsed['data']['tipo_predio'] ?? '') === 'URBANO'
        && str_contains((string) ($ctlLongParsed['data']['direccion'] ?? ''), 'KR 13 B')
        && ($ctlLongParsed['data']['area'] ?? '') === '13.34'
        && ($ctlLongParsed['data']['coeficiente'] ?? '') === '0.1010%'
        && ($ctlLongParsed['data']['matricula_matriz'] ?? '') === '060-187193'
        && count($ctlLongParsed['annotations']) === 3
        && ($ctlLongParsed['data']['check_gravamenes'] ?? '') === 'Sí'
        && ($ctlLongParsed['data']['check_propiedad_horizontal'] ?? '') === 'Sí'
        && $ctlLongFound >= 18,
        'parser juridico lee CTL largo con direccion multilinea anotaciones y PH');
    $snrHeaderText = "La validez de este documento podr? verificarse en la p?gina certificados.supernotariado.gov.co\n"
        . "Certificado generado con el Pin No: 230912151482354135\nNro Matr?cula: 060-187254\n"
        . "Pagina 1 TURNO: 2023-060-1-132812\nImpreso el 12 de Septiembre de 2023 a las 11:03:36 AM\n"
        . "CIRCULO REGISTRAL: 060 - CARTAGENA DEPTO: BOLIVAR MUNICIPIO: CARTAGENA DE INDIAS VEREDA: CARTAGENA\n"
        . "FECHA APERTURA: 13-02-2002 RADICACI?N: 2002-800 CON: ESCRITURA DE: 16-01-2002\n"
        . "ESTADO DEL FOLIO:\nACTIVO\nDESCRIPCION: CABIDA Y LINDEROS\n"
        . "Contenidos en ESCRITURA Nro 2593 de fecha 29-12-2001 en NOTARIA 61 de BOGOTA GARAJE 24 con area de 13.34M2 con coeficiente de 0.1010%\n"
        . "DIRECCION DEL INMUEBLE\nTipo Predio: URBANO\n1 KR 13 B # 26 - 78 GRAJE 24 EDIF 19 DEL PROYECTO INTEGRADO CHAMBACU R P H\n"
        . "MATRICULA ABIERTA CON BASE EN LA s SIGUIENTE s En caso de integraci?n y otros\n060 - 187193\n";
    $snrHeaderParsed = (new LegalCertificateParser())->parse($snrHeaderText, 'certificado187254.pdf');
    expect(($snrHeaderParsed['data']['matricula_inmobiliaria'] ?? '') === '060-187254'
        && ($snrHeaderParsed['data']['circulo_registral'] ?? '') === '060 - CARTAGENA'
        && ($snrHeaderParsed['data']['estado_folio'] ?? '') === 'ACTIVO'
        && ($snrHeaderParsed['data']['matricula_matriz'] ?? '') === '060-187193'
        && str_contains((string) ($snrHeaderParsed['data']['direccion'] ?? ''), 'KR 13 B'),
        'parser juridico lee cabecera SNR real con acentos danados');
    $pendingMerged = AppraisalLegalInput::mergeEmpty(
        ['matricula_inmobiliaria' => 'Pendiente de lectura o revisión manual'],
        ['matricula_inmobiliaria' => '060-187254']);
    expect(($pendingMerged['matricula_inmobiliaria'] ?? '') === '060-187254',
        'reanálisis juridico reemplaza marcador pendiente guardado');
    $badCtlPath = tempnam(sys_get_temp_dir(), 'ga_ctl_bad_');
    file_put_contents($badCtlPath, "Nro Matr\xe9cula: 060-187254\nCIRCULO REGISTRAL: 060 - CARTAGENA DEPTO: BOLIVAR MUNICIPIO: CARTAGENA DE INDIAS VEREDA: CARTAGENA\n"
        . "FECHA APERTURA: 13-02-2002\nESTADO DEL FOLIO:\nACTIVO\nDIRECCION DEL INMUEBLE\nTipo Predio: URBANO\n1 KR 13 B # 26 - 78 GARAJE 24\n"
        . "ANOTACION: Nro 001 Fecha: 01-01-2020 Doc: ESCRITURA 1 VALOR ACTO: $0 ESPECIFICACION: HIPOTECA\n"
        . "Se cancela anotacion No: 001\nNRO TOTAL DE ANOTACIONES: *1*\nSALVEDADES: Informacion Anterior o Corregida\nAnotacion Nro: 1\n");
    $badText = (new \App\Services\LegalCertificateTextExtractor())->extract($badCtlPath, 'txt');
    $badParsed = (new LegalCertificateParser())->parse($badText, 'certificado-danado.txt');
    expect(($badParsed['data']['matricula_inmobiliaria'] ?? '') === '060-187254'
        && ($badParsed['data']['vereda'] ?? '') === 'CARTAGENA'
        && ($badParsed['data']['pin'] ?? '') === ''
        && count($badParsed['annotations']) === 1,
        'parser juridico tolera bytes danados y no cuenta salvedades como anotaciones');
    unlink($badCtlPath);
    $legalRepo = new AppraisalLegalRepository($db);
    $legalManual = [];
    foreach (AppraisalLegalCatalog::fieldKeys() as $fieldKey) $legalManual[$fieldKey] = 'Guardado ' . $fieldKey;
    $legalRepo->saveManual(str_repeat('b', 32), 1, $legalManual);
    $storedLegalProfile = $legalRepo->profile(str_repeat('b', 32), 1);
    $allLegalFieldsSaved = true;
    foreach (AppraisalLegalCatalog::fieldKeys() as $fieldKey) {
        $allLegalFieldsSaved = $allLegalFieldsSaved && (($storedLegalProfile['data'][$fieldKey] ?? '') === 'Guardado ' . $fieldKey);
    }
    expect($allLegalFieldsSaved && $storedLegalProfile['status'] === 'Revisado por analista',
        'numeral 4 guarda y recarga todos los campos manuales');
    $legalRepo->mergeAnalysis(str_repeat('b', 32), 1, str_repeat('9', 32),
        ['matricula_inmobiliaria' => 'lectura reemplazo', 'municipio' => 'lectura reemplazo',
            'reporte_conclusion_entregable' => 'Diagnóstico renovado'],
        [['orden' => '1']], ['alerta'], 'texto extraido');
    $storedAfterReanalysis = $legalRepo->profile(str_repeat('b', 32), 1);
    expect(($storedAfterReanalysis['data']['matricula_inmobiliaria'] ?? '') === 'Guardado matricula_inmobiliaria'
        && ($storedAfterReanalysis['data']['municipio'] ?? '') === 'Guardado municipio'
        && ($storedAfterReanalysis['data']['reporte_conclusion_entregable'] ?? '') === 'Diagnóstico renovado'
        && count($storedAfterReanalysis['annotations']) === 1,
        'reanálisis jurídico conserva datos manuales y refresca cierre automático');
    $tmpCtlPng = tempnam(sys_get_temp_dir(), 'ga_ctl_png_');
    file_put_contents($tmpCtlPng, hex2bin('89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c4890000000a49444154789c6360000002000100ffff03000006000557bfabcf0000000049454e44ae426082'));
    $imageInfo = AppraisalLegalCertificateStorage::inspect($tmpCtlPng, 'certificado-foto.png');
    expect($imageInfo['extension'] === 'png' && $imageInfo['mime'] === 'image/png',
        'almacenamiento juridico acepta imagen valida de certificado');
    $imageRepo = new AppraisalLegalRepository($db);
    $imageUpload = (new AppraisalLegalCertificateUploadService())->store(
        uploadFixture('certificado-foto.png', $tmpCtlPng), str_repeat('c', 32), 1, $imageRepo);
    $imageCertificates = $imageRepo->certificates(str_repeat('c', 32), 1);
    expect(($imageUpload['record']['mime_type'] ?? '') === 'image/png'
        && ($imageUpload['parsed']['status'] ?? '') === 'Requiere lectura manual'
        && count($imageCertificates) === 1,
        'certificado juridico en imagen se guarda y queda pendiente si no hay OCR');
    $clientPdf = tempnam(sys_get_temp_dir(), 'ga_ctl_client_');
    file_put_contents($clientPdf, "%PDF-1.4\n%%EOF\n");
    $clientRepo = new AppraisalLegalRepository($db);
    (new AppraisalLegalCertificateUploadService())->store(
        uploadFixture('certificado-cliente.pdf', $clientPdf), str_repeat('d', 32), 1, $clientRepo, $ctlLongOcrText);
    $clientProfile = $clientRepo->profile(str_repeat('d', 32), 1);
    expect(($clientProfile['data']['matricula_inmobiliaria'] ?? '') === '060-187254'
        && ($clientProfile['data']['direccion'] ?? '') !== ''
        && count($clientProfile['annotations'] ?? []) === 3,
        'certificado juridico usa texto extraido en navegador cuando supera lectura servidor');
    $sectorColumnsSql = implode(', ', array_map(static fn (string $key): string => $key . ' TEXT',
        AppraisalSectorCatalog::keys()));
    $db->exec("CREATE TABLE master_sector_profiles (neighborhood_id TEXT PRIMARY KEY,
        source_appraisal_id TEXT, updated_by_owner_id INTEGER, version INTEGER DEFAULT 1,
        $sectorColumnsSql, updated_at TEXT)");
    $db->exec("CREATE TABLE master_sector_profile_sections (id INTEGER PRIMARY KEY,
        neighborhood_id TEXT, section_code TEXT, section_title TEXT, status TEXT, version INTEGER DEFAULT 1,
        source_name TEXT, source_updated_at TEXT, requires_field_validation TEXT, requires_photo_support TEXT,
        content_text TEXT, data_json TEXT, updated_at TEXT, UNIQUE(neighborhood_id, section_code))");
    $db->exec("CREATE TABLE master_sector_section_sources (neighborhood_id TEXT, section_code TEXT,
        source_key TEXT, relation_status TEXT, source_data_at TEXT, updated_at TEXT,
        PRIMARY KEY (neighborhood_id, section_code, source_key))");
    $db->exec("CREATE TABLE appraisal_sector_profile_sections (id INTEGER PRIMARY KEY,
        appraisal_id TEXT, owner_id INTEGER, neighborhood_id TEXT, section_code TEXT,
        section_title TEXT, status TEXT, version INTEGER DEFAULT 1, data_json TEXT,
        content_text TEXT, updated_at TEXT, UNIQUE(appraisal_id, section_code))");
    $db->exec("CREATE TABLE appraisal_sector_midas_files (id TEXT PRIMARY KEY, appraisal_id TEXT,
        owner_id INTEGER, neighborhood_id TEXT, layer_group TEXT, source_filename TEXT,
        storage_filename TEXT, mime_type TEXT, file_size_bytes INTEGER, notes TEXT,
        file_blob BLOB, created_at TEXT)");
    $neighborhoodSectors = new NeighborhoodSectorRepository($db);
    $sectorBank = new SectorBankRepository($db);
    $neighborhoodId = str_repeat('e', 32);
    $masterSector = AppraisalSectorInput::data(['sector_name' => 'Bruselas',
        'services_status' => 'completa', 'sector_report_text' => 'Ficha reutilizable del barrio.']);
    $neighborhoodSectors->save($neighborhoodId, 1, str_repeat('f', 32), $masterSector);
    $neighborhoodSectors->save($neighborhoodId, 1, str_repeat('f', 32), $masterSector);
    $storedSector = $neighborhoodSectors->find($neighborhoodId);
    expect($storedSector && $storedSector['sector_name'] === 'Bruselas'
        && $storedSector['sector_report_text'] === 'Ficha reutilizable del barrio.', 'banco barrial reutilizable');
    $db->prepare('INSERT INTO master_sector_profile_sections
        (neighborhood_id, section_code, section_title, status, source_name, requires_field_validation,
        requires_photo_support, content_text, data_json, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$neighborhoodId, '01', 'Identificación', 'Generada', 'Datos Abiertos Cartagena',
            'SI', 'NO', 'Base creada', '{}', date('c')]);
    $db->prepare('INSERT INTO master_sector_profile_sections
        (neighborhood_id, section_code, section_title, status, source_name, requires_field_validation,
        requires_photo_support, content_text, data_json, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$neighborhoodId, '02', 'Soporte cartográfico', 'Validada', 'MIDAS',
            'SI', 'SI', 'Base validada', '{}', date('c')]);
    $sectorSummary = $sectorBank->summary($neighborhoodId);
    expect($sectorSummary['total'] === 2 && $sectorSummary['ready'] === 1,
        'semaforo sectorial solo cuenta secciones validadas');
    $advancedRepo = new AppraisalSectorSectionRepository($db);
    $advancedRepo->saveAll(str_repeat('f', 32), 1, $neighborhoodId, $advancedSectorData);
    $storedAdvanced = $advancedRepo->sections(str_repeat('f', 32), 1);
    expect(isset($storedAdvanced['03']) && str_contains((string) $storedAdvanced['03']['data_json'], 'Claro'),
        'ficha sectorial avanzada queda dentro del avaluo');
    $sectorBank->saveAdvancedSections($neighborhoodId, $advancedSectorData);
    $bankAdvanced = $sectorBank->sections($neighborhoodId);
    expect(count($bankAdvanced) === 16, 'banco barrial recibe secciones avanzadas del avaluo');
    $profileVersion = new ReflectionMethod(SectorBankRepository::class, 'profileVersion');
    $profileVersion->setAccessible(true);
    expect($profileVersion->invoke($sectorBank, $neighborhoodId) === 2,
        'instantanea sectorial toma version del banco');
    expect(count(SectorBankCatalog::sections()) === 16
        && (SectorBankCatalog::sections()['05'][0] ?? '') === 'Normatividad urbanística',
        'banco sectorial conserva secciones avanzadas');
    $midasDir = sys_get_temp_dir() . '/ga_midas_' . bin2hex(random_bytes(4));
    putenv('APPRAISAL_MIDAS_STORAGE_DIR=' . $midasDir);
    $tmpMidasPdf = tempnam(sys_get_temp_dir(), 'ga_midas_pdf_');
    file_put_contents($tmpMidasPdf, "%PDF-1.4\n%midas\n");
    $midasFiles = new AppraisalSectorMidasFileRepository($db);
    $uploadedMidas = (new AppraisalMidasSupportUploadService())->store(
        uploadFixture('pdf_descargas_division_politica_barrios.pdf', $tmpMidasPdf),
        str_repeat('f', 32), 1, $neighborhoodId, $midasFiles, 'Barrios / división política', 'Capa revisada');
    $storedMidasFiles = $midasFiles->forAppraisal(str_repeat('f', 32), 1);
    expect(count($storedMidasFiles) === 1 && $storedMidasFiles[0]['id'] === $uploadedMidas['id']
        && $storedMidasFiles[0]['file_available'] === true, 'soporte MIDAS queda asociado al avaluo');
    unlink(AppraisalSectorMidasFileRepository::path($storedMidasFiles[0]['storage_filename']));
    rmdir($midasDir);
    putenv('APPRAISAL_MIDAS_STORAGE_DIR');
    $photoRecordId = str_repeat('c', 32);
    $photoUnitId = str_repeat('d', 32);
    $photoController = (new ReflectionClass(AppraisalSubjectController::class))->newInstanceWithoutConstructor();
    $safePhotoReturn = new ReflectionMethod(AppraisalSubjectController::class, 'safePhotoReturn');
    $safePhotoReturn->setAccessible(true);
    $_POST = ['return_to' => 'avaluos/' . $photoRecordId . '/bien-sujeto#fotos-general'];
    expect($safePhotoReturn->invoke($photoController, $photoRecordId) === $_POST['return_to'], 'retorno a fotos generales conservado');
    $_POST = ['return_to' => 'avaluos/' . $photoRecordId . '/bien-sujeto#fotos-' . $photoUnitId];
    expect($safePhotoReturn->invoke($photoController, $photoRecordId) === $_POST['return_to'], 'retorno a fotos de unidad conservado');
    $_POST = [];
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
    expect($report['present'] === 1 && $report['marked_missing'] === 0
        && (new ValuationStandardRepository($db))->find('nts-s04-codigo-conducta')['has_file'],
        'norma tecnica conserva PDF desde respaldo interno');
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
