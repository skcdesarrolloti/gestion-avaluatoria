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
        $notice = Session::pullFlash('standards_import');
        view('standards/index', [
            'title' => 'Normas Técnicas Sectoriales',
            'categories' => $this->standards->categoriesWithStandards(),
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
        Http::redirect('normas-tecnicas-sectoriales');
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
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($standard['file_path']));
        header('Content-Disposition: inline; filename="' . $name . '"');
        readfile($standard['file_path']);
        exit;
    }
}
