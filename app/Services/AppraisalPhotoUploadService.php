<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalRepository;

final class AppraisalPhotoUploadService
{
    public function store(array $files, string $appraisalId, int $owner, AppraisalRepository $repo,
        ?string $unitId = null, string $caption = '', string $displayName = ''): int
    {
        if (!is_array($files['name'] ?? null)) {
            throw new \InvalidArgumentException('Selecciona al menos una foto.');
        }
        $stored = 0;
        foreach (array_keys($files['name']) as $index) {
            $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
            if ($error === UPLOAD_ERR_NO_FILE) continue;
            if ($error !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo recibir una de las fotos.');
            $this->storeOne($files, $index, $appraisalId, $owner, $repo, $unitId, $caption, $displayName);
            $stored++;
        }
        if ($stored === 0) throw new \InvalidArgumentException('Selecciona al menos una foto.');
        return $stored;
    }

    public function storeAttributeEvidence(array $files, string $appraisalId, int $owner, AppraisalRepository $repo): int
    {
        $stored = 0;
        foreach (($files['name'] ?? []) as $unitId => $attributes) {
            if (!preg_match('/^[a-f0-9]{32}$/', (string) $unitId) || !is_array($attributes)) continue;
            foreach ($attributes as $key => $names) {
                foreach (array_keys((array) $names) as $index) {
                    $flat = $this->flatNested($files, (string) $unitId, (string) $key);
                    $error = (int) ($flat['error'][$index] ?? UPLOAD_ERR_NO_FILE);
                    if ($error === UPLOAD_ERR_NO_FILE) continue;
                    if ($error !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo recibir una evidencia.');
                    $this->storeOne($flat, $index, $appraisalId, $owner, $repo, (string) $unitId, 'attribute:' . $key, '');
                    $stored++;
                }
            }
        }
        return $stored;
    }

    private function flatNested(array $files, string $unitId, string $key): array
    {
        return array_map(static fn (array $values): array => (array) ($values[$unitId][$key] ?? []), $files);
    }

    private function storeOne(array $files, int $index, string $appraisalId, int $owner, AppraisalRepository $repo,
        ?string $unitId, string $caption, string $displayName): void
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
            'caption' => $caption, 'display_name' => mb_substr(trim($displayName), 0, 190),
            'file_blob' => $blob, 'unit_id' => $unitId]);
    }
}
