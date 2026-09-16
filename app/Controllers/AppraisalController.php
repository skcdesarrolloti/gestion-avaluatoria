<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSubjectRepository;
use App\Models\AppraiserRepository;
use App\Models\GeoMasterRepository;
use App\Models\IgacTypologyRepository;
use App\Services\AppraisalAttributeInput;
use App\Services\AppraisalChapterZeroInput;
use App\Services\AppraisalPhotoUploadService;
use App\Services\AppraisalValidator;
use App\Support\AppraisalCatalog;
use App\Support\AppraisalSpecialAttributeCatalog;
use App\Support\AppraisalSubjectCatalog;

final class AppraisalController
{
    public function __construct(private AppraisalRepository $appraisals, private array $user,
        private AppraiserRepository $appraisers, private IgacTypologyRepository $typologies,
        private AppraisalSubjectRepository $subjects, private GeoMasterRepository $geo) {}

    public function index(): void
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
        $page = max(1, min(100000, $page));
        $rows = $this->appraisals->recent($this->user['id'], $page);
        view('appraisals/index', ['title' => 'Mis avalúos', 'rows' => array_slice($rows, 0, 20),
            'hasNext' => count($rows) > 20, 'page' => $page]);
    }

    public function create(): never
    {
        $id = $this->appraisals->create($this->user['id']);
        Http::redirect('avaluos/' . $id . '/expediente');
    }

    public function chapterZero(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/chapter-zero', ['title' => 'Expediente valuatorio', 'record' => $record,
            'appraisers' => $this->appraisers->all(),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function subject(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $this->appraisals->ensureUnits($id, $this->user['id'],
            (int) ($record['igac_property_units_count'] ?? 0), (int) ($record['igac_annex_units_count'] ?? 0));
        view('appraisals/subject', ['title' => 'Bien sujeto', 'record' => $record,
            'subject' => $this->subjects->find($id, $this->user['id']),
            'geo' => ['departments' => $this->geo->departments(), 'cities' => $this->geo->cities(),
                'neighborhoods' => $this->geo->neighborhoods()],
            'photos' => $this->appraisals->photos($id, $this->user['id']),
            'units' => $this->appraisals->units($id, $this->user['id']),
            'igacCategories' => $this->typologies->categories(),
            'igacTypologiesByCategory' => $this->typologies->optionsByCategory(),
            'specialAttributeCatalog' => AppraisalSpecialAttributeCatalog::groups(),
            'specialAttributeOptions' => AppraisalSpecialAttributeCatalog::selectOptions(),
            'subjectCatalog' => AppraisalSubjectCatalog::selects(),
            'subjectHelp' => AppraisalSubjectCatalog::helps(),
            'subjectMessage' => Session::pullFlash('subject_message'),
            'subjectError' => Session::pullFlash('subject_error'),
            'photoMessage' => Session::pullFlash('chapter_zero_photo_message'),
            'photoError' => Session::pullFlash('chapter_zero_photo_error'),
            'preclassMessage' => Session::pullFlash('chapter_zero_preclass_message'),
            'preclassError' => Session::pullFlash('chapter_zero_preclass_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function saveSubjectBasic(string $id): never
    {
        $this->saveSubjectData($id, fn () => $this->subjects->save($id, $this->user['id'], $_POST),
            'Ficha básica del sujeto guardada correctamente.', '#ficha-basica');
    }

    public function saveChapterZero(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
            $this->igacCodes(), $this->appraiserIds());
        $this->appraisals->saveChapterZero($id, $this->user['id'], (int) $_POST['version'], $data);
        Http::redirect('avaluos/' . $record['id'] . '/expediente');
    }

    public function saveSubjectUnits(string $id): never { $this->saveUnitsAndRedirect($id, 'avaluos/' . $id . '/bien-sujeto'); }

    public function saveSubjectSurfaces(string $id): never { $this->saveSubjectData($id, fn () => $this->appraisals->saveUnitSurfaces($id, $this->user['id'], AppraisalChapterZeroInput::unitSurfaceData()), 'Datos de superficie guardados correctamente.', '#superficies'); }

    public function saveSubjectConstructions(string $id): never { $this->saveSubjectData($id, fn () => $this->appraisals->saveUnitConstructions($id, $this->user['id'], AppraisalChapterZeroInput::unitConstructionData()), 'Datos de construcción guardados correctamente.', '#construccion'); }

    public function saveSubjectAttributes(string $id): never { $this->saveSubjectData($id, fn () => $this->appraisals->saveUnitAttributes($id, $this->user['id'], AppraisalAttributeInput::unitAttributeData()), 'Atributos especiales guardados correctamente.', '#atributos'); }

    private function saveSubjectData(string $id, callable $save, string $message, string $hash): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $save();
            Session::flash('subject_message', $message);
        } catch (\Throwable $error) {
            Session::flash('subject_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/bien-sujeto' . $hash);
    }

    private function saveUnitsAndRedirect(string $id, string $target): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->saveUnits($id, $this->user['id'],
                AppraisalChapterZeroInput::unitData($this->igacCodes(), $this->typologies->optionsByCategory()));
            Session::flash('chapter_zero_preclass_message', 'Unidades guardadas correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_preclass_error', $error->getMessage());
        }
        Http::redirect($target);
    }

    public function saveSubjectPreclassification(string $id): never { $this->savePreclassificationAndRedirect($id, 'avaluos/' . $id . '/bien-sujeto'); }

    private function savePreclassificationAndRedirect(string $id, string $target): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->savePreclassification($id, $this->user['id'], (int) ($_POST['version'] ?? 0),
                AppraisalChapterZeroInput::preclassificationData($this->igacCodes()));
            Session::flash('chapter_zero_preclass_message', 'Lectura inicial guardada correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_preclass_error', $error->getMessage());
        }
        Http::redirect($target);
    }

    public function uploadSubjectPhotos(string $id): never { $this->uploadPhotosAndRedirect($id, 'avaluos/' . $id . '/bien-sujeto'); }

    private function uploadPhotosAndRedirect(string $id, string $target): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        try {
            $unitId = preg_match('/^[a-f0-9]{32}$/', (string) ($_POST['unit_id'] ?? '')) ? (string) $_POST['unit_id'] : null;
            $count = (new AppraisalPhotoUploadService())->store($_FILES['photos'] ?? [], $record['id'],
                $this->user['id'], $this->appraisals, $unitId);
            Session::flash('chapter_zero_photo_message', $count === 1
                ? 'Foto cargada correctamente.'
                : $count . ' fotos cargadas correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_photo_error', $error->getMessage());
        }
        Http::redirect($target);
    }

    public function photo(string $id, string $photoId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $photo = $this->appraisals->findPhoto($photoId, $this->user['id']);
        $path = AppraisalRepository::photoPath((string) $photo['storage_filename']);
        $blob = $photo['file_blob'] ?? null;
        if (!is_file($path) && !is_string($blob)) throw new HttpException(404, 'No se encontró la foto.');
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: ' . $photo['mime_type']);
        header('Content-Disposition: inline; filename="' . basename((string) $photo['source_filename']) . '"');
        header('X-Content-Type-Options: nosniff');
        if (is_file($path)) {
            header('Content-Length: ' . filesize($path));
            readfile($path);
        } else {
            header('Content-Length: ' . strlen($blob));
            echo $blob;
        }
        exit;
    }

    public function deletePhoto(string $id, string $photoId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $path = $this->appraisals->deletePhoto($id, $photoId, $this->user['id']);
        if ($path && is_file($path)) @unlink($path);
        Session::flash('chapter_zero_photo_message', 'Foto retirada del expediente.');
        Http::redirect($this->safePhotoReturn($id));
    }

    private function safePhotoReturn(string $id): string
    {
        $target = (string) ($_POST['return_to'] ?? '');
        return in_array($target, ['avaluos/' . $id . '/expediente', 'avaluos/' . $id . '/bien-sujeto',
            'avaluos/' . $id . '/bien-sujeto#atributos'], true)
            ? $target
            : 'avaluos/' . $id . '/expediente';
    }

    public function edit(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/edit', ['title' => 'Ficha del avalúo', 'record' => $record,
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function save(string $id): never
    {
        $input = Http::input();
        $data = AppraisalValidator::validate($input);
        $result = $this->appraisals->save($id, $this->user['id'], $input['version'], $data);
        Http::json(['ok' => true] + $result);
    }

    private function appraiserIds(): array { return array_column($this->appraisers->all(), 'id'); }

    private function igacCodes(): array { return array_column($this->typologies->categories(), 'code'); }
}
