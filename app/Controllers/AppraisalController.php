<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraiserRepository;
use App\Models\IgacTypologyRepository;
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
        view('appraisals/chapter-zero', ['title' => 'Capítulo 0', 'record' => $record,
            'photos' => $this->appraisals->photos($id, $this->user['id']),
            'appraisers' => $this->appraisers->all(), 'igacCategories' => $this->typologies->categories(),
            'photoMessage' => Session::pullFlash('chapter_zero_photo_message'),
            'photoError' => Session::pullFlash('chapter_zero_photo_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function saveChapterZero(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $data = $this->chapterZeroData((int) ($_POST['version'] ?? 0));
        $this->appraisals->saveChapterZero($id, $this->user['id'], (int) $_POST['version'], $data);
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
        if (!is_file($path)) throw new HttpException(404, 'No se encontró la foto.');
        header('Content-Type: ' . $photo['mime_type']);
        header('Content-Disposition: inline; filename="' . basename((string) $photo['source_filename']) . '"');
        readfile($path);
        exit;
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

    private function chapterZeroData(int $version): array
    {
        if ($version < 1) throw new HttpException(422, 'La versión del borrador no es válida.');
        $input = ['version' => $version];
        foreach (AppraisalCatalog::fieldKeys() as $field) $input[$field] = (string) ($_POST[$field] ?? '');
        $data = AppraisalValidator::validate($input);
        $extra = [];
        foreach (['appraiser_id', 'igac_category', 'igac_typology_hint', 'inspection_notes'] as $field) {
            $extra[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $extra['configuration_status'] = 'borrador';
        if ($extra['appraiser_id'] !== '' && !$this->appraiserExists($extra['appraiser_id'])) {
            throw new HttpException(422, 'Selecciona un perito válido.');
        }
        if ($extra['igac_category'] !== '' && !in_array($extra['igac_category'], $this->igacCodes(), true)) {
            throw new HttpException(422, 'Selecciona una categoría IGAC válida.');
        }
        $extra['igac_typology_hint'] = mb_substr($extra['igac_typology_hint'], 0, 190);
        $extra['inspection_notes'] = mb_substr($extra['inspection_notes'], 0, 2000);
        return $data + $extra;
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
            $this->appraisals->addPhoto($id, $this->user['id'], ['id' => $photoId,
                'source_filename' => $source, 'storage_filename' => $storage,
                'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'caption' => '']);
            $stored++;
        }
        if ($stored === 0) throw new \InvalidArgumentException('Selecciona al menos una foto.');
        return $stored;
    }

    private function appraiserExists(string $id): bool
    {
        foreach ($this->appraisers->all() as $appraiser) if ($appraiser['id'] === $id) return true;
        return false;
    }

    private function igacCodes(): array
    {
        return array_column($this->typologies->categories(), 'code');
    }
}
