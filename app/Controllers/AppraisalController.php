<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraiserRepository;
use App\Models\IgacTypologyRepository;
use App\Services\AppraisalChapterZeroInput;
use App\Services\AppraisalPhotoStorage;
use App\Services\AppraisalValidator;
use App\Support\AppraisalCatalog;

final class AppraisalController
{
    public function __construct(
        private AppraisalRepository $appraisals,
        private array $user,
        private AppraiserRepository $appraisers,
        private IgacTypologyRepository $typologies,
    ) {}

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
        Http::redirect('avaluos/' . $id . '/capitulo-0');
    }

    public function chapterZero(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $this->appraisals->ensureUnits($id, $this->user['id'],
            (int) ($record['igac_property_units_count'] ?? 0), (int) ($record['igac_annex_units_count'] ?? 0));
        view('appraisals/chapter-zero', ['title' => 'Capítulo 0', 'record' => $record,
            'photos' => $this->appraisals->photos($id, $this->user['id']),
            'units' => $this->appraisals->units($id, $this->user['id']),
            'appraisers' => $this->appraisers->all(), 'igacCategories' => $this->typologies->categories(),
            'igacTypologiesByCategory' => $this->typologies->optionsByCategory(),
            'photoMessage' => Session::pullFlash('chapter_zero_photo_message'),
            'photoError' => Session::pullFlash('chapter_zero_photo_error'),
            'preclassMessage' => Session::pullFlash('chapter_zero_preclass_message'),
            'preclassError' => Session::pullFlash('chapter_zero_preclass_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function saveChapterZero(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
            $this->igacCodes(), $this->appraiserIds());
        $this->appraisals->saveChapterZero($id, $this->user['id'], (int) $_POST['version'], $data);
        Http::redirect('avaluos/' . $record['id'] . '/capitulo-0');
    }

    public function saveChapterZeroUnits(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->saveUnits($id, $this->user['id'],
                AppraisalChapterZeroInput::unitData($this->igacCodes(), $this->typologies->optionsByCategory()));
            Session::flash('chapter_zero_preclass_message', 'Unidades guardadas correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_preclass_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/capitulo-0');
    }

    public function saveChapterZeroPreclassification(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        try {
            $this->appraisals->savePreclassification($id, $this->user['id'], (int) ($_POST['version'] ?? 0),
                AppraisalChapterZeroInput::preclassificationData($this->igacCodes()));
            Session::flash('chapter_zero_preclass_message', 'Lectura inicial guardada correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_preclass_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $record['id'] . '/capitulo-0');
    }

    public function uploadChapterZeroPhotos(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        try {
            $count = $this->storePhotos($record['id']);
            Session::flash('chapter_zero_photo_message', $count === 1
                ? 'Foto cargada correctamente.'
                : $count . ' fotos cargadas correctamente.');
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_photo_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $record['id'] . '/capitulo-0');
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
        Http::redirect('avaluos/' . $id . '/capitulo-0');
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

    private function storePhotos(string $id): int
    {
        $files = $_FILES['photos'] ?? null;
        if (!is_array($files) || !is_array($files['name'] ?? null)) {
            throw new \InvalidArgumentException('Selecciona al menos una foto.');
        }
        $stored = 0;
        foreach (array_keys($files['name']) as $index) {
            $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
            if ($error === UPLOAD_ERR_NO_FILE) continue;
            if ($error !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo recibir una de las fotos.');
            $photoId = bin2hex(random_bytes(16));
            $source = basename(str_replace('\\', '/', (string) $files['name'][$index]));
            $info = AppraisalPhotoStorage::inspect((string) $files['tmp_name'][$index], $source);
            $storage = 'foto-' . $id . '-' . $photoId . '.' . $info['extension'];
            $bytes = AppraisalPhotoStorage::storeUploaded((string) $files['tmp_name'][$index],
                AppraisalPhotoStorage::path($storage));
            $blob = file_get_contents(AppraisalPhotoStorage::path($storage));
            if (!is_string($blob)) throw new \RuntimeException('La foto no pudo quedar respaldada.');
            $this->appraisals->addPhoto($id, $this->user['id'], ['id' => $photoId,
                'source_filename' => $source, 'storage_filename' => $storage,
                'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'caption' => '', 'file_blob' => $blob]);
            $stored++;
        }
        if ($stored === 0) throw new \InvalidArgumentException('Selecciona al menos una foto.');
        return $stored;
    }

    private function appraiserIds(): array
    {
        return array_column($this->appraisers->all(), 'id');
    }

    private function igacCodes(): array
    {
        return array_column($this->typologies->categories(), 'code');
    }
}
