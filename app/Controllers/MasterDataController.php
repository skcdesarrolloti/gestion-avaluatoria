<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraiserRepository;
use App\Models\MasterDocumentRepository;
use App\Services\MasterDocumentUploadService;
use App\Services\AppraiserRaaImportService;
use App\Support\RaaCategoryCatalog;
use PDOException;

final class MasterDataController
{
    public function __construct(private AppraiserRepository $appraisers, private MasterDocumentRepository $documents) {}

    public function index(): void
    {
        view('masters/index', [
            'title' => 'Creación de Maestros',
            'appraisers' => $this->appraisers->all(),
            'analystAccounts' => (new \App\Models\AnalystAccountRepository(\App\Core\Database::connection()))->forOwner((int)$_SESSION['user']['id']),
            'documents' => $this->documents->latest(),
            'documentDestinations' => MasterDocumentRepository::destinations(),
            'documentModules' => MasterDocumentRepository::modules(),
            'documentStorage' => $this->documents->storageReport(),
            'categories' => RaaCategoryCatalog::all(),
            'message' => Session::pullFlash('masters_message'),
            'error' => Session::pullFlash('masters_error'),
        ]);
    }

    public function createAppraiser(): never
    {
        try {
            $result = (new AppraiserRaaImportService($this->appraisers))->import($_POST, $_FILES['raa_file'] ?? []);
            Session::flash('masters_message', $result['updated']
                ? 'RAA actualizado. Se conservaron código, nombre y cédula del perito.'
                : 'Perito creado desde el certificado RAA.');
        } catch (PDOException $error) {
            $message = str_contains($error->getMessage(), 'uq_valuation_appraisers_code')
                ? 'Ya existe un perito con ese código.' : 'No se pudo guardar el perito.';
            Session::flash('masters_error', $message);
        } catch (\InvalidArgumentException|\RuntimeException $error) {
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros');
    }

    public function createDocument(): never
    {
        try {
            $result = (new MasterDocumentUploadService($this->documents))
                ->upload($_POST, $_FILES['document_file'] ?? [], $_SESSION['user'] ?? []);
            Session::flash('masters_message', 'Documento maestro cargado: ' . $result['document_code'] . '.');
        } catch (\Throwable $error) {
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros#biblioteca-documental');
    }

    public function documentFile(string $id): never
    {
        $document = $this->documents->find($id);
        $name = str_replace(['"', '\\'], '', (string) ($document['source_filename'] ?: $document['title'] . '.pdf'));
        $blob = is_string($document['pdf_blob'] ?? null) ? $document['pdf_blob'] : null;
        $fromDisk = is_string($document['file_path']) && is_file($document['file_path']);
        if (!$fromDisk && $blob === null) throw new HttpException(404, 'No se encontró el PDF del documento maestro.');
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . ($fromDisk ? filesize($document['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) readfile($document['file_path']); else echo $blob;
        exit;
    }

    public function raaFile(string $id): never
    {
        $appraiser = $this->appraisers->find($id);
        $filename = (string) ($appraiser['raa_storage_filename'] ?? '');
        $path = AppraiserRepository::raaPath($filename);
        if ($filename === '' || !is_file($path)) throw new HttpException(404, 'No se encontró el soporte RAA.');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename((string) $appraiser['raa_source_filename']) . '"');
        readfile($path);
        exit;
    }
}
