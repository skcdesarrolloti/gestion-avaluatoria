<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\InternationalStandardRepository;
use App\Services\InternationalStandardFileImportService;

final class InternationalStandardController
{
    public function __construct(private InternationalStandardRepository $standards) {}

    public function index(): void
    {
        $groups = $this->standards->groupsWithStandards();
        $codes = array_column($groups, 'code');
        $requested = (string) ($_GET['grupo'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : (string) ($codes[0] ?? 'G');
        view('international/index', [
            'title' => 'Normas Internacionales de Valuación',
            'groups' => $groups,
            'activeGroupCode' => $active,
            'stats' => $this->standards->stats($groups),
            'storageReport' => $this->standards->storageReport(),
            'importNotice' => ($notice = Session::pullFlash('international_import'))
                ? json_decode($notice, true) : null,
        ]);
    }

    public function importFile(string $slug): void
    {
        $standard = $this->standards->find($slug);
        try {
            $result = (new InternationalStandardFileImportService($this->standards))
                ->importFor($_FILES['international_file'] ?? [], $standard);
        } catch (\Throwable $error) {
            $result = ['ok' => false, 'message' => $error->getMessage()];
        }
        Session::flash('international_import', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        Http::redirect('normas-internacionales-valuacion?grupo=' . rawurlencode((string) $standard['group_code']));
    }

    public function file(string $slug): void
    {
        $standard = $this->standards->find($slug);
        if (!$standard['has_file']) {
            throw new HttpException(404, 'El PDF de esta norma internacional todavía no fue importado.');
        }
        $name = str_replace(['"', '\\'], '', (string) $standard['source_filename']);
        $blob = is_string($standard['pdf_blob'] ?? null) ? $standard['pdf_blob'] : null;
        $fromDisk = is_string($standard['file_path']) && is_file($standard['file_path']);
        if (!$fromDisk && $blob === null) {
            throw new HttpException(404, 'El PDF de esta norma internacional todavía no fue importado.');
        }
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . ($fromDisk ? filesize($standard['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) readfile($standard['file_path']); else echo $blob;
        exit;
    }
}
