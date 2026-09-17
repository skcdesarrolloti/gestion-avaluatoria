<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalSectorMidasFileRepository;

final class AppraisalMidasSupportUploadService
{
    public function store(array $files, string $appraisalId, int $owner, string $neighborhoodId,
        AppraisalSectorMidasFileRepository $repo, string $layerGroup, string $notes): array
    {
        $file = $this->singleFile($files);
        $name = $this->cleanName((string) $file['name']);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("$name " . AppraisalMidasFileStorage::uploadErrorMessage((int) $file['error']));
        }
        $info = AppraisalMidasFileStorage::inspect((string) $file['tmp_name'], $name);
        $fileId = bin2hex(random_bytes(16));
        $storageName = 'midas-' . $appraisalId . '-' . $fileId . '.' . $info['extension'];
        $bytes = AppraisalMidasFileStorage::storeUploaded((string) $file['tmp_name'],
            AppraisalMidasFileStorage::path($storageName));
        $blob = file_get_contents(AppraisalMidasFileStorage::path($storageName));
        if (!is_string($blob)) throw new \RuntimeException('El soporte se guardó, pero no quedó respaldado.');
        $record = ['id' => $fileId, 'neighborhood_id' => mb_substr($neighborhoodId, 0, 80),
            'layer_group' => mb_substr($layerGroup, 0, 120), 'source_filename' => $name,
            'storage_filename' => $storageName, 'mime_type' => $info['mime'],
            'file_size_bytes' => $bytes, 'notes' => mb_substr(trim($notes), 0, 1000), 'file_blob' => $blob];
        $repo->add($appraisalId, $owner, $record);
        return $record;
    }

    private function singleFile(array $files): array
    {
        $names = $files['name'] ?? [];
        if (!is_array($names) || $names === []) {
            throw new \InvalidArgumentException('Selecciona el archivo descargado de MIDAS.');
        }
        return ['name' => (string) ($files['name'][0] ?? ''),
            'tmp_name' => (string) ($files['tmp_name'][0] ?? ''),
            'error' => (int) ($files['error'][0] ?? UPLOAD_ERR_NO_FILE)];
    }

    private function cleanName(string $name): string { return mb_substr(basename(str_replace('\\', '/', $name)), 0, 220); }
}
