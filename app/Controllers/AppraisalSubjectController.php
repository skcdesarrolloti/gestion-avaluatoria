<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, HttpException, Session};
use App\Models\{AppraisalPhRepository, AppraisalRepository, AppraisalSubjectRepository, GeoMasterRepository, IgacTypologyRepository};
use App\Services\{AppraisalAttributeInput, AppraisalChapterZeroInput, AppraisalPhotoUploadService};
use App\Support\{AppraisalCatalog, AppraisalPhCatalog, AppraisalSpecialAttributeCatalog, AppraisalSubjectCatalog};

final class AppraisalSubjectController
{
    public function __construct(
        private AppraisalRepository $appraisals, private array $user, private IgacTypologyRepository $typologies,
        private AppraisalSubjectRepository $subjects, private GeoMasterRepository $geo, private AppraisalPhRepository $ph
    ) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $phSearch = mb_substr(trim((string) ($_GET['copropiedad'] ?? '')), 0, 190);
        $this->appraisals->ensureUnits($id, $this->user['id'],
            (int) ($record['igac_property_units_count'] ?? 0), (int) ($record['igac_annex_units_count'] ?? 0));
        view('appraisals/subject', ['title' => 'Bien sujeto', 'record' => $record,
            'subject' => $subject,
            'geo' => ['departments' => $this->geo->departments(), 'cities' => $this->geo->cities(),
                'neighborhoods' => $this->geo->neighborhoods()],
            'photos' => $this->appraisals->photos($id, $this->user['id']),
            'units' => $this->appraisals->units($id, $this->user['id']),
            'phProfile' => $this->ph->profile($id, $this->user['id']),
            'phDocuments' => $this->ph->documents($id, $this->user['id']),
            'phLegalPrefill' => $this->ph->legalPrefill($id, $this->user['id']),
            'phSearchQuery' => $phSearch,
            'phSearchResults' => $phSearch !== ''
                ? $this->ph->searchByCoproperty($phSearch, $this->user['id'], $id)
                : [],
            'phCatalog' => ['status' => AppraisalPhCatalog::statusOptions(),
                'typologies' => AppraisalPhCatalog::typologies(), 'technical' => AppraisalPhCatalog::technicalGroups(),
                'commonAreas' => AppraisalPhCatalog::commonAreas(), 'documents' => AppraisalPhCatalog::documents(),
                'risks' => AppraisalPhCatalog::risks(), 'photos' => AppraisalPhCatalog::photos()],
            'igacCategories' => $this->typologies->categories(),
            'igacTypologiesByCategory' => $this->typologies->optionsByCategory(),
            'specialAttributeCatalog' => AppraisalSpecialAttributeCatalog::groups((string) ($record['tipo_inmueble'] ?? '')),
            'specialAttributeOptions' => AppraisalSpecialAttributeCatalog::selectOptions(),
            'subjectCatalog' => AppraisalSubjectCatalog::selects(),
            'subjectHelp' => AppraisalSubjectCatalog::helps(),
            'subjectMessage' => Session::pullFlash('subject_message'),
            'subjectError' => Session::pullFlash('subject_error'),
            'phMessage' => Session::pullFlash('ph_message'),
            'phError' => Session::pullFlash('ph_error'),
            'photoMessage' => Session::pullFlash('chapter_zero_photo_message'),
            'photoError' => Session::pullFlash('chapter_zero_photo_error'),
            'preclassMessage' => Session::pullFlash('chapter_zero_preclass_message'),
            'preclassError' => Session::pullFlash('chapter_zero_preclass_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function saveBasic(string $id): never
    { $this->saveSubjectData($id, fn () => $this->subjects->save($id, $this->user['id'], $_POST), 'Ficha básica del sujeto guardada correctamente.', '#ficha-basica'); }

    public function autosaveBasic(string $id): never
    { $this->appraisals->find($id, $this->user['id']); $this->subjects->save($id, $this->user['id'], $_POST); $this->savedJson(); }

    public function saveUnits(string $id): never
    { $this->saveUnitsAndRedirect($id, 'avaluos/' . $id . '/bien-sujeto'); }

    public function autosaveUnits(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $this->appraisals->saveUnits($id, $this->user['id'],
            AppraisalChapterZeroInput::unitData($this->igacCodes(), $this->typologies->optionsByCategory()));
        $this->savedJson();
    }

    public function saveSurfaces(string $id): never
    { $this->saveSubjectData($id, fn () => $this->appraisals->saveUnitSurfaces($id, $this->user['id'], AppraisalChapterZeroInput::unitSurfaceData()), 'Datos de superficie guardados correctamente.', '#superficies'); }

    public function autosaveSurfaces(string $id): never
    { $this->appraisals->find($id, $this->user['id']); $this->appraisals->saveUnitSurfaces($id, $this->user['id'], AppraisalChapterZeroInput::unitSurfaceData()); $this->savedJson(); }

    public function saveConstructions(string $id): never
    { $this->saveSubjectData($id, fn () => $this->appraisals->saveUnitConstructions($id, $this->user['id'], AppraisalChapterZeroInput::unitConstructionData()), 'Datos de construcción guardados correctamente.', '#construccion'); }

    public function autosaveConstructions(string $id): never
    { $this->appraisals->find($id, $this->user['id']); $this->appraisals->saveUnitConstructions($id, $this->user['id'], AppraisalChapterZeroInput::unitConstructionData()); $this->savedJson(); }

    public function saveAttributes(string $id): never
    { $this->saveSubjectData($id, fn () => $this->saveAttributesAndPhotos($id), 'Atributos especiales guardados correctamente.', '#atributos'); }

    public function autosaveAttributes(string $id): never
    { $this->appraisals->find($id, $this->user['id']); $this->appraisals->saveUnitAttributes($id, $this->user['id'], AppraisalAttributeInput::unitAttributeData()); $this->savedJson(); }

    public function savePreclassification(string $id): never
    { $this->savePreclassificationAndRedirect($id, 'avaluos/' . $id . '/bien-sujeto'); }

    public function autosavePreclassification(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $result = $this->appraisals->savePreclassification($id, $this->user['id'], (int) ($_POST['version'] ?? 0),
            AppraisalChapterZeroInput::preclassificationData($this->igacCodes()));
        Http::json(['ok' => true] + $result);
    }

    public function uploadPhotos(string $id): never
    { $this->uploadPhotosAndRedirect($id, $this->safePhotoReturn($id)); }

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

    private function saveAttributesAndPhotos(string $id): void
    {
        $this->appraisals->saveUnitAttributes($id, $this->user['id'], AppraisalAttributeInput::unitAttributeData());
        (new AppraisalPhotoUploadService())->storeAttributeEvidence($_FILES['attribute_photos'] ?? [], $id, $this->user['id'], $this->appraisals);
    }

    private function saveSubjectData(string $id, callable $save, string $message, string $hash): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try { $save(); Session::flash('subject_message', $message); }
        catch (\Throwable $error) { Session::flash('subject_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto' . $hash);
    }

    private function saveUnitsAndRedirect(string $id, string $target): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->saveUnits($id, $this->user['id'],
                AppraisalChapterZeroInput::unitData($this->igacCodes(), $this->typologies->optionsByCategory()));
            Session::flash('chapter_zero_preclass_message', 'Unidades guardadas correctamente.');
        } catch (\Throwable $error) { Session::flash('chapter_zero_preclass_error', $error->getMessage()); }
        Http::redirect($target);
    }

    private function savePreclassificationAndRedirect(string $id, string $target): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->savePreclassification($id, $this->user['id'], (int) ($_POST['version'] ?? 0),
                AppraisalChapterZeroInput::preclassificationData($this->igacCodes()));
            Session::flash('chapter_zero_preclass_message', 'Lectura inicial guardada correctamente.');
        } catch (\Throwable $error) { Session::flash('chapter_zero_preclass_error', $error->getMessage()); }
        Http::redirect($target);
    }

    private function uploadPhotosAndRedirect(string $id, string $target): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        try {
            $unitId = preg_match('/^[a-f0-9]{32}$/', (string) ($_POST['unit_id'] ?? '')) ? (string) $_POST['unit_id'] : null;
            $caption = mb_substr(trim((string) ($_POST['photo_caption'] ?? '')), 0, 190);
            $displayName = mb_substr(trim((string) ($_POST['photo_name'] ?? '')), 0, 190);
            if ($displayName === '') $displayName = $this->attributePhotoName($caption);
            $count = (new AppraisalPhotoUploadService())->store($_FILES['photos'] ?? [], $record['id'],
                $this->user['id'], $this->appraisals, $unitId, $caption, $displayName);
            Session::flash('chapter_zero_photo_message', $count === 1 ? 'Foto cargada correctamente.' : $count . ' fotos cargadas correctamente.');
        } catch (\Throwable $error) { Session::flash('chapter_zero_photo_error', $error->getMessage()); }
        Http::redirect($target);
    }

    private function safePhotoReturn(string $id): string
    {
        $target = (string) ($_POST['return_to'] ?? '');
        [$subject, $sector] = ['avaluos/' . $id . '/bien-sujeto', 'avaluos/' . $id . '/sector'];
        return in_array($target, ['avaluos/' . $id . '/expediente', $subject, $subject . '#atributos', $subject . '#fotos'], true)
            || preg_match('#^' . preg_quote($subject, '#') . '\#fotos(?:-general|-[a-f0-9]{32})$#', $target)
            || preg_match('#^' . preg_quote($sector, '#') . '(?:\#[a-z_]+)?$#', $target) ? $target : $subject . '#fotos';
    }

    private function attributePhotoName(string $caption): string
    {
        if (!preg_match('/^attribute:([a-z0-9_]+)$/', $caption, $match)) return '';
        return AppraisalSpecialAttributeCatalog::labels()[$match[1]] ?? '';
    }

    private function igacCodes(): array { return array_column($this->typologies->categories(), 'code'); }
    private function savedJson(): never { Http::json(['ok' => true, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]); }
}
