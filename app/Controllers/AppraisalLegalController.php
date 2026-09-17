<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\Session;
use App\Models\AppraisalLegalRepository;
use App\Models\AppraisalRepository;
use App\Services\AppraisalLegalCertificateUploadService;
use App\Services\AppraisalLegalInput;
use App\Services\LegalCertificateParser;
use App\Services\LegalCertificateTextExtractor;
use App\Support\AppraisalLegalCatalog;

final class AppraisalLegalController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalLegalRepository $legal, private array $user) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/legal-characteristics', [
            'title' => 'Características jurídicas',
            'record' => $record,
            'profile' => $this->legal->profile($id, $this->user['id']),
            'certificates' => $this->legal->certificates($id, $this->user['id']),
            'legalGroups' => AppraisalLegalCatalog::groups(),
            'legalLabels' => AppraisalLegalCatalog::labels(),
            'legalMessage' => Session::pullFlash('legal_message'),
            'legalError' => Session::pullFlash('legal_error'),
        ]);
    }

    public function upload(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $result = (new AppraisalLegalCertificateUploadService())->store(
                $_FILES['legal_certificate'] ?? [], $id, $this->user['id'], $this->legal);
            $chars = (int) ($result['record']['extracted_chars'] ?? 0);
            Session::flash('legal_message', $chars > 0
                ? 'Certificado cargado y lectura preliminar preparada con ' . $chars . ' caracteres.'
                : 'Certificado cargado. No se leyó texto útil; revisa si requiere OCR o diligenciamiento manual.');
        } catch (\Throwable $error) {
            Session::flash('legal_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/caracteristicas-juridicas');
    }

    public function save(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->legal->saveManual($id, $this->user['id'], AppraisalLegalInput::data($_POST));
            Session::flash('legal_message', 'Numeral 4 guardado correctamente.');
        } catch (\Throwable $error) {
            Session::flash('legal_error', $error->getMessage());
        }
        $target = (string) ($_POST['next'] ?? '') === 'deliverable'
            ? 'avaluos/' . $id . '/entregable'
            : 'avaluos/' . $id . '/caracteristicas-juridicas';
        Http::redirect($target);
    }

    public function reanalyze(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $file = $this->legal->latestCertificate($id, $this->user['id']);
            $path = $this->certificatePath($file);
            $extension = mb_strtolower(pathinfo((string) $file['source_filename'], PATHINFO_EXTENSION));
            $text = (new LegalCertificateTextExtractor())->extract($path, $extension);
            $parsed = (new LegalCertificateParser())->parse($text, (string) $file['source_filename']);
            $this->legal->updateCertificateAnalysis((string) $file['id'], $id, $this->user['id'],
                mb_strlen($text), (string) $parsed['status'], (string) $parsed['message']);
            $this->legal->mergeAnalysis($id, $this->user['id'], (string) $file['id'],
                $parsed['data'], $parsed['annotations'], $parsed['alerts'], $text);
            Session::flash('legal_message', 'Último certificado reanalizado: ' . mb_strlen($text) . ' caracteres leídos.');
            if (!is_file(AppraisalLegalRepository::path((string) $file['storage_filename']))) @unlink($path);
        } catch (\Throwable $error) {
            Session::flash('legal_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/caracteristicas-juridicas');
    }

    public function certificate(string $id, string $certificateId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $file = $this->legal->findCertificate($certificateId, $id, $this->user['id']);
        $path = AppraisalLegalRepository::path((string) $file['storage_filename']);
        $blob = $file['file_blob'] ?? null;
        if (!is_file($path) && !is_string($blob)) {
            throw new \App\Core\HttpException(404, 'No se encontró el certificado.');
        }
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: ' . (string) $file['mime_type']);
        header('Content-Disposition: inline; filename="' . basename((string) $file['source_filename']) . '"');
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

    private function certificatePath(array $file): string
    {
        $path = AppraisalLegalRepository::path((string) $file['storage_filename']);
        if (is_file($path)) return $path;
        if (!is_string($file['file_blob'] ?? null)) {
            throw new \RuntimeException('El certificado no está disponible para reanalizar.');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'ga_ctl_');
        if ($tmp === false || file_put_contents($tmp, $file['file_blob']) === false) {
            throw new \RuntimeException('No se pudo preparar el certificado temporal.');
        }
        return $tmp;
    }
}
