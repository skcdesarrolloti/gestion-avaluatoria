<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalLegalRepository;

final class AppraisalLegalCertificateUploadService
{
    public function store(array $files, string $appraisalId, int $owner, AppraisalLegalRepository $repo,
        string $clientText = ''): array
    {
        $file = $this->singleFile($files);
        $name = mb_substr(basename(str_replace('\\', '/', (string) $file['name'])), 0, 220);
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("$name " . AppraisalLegalCertificateStorage::uploadErrorMessage((int) $file['error']));
        }
        $info = AppraisalLegalCertificateStorage::inspect((string) $file['tmp_name'], $name);
        $id = bin2hex(random_bytes(16));
        $storageName = 'certificado-' . $appraisalId . '-' . $id . '.' . $info['extension'];
        $bytes = AppraisalLegalCertificateStorage::storeUploaded((string) $file['tmp_name'],
            AppraisalLegalCertificateStorage::path($storageName));
        $path = AppraisalLegalCertificateStorage::path($storageName);
        $serverText = (new LegalCertificateTextExtractor())->extract($path, $info['extension']);
        $clientText = $this->cleanClientText($clientText);
        $text = mb_strlen($clientText) > mb_strlen($serverText) ? $clientText : $serverText;
        $parsed = (new LegalCertificateParser())->parse($text, $name);
        $blob = file_get_contents($path);
        if (!is_string($blob)) throw new \RuntimeException('El certificado se guardó, pero no quedó respaldado.');
        $record = ['id' => $id, 'source_filename' => $name, 'storage_filename' => $storageName,
            'mime_type' => $info['mime'], 'file_size_bytes' => $bytes,
            'extracted_chars' => mb_strlen($text), 'analysis_status' => $parsed['status'],
            'analysis_message' => $parsed['message'], 'file_blob' => $blob];
        $repo->addCertificate($appraisalId, $owner, $record);
        $repo->mergeAnalysis($appraisalId, $owner, $id, $parsed['data'], $parsed['annotations'], $parsed['alerts'], $text);
        return ['record' => $record, 'parsed' => $parsed];
    }

    private function singleFile(array $files): array
    {
        $name = $files['name'] ?? null;
        if (is_array($name)) {
            return ['name' => (string) ($files['name'][0] ?? ''), 'tmp_name' => (string) ($files['tmp_name'][0] ?? ''),
                'error' => (int) ($files['error'][0] ?? UPLOAD_ERR_NO_FILE)];
        }
        return ['name' => (string) ($files['name'] ?? ''), 'tmp_name' => (string) ($files['tmp_name'] ?? ''),
            'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE)];
    }

    private function cleanClientText(string $text): string
    {
        $text = function_exists('mb_scrub') ? mb_scrub($text, 'UTF-8') : $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/', "\n\n", $text) ?? $text;
        $text = trim($text);
        if (mb_strlen($text) < 200) return '';
        return preg_match('/matr|anotaci|folio|certificado|registro|supernotariado|departamento|municipio/iu', $text)
            ? $text : '';
    }
}
