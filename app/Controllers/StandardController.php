<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\ValuationStandardRepository;

final class StandardController
{
    public function __construct(private ValuationStandardRepository $standards) {}

    public function index(): void
    {
        $categories = $this->standards->categoriesWithStandards();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : (string) ($codes[0] ?? 'A');
        $stats = $this->stats($categories);
        $notice = Session::pullFlash('standards_import');
        view('standards/index', [
            'title' => 'Normas Técnicas Sectoriales',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'standardStats' => $stats,
            'storageReport' => $this->standards->storageReport(),
            'importNotice' => $notice ? json_decode($notice, true) : null,
        ]);
    }

    public function import(): void
    {
        try {
            $result = $this->standards->importUploaded($_FILES['standard_files'] ?? []);
        } catch (\Throwable $error) {
            $result = ['ok' => false, 'message' => $error->getMessage()];
        }
        Session::flash('standards_import', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $category = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_POST['categoria'] ?? ''));
        Http::redirect('normas-tecnicas-sectoriales' . ($category ? '?categoria=' . rawurlencode($category) : ''));
    }

    private function stats(array $categories): array
    {
        $stats = ['total' => 0, 'available' => 0, 'missing' => 0];
        foreach ($categories as $category) {
            foreach ($category['standards'] as $standard) {
                $stats['total']++;
                $standard['has_file'] ? $stats['available']++ : $stats['missing']++;
            }
        }
        return $stats;
    }

    public function show(string $slug): void
    {
        view('standards/show', [
            'title' => 'Norma técnica',
            'standard' => $this->standards->find($slug),
        ]);
    }

    public function file(string $slug): void
    {
        $standard = $this->standards->find($slug);
        if (!$standard['has_file']) {
            throw new HttpException(404, 'El PDF de esta norma todavía no fue importado.');
        }
        $name = str_replace(['"', '\\'], '', (string) $standard['source_filename']);
        $blob = is_string($standard['pdf_blob'] ?? null) ? $standard['pdf_blob'] : null;
        $fromDisk = is_string($standard['file_path']) && is_file($standard['file_path']);
        if (!$fromDisk && $blob === null) {
            throw new HttpException(404, 'El PDF de esta norma todavía no fue importado.');
        }
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . ($fromDisk ? filesize($standard['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) readfile($standard['file_path']); else echo $blob;
        exit;
    }
}
