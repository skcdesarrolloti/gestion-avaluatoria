<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalRepository;

final class AppraisalPhotoUploadService
{
    public function store(array $files, string $appraisalId, int $owner, AppraisalRepository $repo, ?string $unitId = null): int
    {
        if (!is_array($files['name'] ?? null)) {
            throw new \InvalidArgumentException('Selecciona al menos una foto.');
        }
        $stored = 0;
        foreach (array_keys($files['name']) as $index) {
            $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
            if ($error === UPLOAD_ERR_NO_FILE) continue;
            if ($error !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo recibir una de las fotos.');
            $this->storeOne($files, $index, $appraisalId, $owner, $repo, $unitId);
            $stored++;
        }
        if ($stored === 0) throw new \InvalidArgumentException('Selecciona al menos una foto.');
        return $stored;
    }

    private function storeOne(array $files, int $index, string $appraisalId, int $owner, AppraisalRepository $repo, ?string $unitId): void
    {
        $photoId = bin2hex(random_bytes(16));
        $source = basename(str_replace('\\', '/', (string) $files['name'][$index]));
        $info = AppraisalPhotoStorage::inspect((string) $files['tmp_name'][$index], $source);
        $storage = 'foto-' . $appraisalId . '-' . $photoId . '.' . $info['extension'];
        $bytes = AppraisalPhotoStorage::storeUploaded((string) $files['tmp_name'][$index],
            AppraisalPhotoStorage::path($storage));
        $blob = file_get_contents(AppraisalPhotoStorage::path($storage));
        if (!is_string($blob)) throw new \RuntimeException('La foto no pudo quedar respaldada.');
        $repo->addPhoto($appraisalId, $owner, ['id' => $photoId, 'source_filename' => $source,
            'storage_filename' => $storage, 'mime_type' => $info['mime'], 'file_size_bytes' => $bytes,
            'caption' => '', 'file_blob' => $blob, 'unit_id' => $unitId]);
    }
}
