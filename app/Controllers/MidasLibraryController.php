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
        $groups = MidasDocumentRepository::groups();
        $documents = $this->documents->latest();
        $activeGroup = (string) ($_GET['grupo'] ?? array_key_first($groups));
        if (!isset($groups[$activeGroup])) $activeGroup = (string) array_key_first($groups);
        $groupStats = array_fill_keys(array_keys($groups), 0);
        foreach ($documents as $document) {
            $group = (string) ($document['layer_group'] ?? '');
            $groupStats[$group] = ($groupStats[$group] ?? 0) + 1;
        }
        view('midas/index', [
            'title' => 'Biblioteca MIDAS',
            'documents' => $documents,
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'groupStats' => $groupStats,
            'storage' => $this->documents->storageReport(),
            'message' => Session::pullFlash('midas_message'),
            'error' => Session::pullFlash('midas_error'),
        ]);
    }

    public function upload(): never
    {
        $selectedGroup = MidasDocumentRepository::canonicalGroup((string) ($_POST['layer_group'] ?? ''));
        try {
            $result = (new MidasDocumentUploadService($this->documents))
                ->uploadMany($_POST, $_FILES['midas_file'] ?? [], $this->user);
            $message = count($result['stored']) . ' documento(s) MIDAS cargado(s).';
            if ($result['skipped']) $message .= ' ' . count($result['skipped']) . ' ya existía(n) y se omitieron.';
            Session::flash('midas_message', $message);
            if ($result['stored']) $selectedGroup = (string) ($result['stored'][0]['layer_group'] ?? $selectedGroup);
        } catch (\Throwable $error) {
            Session::flash('midas_error', $error->getMessage());
        }
        $groups = MidasDocumentRepository::groups();
        $target = isset($groups[$selectedGroup]) ? '?grupo=' . rawurlencode($selectedGroup) : '';
        Http::redirect('midas' . $target . '#biblioteca-midas');
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
