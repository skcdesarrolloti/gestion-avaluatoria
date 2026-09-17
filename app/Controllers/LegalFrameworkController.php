<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\LegalDocumentRepository;
use App\Services\LegalDocumentFileImportService;
use App\Services\LegalDocumentImportService;

final class LegalFrameworkController
{
    public function __construct(private LegalDocumentRepository $documents) {}

    public function index(): void
    {
        $categories = $this->documents->categoriesWithDocuments();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : (string) ($codes[0] ?? 'A');
        $notice = Session::pullFlash('legal_import');
        view('legal/index', [
            'title' => 'Marco Jurídico Nacional',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'legalStats' => $this->documents->stats($categories),
            'storageReport' => $this->documents->storageReport(),
            'importNotice' => $notice ? json_decode($notice, true) : null,
        ]);
    }

    public function import(): void
    {
        $category = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_POST['categoria'] ?? 'A'));
        $status = preg_replace('/[^a-z]/', '', (string) ($_POST['estado'] ?? 'vigente'));
        try {
            $result = (new LegalDocumentImportService($this->documents))
                ->importUploaded($_FILES['legal_files'] ?? [], $category, $status);
        } catch (\Throwable $error) {
            $result = ['ok' => false, 'message' => $error->getMessage()];
        }
        Session::flash('legal_import', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        Http::redirect('marco-juridico-valuatorio' . ($category ? '?categoria=' . rawurlencode($category) : ''));
    }

    public function importFile(string $slug): void
    {
        $document = $this->documents->find($slug);
        try {
            $result = (new LegalDocumentFileImportService($this->documents))
                ->importFor($_FILES['legal_file'] ?? [], $document);
        } catch (\Throwable $error) {
            $result = ['ok' => false, 'message' => $error->getMessage()];
        }
        Session::flash('legal_import', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        Http::redirect('marco-juridico-valuatorio?categoria=' . rawurlencode((string) $document['category_code']));
    }

    public function file(string $slug): void
    {
        $document = $this->documents->find($slug);
        if (!$document['has_file']) {
            throw new HttpException(404, 'El PDF de este documento jurídico todavía no fue importado.');
        }
        $name = str_replace(['"', '\\'], '', (string) ($document['source_filename'] ?: $document['title'] . '.pdf'));
        $blob = is_string($document['pdf_blob'] ?? null) ? $document['pdf_blob'] : null;
        $fromDisk = is_string($document['file_path']) && is_file($document['file_path']);
        if (!$fromDisk && $blob === null) {
            throw new HttpException(404, 'El PDF de este documento jurídico todavía no fue importado.');
        }
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . ($fromDisk ? filesize($document['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) {
            readfile($document['file_path']);
        } else {
            echo $blob;
        }
        exit;
    }
}
