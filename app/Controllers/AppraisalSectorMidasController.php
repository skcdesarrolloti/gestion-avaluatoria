<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalSectorRepository, AppraisalSectorSectionRepository,
    AppraisalSectorMidasFileRepository, AppraisalSubjectRepository, SectorBankRepository};
use App\Services\{AppraisalMidasReview, AppraisalMidasSupportUploadService, AppraisalSectorAdvancedPrefill};
use App\Support\AppraisalSectorAdvancedCatalog;

final class AppraisalSectorMidasController
{
    public function __construct(
        private AppraisalRepository $appraisals, private AppraisalSectorRepository $sectors,
        private AppraisalSectorSectionRepository $sections, private AppraisalSubjectRepository $subjects,
        private SectorBankRepository $bank, private AppraisalSectorMidasFileRepository $midasFiles, private array $user
    ) {}

    public function consult(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $subject = $this->subjects->find($id, $this->user['id']);
            if (empty($subject['neighborhood_id'])) throw new \InvalidArgumentException('Primero carga el barrio.');
            $suggestions = AppraisalMidasReview::suggestions($subject);
            $stats = AppraisalMidasReview::stats($suggestions);
            $diagnostics = AppraisalMidasReview::diagnostics();
            Session::flash('sector_midas_review', '1');
            Session::flash('sector_message', 'Consulta MIDAS preparada: se encontraron '
                . $stats['fields'] . ' datos sugeridos en ' . $stats['sections'] . ' pestañas. '
                . ($diagnostics ? $diagnostics[0] . ' ' : '') . 'Revisa antes de aplicar.');
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/sector#midas-review');
    }

    public function uploadSupport(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $subject = $this->subjects->find($id, $this->user['id']);
            $record = (new AppraisalMidasSupportUploadService())->store($_FILES['midas_support'] ?? [],
                $id, $this->user['id'], (string) ($subject['neighborhood_id'] ?? ''), $this->midasFiles,
                trim((string) ($_POST['layer_group'] ?? 'Otro soporte MIDAS')), (string) ($_POST['notes'] ?? ''));
            Session::flash('sector_midas_file_message', 'Soporte MIDAS cargado: ' . $record['source_filename'] . '.');
        } catch (\Throwable $error) {
            Session::flash('sector_midas_file_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/sector#midas-soportes');
    }

    public function supportFile(string $id, string $fileId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $file = $this->midasFiles->find($fileId, $id, $this->user['id']);
        $path = AppraisalSectorMidasFileRepository::path((string) $file['storage_filename']);
        $blob = $file['file_blob'] ?? null;
        if (!is_file($path) && !is_string($blob)) throw new \App\Core\HttpException(404, 'No se encontró el soporte MIDAS.');
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: ' . ($file['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename((string) $file['source_filename']) . '"');
        header('X-Content-Type-Options: nosniff');
        if (is_file($path)) {
            header('Content-Length: ' . filesize($path));
            readfile($path);
        } else {
            header('Content-Length: ' . strlen($blob));
            echo $blob;
        }
        exit;
    }

    public function apply(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $subject = $this->subjects->find($id, $this->user['id']);
            $neighborhoodId = (string) ($subject['neighborhood_id'] ?? '');
            if ($neighborhoodId === '') throw new \InvalidArgumentException('Primero carga el barrio.');
            $sector = $this->sectors->find($id, $this->user['id']);
            $merged = AppraisalMidasReview::mergeEmpty($this->current($id, $subject, $sector),
                AppraisalMidasReview::suggestions($subject));
            $this->sections->saveAll($id, $this->user['id'], $neighborhoodId, $merged);
            $this->bank->saveAdvancedSections($neighborhoodId, $merged);
            $this->bank->saveSnapshot($id, $this->user['id'], $neighborhoodId, $sector);
            Session::flash('sector_midas_review', '1');
            Session::flash('sector_message', 'Sugerencias MIDAS aplicadas solo en campos vacíos. Lo escrito manualmente se conservó.');
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/sector#midas-review');
    }

    private function current(string $id, array $subject, array $sector): array
    {
        $current = AppraisalSectorAdvancedPrefill::sections($subject, $sector);
        foreach ($this->sections->sections($id, $this->user['id']) as $code => $row) {
            $data = json_decode((string) ($row['data_json'] ?? '{}'), true);
            if (is_array($data)) $current[(string) $code] = array_replace($current[(string) $code] ?? [], $data);
        }
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [, $fields]) {
            foreach ($fields as [$field,, $type]) $current[(string) $code][$field] ??= $type === 'multiselect' ? [] : '';
        }
        return $current;
    }
}
