<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\MidasDocumentRepository;

final class MidasDocumentUploadService
{
    public function __construct(private MidasDocumentRepository $documents) {}

    public function upload(array $input, array $file, array $user): array
    {
        $name = $this->cleanName((string) ($file['name'] ?? ''));
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(($name ?: 'El archivo') . ' ' . MidasDocumentStorage::uploadErrorMessage($error));
        }
        $groups = MidasDocumentRepository::groups();
        $group = $this->text($input['layer_group'] ?? '', 120);
        if (!isset($groups[$group])) throw new \InvalidArgumentException('Selecciona el grupo de capa MIDAS.');
        $title = $this->text($input['title'] ?? '', 240);
        if ($title === '') $title = pathinfo($name, PATHINFO_FILENAME) ?: 'Documento MIDAS';
        $code = $this->text($input['document_code'] ?? '', 120);
        if ($code === '') $code = $this->text($title, 120);
        if ($duplicate = $this->documents->findDuplicate($code, $name)) {
            throw new \RuntimeException('Ya existe en Biblioteca MIDAS: ' . $duplicate['title'] . '.');
        }
        $info = MidasDocumentStorage::inspect((string) $file['tmp_name'], $name);
        $id = bin2hex(random_bytes(16));
        $storageName = 'midas-biblioteca-' . $id . '.' . $info['extension'];
        $bytes = MidasDocumentStorage::storeUploaded((string) $file['tmp_name'], MidasDocumentStorage::path($storageName));
        $blob = file_get_contents(MidasDocumentStorage::path($storageName));
        if (!is_string($blob)) throw new \RuntimeException('No se pudo conservar el respaldo del documento MIDAS.');
        $data = ['id' => $id, 'slug' => $this->documents->uniqueSlug($this->slug($code . ' ' . $title)),
            'layer_group' => $group, 'document_code' => $code, 'title' => $title,
            'status' => $this->status((string) ($input['status'] ?? 'vigente')),
            'practical_use' => $this->text($input['practical_use'] ?? '', 700),
            'applies_to' => $this->text($input['applies_to'] ?? '', 240),
            'source_filename' => $name, 'storage_filename' => $storageName,
            'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'file_blob' => $blob,
            'created_by' => $this->text($user['name'] ?? '', 120), 'now' => gmdate('Y-m-d H:i:s')];
        $this->documents->store($data);
        return $data;
    }

    private function cleanName(string $name): string
    {
        return $this->text(basename(str_replace('\\', '/', $name)), 240);
    }

    private function text(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }

    private function status(string $status): string
    {
        return in_array($status, ['vigente', 'historico', 'reemplazado'], true) ? $status : 'vigente';
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
        return trim($value, '-');
    }
}
