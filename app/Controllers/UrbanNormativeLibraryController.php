<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, HttpException, Session};
use App\Models\UrbanNormativeRepository;
use App\Services\UrbanNormFileImportService;

final class UrbanNormativeLibraryController
{
    public function __construct(private UrbanNormativeRepository $documents) {}

    public function index(): void
    {
        $items = $this->documents->documents();
        $notice = Session::pullFlash('urban_norm_import');
        view('urban-normative/index', ['title' => 'Normatividad urbana',
            'documents' => $items, 'stats' => $this->documents->stats($items),
            'storageReport' => $this->documents->storageReport(),
            'importNotice' => $notice ? json_decode($notice, true) : null]);
    }

    public function importFile(string $slug): never
    {
        $document = $this->documents->find($slug);
        try {
            $result = (new UrbanNormFileImportService($this->documents))
                ->importFor($_FILES['urban_norm_file'] ?? [], $document);
        } catch (\Throwable $error) {
            $result = ['ok' => false, 'message' => $error->getMessage()];
        }
        Session::flash('urban_norm_import', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        Http::redirect('normatividad-urbana');
    }

    public function file(string $slug): void
    {
        $document = $this->documents->find($slug);
        if (!$document['has_file']) throw new HttpException(404, 'El PDF de esta norma urbana todavía no fue importado.');
        $name = str_replace(['"', '\\'], '', (string) ($document['source_filename'] ?: $document['slug'] . '.pdf'));
        $blob = is_string($document['pdf_blob'] ?? null) ? $document['pdf_blob'] : null;
        $fromDisk = is_string($document['file_path']) && is_file($document['file_path']);
        if (!$fromDisk && $blob === null) throw new HttpException(404, 'El PDF de esta norma urbana todavía no fue importado.');
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . ($fromDisk ? filesize($document['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) readfile($document['file_path']); else echo $blob;
        exit;
    }
}
