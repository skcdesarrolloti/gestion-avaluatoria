<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalPhRepository;

final class AppraisalPhDocumentUploadService
{
    public function store(array $files, string $appraisalId, int $owner, string $typology,
        AppraisalPhRepository $repo): array
    {
        $uploads = $this->files($files);
        if (!$uploads) throw new \RuntimeException('Selecciona al menos un soporte PH para analizar.');
        $texts = []; $names = []; $stored = [];
        foreach ($uploads as $file) {
            $name = mb_substr(basename(str_replace('\\', '/', (string) $file['name'])), 0, 220);
            if ((int) $file['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException(($name ?: 'El soporte PH') . ' '
                    . AppraisalPhDocumentStorage::uploadErrorMessage((int) $file['error']));
            }
            $info = AppraisalPhDocumentStorage::inspect((string) $file['tmp_name'], $name);
            $id = bin2hex(random_bytes(16));
            $storageName = 'ph-' . $appraisalId . '-' . $id . '.' . $info['extension'];
            $bytes = AppraisalPhDocumentStorage::storeUploaded((string) $file['tmp_name'],
                AppraisalPhDocumentStorage::path($storageName));
            $path = AppraisalPhDocumentStorage::path($storageName);
            [$text, $readNames] = $info['extension'] === 'zip'
                ? $this->zipText($path, $name)
                : $this->singleText($path, $name, $info['extension']);
            $texts[] = $text; $names = array_merge($names, $readNames);
            $blob = file_get_contents($path);
            if (!is_string($blob)) throw new \RuntimeException('El soporte PH se guardó, pero no quedó respaldado.');
            $stored[] = ['id' => $id, 'source_filename' => $name, 'storage_filename' => $storageName,
                'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'extracted_chars' => mb_strlen($text),
                'file_blob' => $blob];
        }
        $analysis = (new AppraisalPhDocumentAnalyzer())->analyze(trim(implode("\n\n", array_filter($texts))),
            $names, $typology);
        foreach ($stored as $file) {
            $repo->addDocument($appraisalId, $owner, $file + [
                'analysis_status' => 'Lectura preliminar',
                'analysis_message' => $analysis['summary'],
            ]);
        }
        $repo->mergeAnalysis($appraisalId, $owner, $analysis);
        return $analysis;
    }

    private function zipText(string $path, string $name): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) throw new \RuntimeException("$name no es un ZIP válido.");
        $texts = []; $names = [];
        for ($i = 0; $i < $zip->numFiles && count($names) < 80; $i++) {
            $entry = $zip->getNameIndex($i);
            if (!is_string($entry) || str_ends_with($entry, '/')) continue;
            $ext = mb_strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'webp', 'tif', 'tiff'], true)) continue;
            $stream = $zip->getStream($entry);
            if (!$stream) continue;
            $tmp = tempnam(sys_get_temp_dir(), 'ga_ph_zip_');
            $out = fopen($tmp, 'wb');
            if (!$out) { fclose($stream); continue; }
            stream_copy_to_stream($stream, $out); fclose($out); fclose($stream);
            $names[] = basename(str_replace('\\', '/', $entry));
            $texts[] = (new LegalCertificateTextExtractor())->extract($tmp, $ext);
            @unlink($tmp);
        }
        $zip->close();
        return [trim(implode("\n\n", array_filter($texts))), $names ?: [$name]];
    }

    private function singleText(string $path, string $name, string $extension): array
    {
        return [(new LegalCertificateTextExtractor())->extract($path, $extension), [$name]];
    }

    private function files(array $files): array
    {
        $name = $files['name'] ?? null;
        if (!is_array($name)) {
            return trim((string) $name) === '' ? [] : [[
                'name' => (string) ($files['name'] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'] ?? ''),
                'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
            ]];
        }
        $items = [];
        foreach ($name as $index => $value) {
            if (trim((string) $value) === '' && (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $items[] = ['name' => (string) $value, 'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return array_slice($items, 0, 80);
    }
}
