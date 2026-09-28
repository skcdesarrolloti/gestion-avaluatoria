<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\MasterDocumentRepository;

final class MasterDocumentUploadService
{
    public function __construct(private MasterDocumentRepository $documents) {}

    public function upload(array $input, array $file, array $user): array
    {
        $name = basename(str_replace('\\', '/', (string) ($file['name'] ?? '')));
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($name . ' ' . MasterDocumentStorage::uploadErrorMessage($error));
        }
        if (!MasterDocumentStorage::isPdf((string) $file['tmp_name'], $name)) {
            throw new \RuntimeException('El documento maestro debe ser un PDF válido.');
        }
        $destinations = MasterDocumentRepository::destinations();
        $destination = (string) ($input['destination'] ?? '');
        if (!isset($destinations[$destination])) {
            throw new \InvalidArgumentException('Selecciona dónde se alojará el documento.');
        }
        $title = $this->text($input['title'] ?? '', 240);
        if ($title === '') throw new \InvalidArgumentException('Escribe el nombre del documento.');
        $code = $this->text($input['document_code'] ?? $title, 120);
        $storageName = bin2hex(random_bytes(10)) . '.pdf';
        $bytes = MasterDocumentStorage::storeUploaded((string) $file['tmp_name'], MasterDocumentStorage::path($storageName));
        $path = MasterDocumentStorage::path($storageName);
        $blob = file_get_contents($path);
        if (!is_string($blob)) throw new \RuntimeException('No se pudo conservar el respaldo interno del PDF.');
        $data = [
            'id' => bin2hex(random_bytes(16)),
            'slug' => $this->documents->uniqueSlug($this->slug($code . ' ' . $title)),
            'destination' => $destination,
            'document_code' => $code,
            'title' => $title,
            'document_type' => $this->text($input['document_type'] ?? 'Documento', 40),
            'version' => $this->text($input['version'] ?? '', 80),
            'effective_at' => $this->date($input['effective_at'] ?? ''),
            'status' => $this->status((string) ($input['status'] ?? 'vigente')),
            'source_url' => $this->text($input['source_url'] ?? '', 500),
            'summary' => $this->text($input['summary'] ?? '', 600),
            'topics' => $this->list($input['topics'] ?? ''),
            'modules' => $this->modules($input['modules'] ?? []),
            'source_filename' => $name,
            'storage_filename' => $storageName,
            'file_size_bytes' => $bytes,
            'pdf_blob' => $blob,
            'created_by' => $this->text($user['name'] ?? '', 120),
            'now' => gmdate('Y-m-d H:i:s'),
        ];
        $this->documents->store($data);
        return $data;
    }

    private function text(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }

    private function date(mixed $value): string
    {
        $value = trim((string) $value);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
    }

    private function status(string $status): string
    {
        return in_array($status, ['vigente', 'historico', 'derogado'], true) ? $status : 'vigente';
    }

    private function modules(mixed $values): array
    {
        $allowed = array_keys(MasterDocumentRepository::modules());
        $items = is_array($values) ? $values : [];
        return array_values(array_intersect(array_map('strval', $items), $allowed));
    }

    private function list(mixed $value): array
    {
        $parts = preg_split('/[,;\n]+/', (string) $value) ?: [];
        return array_values(array_filter(array_map(fn (string $v): string => $this->text($v, 80), $parts)));
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
        return trim($value, '-');
    }
}
