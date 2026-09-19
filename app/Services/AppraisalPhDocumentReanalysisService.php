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
        $storedText = trim((string) ($document['extracted_text'] ?? ''));
        $resolver = new AppraisalPhDocumentFileResolver();
        $path = $storedText === '' ? $resolver->path($document) : '';
        try {
            if ($storedText !== '') {
                [$text, $names] = [$storedText, [$name]];
            } else {
                $extension = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if ($extension === '') $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
                [$text, $names] = (new AppraisalPhDocumentUploadService())->readStoredText($path, $name, $extension);
            }
            $analysis = (new AppraisalPhDocumentAnalyzer())->analyze($text, $names, $typology);
            $repo->updateDocumentAnalysis($documentId, $appraisalId, $owner, mb_strlen($text),
                (string) ($analysis['summary'] ?? 'Lectura preliminar PH.'), $text);
            $repo->mergeAnalysis($appraisalId, $owner, $analysis);
            return $analysis;
        } finally {
            if ($path !== '' && $resolver->temporary($path)) @unlink($path);
        }
    }
}
