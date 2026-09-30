<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Http;
use App\Models\{AppraisalRepository, ComparablePhotoRepository};
use App\Services\ComparablePhotoUpload;

final class ComparablePhotoController
{
    public function __construct(private AppraisalRepository $appraisals, private ComparablePhotoRepository $photos, private array $user) {}

    public function index(string $appraisal, string $comparable): never
    {
        $this->appraisals->find($appraisal, $this->user['id']);
        Http::json(['ok' => true, 'photos' => $this->photos->listing($appraisal, $comparable, $this->user['id'])]);
    }

    public function upload(string $appraisal, string $comparable): never
    {
        $this->appraisals->find($appraisal, $this->user['id']);
        $this->photos->requireComparable($appraisal, $comparable, $this->user['id']);
        try {
            $file = $_FILES['photo'] ?? [];
            if (!is_string($file['tmp_name'] ?? null) || !is_uploaded_file($file['tmp_name'])) {
                throw new \InvalidArgumentException('No se recibió una foto válida. Revisa el límite de carga del servidor.');
            }
            $caption = $_POST['caption'] ?? '';
            if (!is_string($caption)) throw new \InvalidArgumentException('La descripción debe ser texto.');
            $this->photos->store($appraisal, $comparable, $this->user['id'], ComparablePhotoUpload::inspect($file, $caption));
            Http::json(['ok' => true, 'photos' => $this->photos->listing($appraisal, $comparable, $this->user['id'])]);
        } catch (\InvalidArgumentException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 422); }
    }

    public function show(string $appraisal, string $comparable, string $photo): never
    {
        $this->appraisals->find($appraisal, $this->user['id']);
        $row = $this->photos->photo($appraisal, $comparable, $this->user['id'], $photo);
        header('Content-Type: ' . $row['mime_type']);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        header('Content-Disposition: inline');
        echo $row['file_blob'];
        exit;
    }
}
