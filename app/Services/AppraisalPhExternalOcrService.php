<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalPhRepository;

final class AppraisalPhExternalOcrService
{
    public function __construct(private AppraisalExternalOcrClient $client = new AppraisalExternalOcrClient()) {}

    public function reanalyze(string $documentId, string $appraisalId, int $owner, string $typology,
        AppraisalPhRepository $repo): array
    {
        $document = $repo->documentForAnalysis($documentId, $appraisalId, $owner);
        $resolver = new AppraisalPhDocumentFileResolver();
        $path = $resolver->path($document);
        try {
            $name = (string) $document['source_filename'];
            $text = $this->client->extract($path, $name, (string) ($document['mime_type'] ?? 'application/octet-stream'));
            $analysis = (new AppraisalPhDocumentAnalyzer())->analyze($text, [$name], $typology);
            $repo->updateDocumentAnalysis($documentId, $appraisalId, $owner, mb_strlen($text),
                'Lectura externa IA/OCR aplicada.', $text);
            $repo->mergeAnalysis($appraisalId, $owner, $analysis);
            return $analysis;
        } finally {
            if ($resolver->temporary($path)) @unlink($path);
        }
    }
}
