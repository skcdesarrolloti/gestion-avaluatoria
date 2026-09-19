<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalPhRepository;

final class AppraisalPhDocumentReanalysisService
{
    public function reanalyze(string $documentId, string $appraisalId, int $owner, string $typology,
        AppraisalPhRepository $repo): array
    {
        $document = $repo->documentForAnalysis($documentId, $appraisalId, $owner);
        $name = (string) $document['source_filename'];
        $path = $this->path($document);
        $temporary = str_starts_with($path, sys_get_temp_dir());
        try {
            $extension = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($extension === '') $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
            [$text, $names] = (new AppraisalPhDocumentUploadService())->readStoredText($path, $name, $extension);
            $analysis = (new AppraisalPhDocumentAnalyzer())->analyze($text, $names, $typology);
            $repo->updateDocumentAnalysis($documentId, $appraisalId, $owner, mb_strlen($text),
                (string) ($analysis['summary'] ?? 'Lectura preliminar PH.'));
            $repo->mergeAnalysis($appraisalId, $owner, $analysis);
            return $analysis;
        } finally {
            if ($temporary) @unlink($path);
        }
    }

    private function path(array $document): string
    {
        $storage = (string) ($document['storage_filename'] ?? '');
        $path = $storage !== '' ? AppraisalPhDocumentStorage::path($storage) : '';
        if ($path !== '' && is_file($path)) return $path;
        $blob = $document['file_blob'] ?? null;
        if (!is_string($blob) || $blob === '') {
            throw new \RuntimeException('El soporte PH no tiene archivo físico ni respaldo interno disponible.');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'ga_ph_reload_');
        file_put_contents($tmp, $blob);
        return $tmp;
    }
}
