<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Http, HttpException, Session};
use App\Models\MidasDocumentRepository;
use App\Services\MidasDocumentUploadService;

final class MidasLibraryController
{
    public function __construct(private MidasDocumentRepository $documents, private array $user) {}

    public function index(): void
    {
        view('midas/index', [
            'title' => 'Biblioteca MIDAS',
            'documents' => $this->documents->latest(),
            'groups' => MidasDocumentRepository::groups(),
            'storage' => $this->documents->storageReport(),
            'message' => Session::pullFlash('midas_message'),
            'error' => Session::pullFlash('midas_error'),
        ]);
    }

    public function upload(): never
    {
        try {
            $data = (new MidasDocumentUploadService($this->documents))
                ->upload($_POST, $_FILES['midas_file'] ?? [], $this->user);
            Session::flash('midas_message', 'Documento MIDAS cargado: ' . $data['title'] . '.');
        } catch (\Throwable $error) {
            Session::flash('midas_error', $error->getMessage());
        }
        Http::redirect('midas#biblioteca-midas');
    }

    public function file(string $id): never
    {
        $document = $this->documents->find($id);
        $blob = is_string($document['file_blob'] ?? null) ? $document['file_blob'] : null;
        $fromDisk = is_string($document['file_path']) && is_file($document['file_path']);
        if (!$fromDisk && $blob === null) throw new HttpException(404, 'No se encontró el archivo MIDAS.');
        $name = str_replace(['"', '\\'], '', (string) $document['source_filename']);
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: ' . (string) $document['mime_type']);
        header('Content-Length: ' . ($fromDisk ? filesize($document['file_path']) : strlen($blob)));
        header('Content-Disposition: inline; filename="' . $name . '"');
        if ($fromDisk) readfile($document['file_path']); else echo $blob;
        exit;
    }

    public function delete(string $id): never
    {
        try {
            $document = $this->documents->delete($id);
            $path = (string) $document['file_path'];
            if (is_file($path)) @unlink($path);
            Session::flash('midas_message', 'Documento MIDAS eliminado: ' . $document['title'] . '.');
        } catch (\Throwable $error) {
            Session::flash('midas_error', $error->getMessage());
        }
        Http::redirect('midas#biblioteca-midas');
    }
}
