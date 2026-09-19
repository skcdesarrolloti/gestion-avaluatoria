<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalPhRepository, AppraisalRepository};
use App\Services\{AppraisalPhChunkUploadService, AppraisalPhDocumentReanalysisService, AppraisalPhDocumentStorage, AppraisalPhDocumentUploadService, AppraisalPhInput};

final class AppraisalPhController
{
    public function __construct(private AppraisalRepository $appraisals, private AppraisalPhRepository $ph,
        private array $user) {}

    public function save(string $id): never
    {
        $this->saveAndRedirect($id, fn () => $this->ph->save($id, $this->user['id'], AppraisalPhInput::data()),
            'Propiedad horizontal guardada correctamente.');
    }

    public function autosave(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $this->ph->save($id, $this->user['id'], AppraisalPhInput::data());
        Http::json(['ok' => true, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]);
    }

    public function upload(string $id): never
    {
        $this->saveAndRedirect($id, function () use ($id): string {
            $typology = (string) ($_POST['ph_typology'] ?? '');
            $analysis = (new AppraisalPhDocumentUploadService())->store($_FILES['ph_document'] ?? [], $id,
                $this->user['id'], $typology, $this->ph);
            if (($analysis['message'] ?? '') !== '') return (string) $analysis['message'];
            return ($analysis['has_text'] ?? true) === false
                ? 'Soporte PH cargado, pero no se extrajo texto útil para diligenciar campos.'
                : '';
        }, 'Soporte PH analizado. Se llenaron los campos vacíos sugeridos por la lectura preliminar.');
    }

    public function uploadChunk(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try { Http::json((new AppraisalPhChunkUploadService())->store($id, $this->user['id'])); }
        catch (\Throwable $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 422); }
    }

    public function finishChunkUpload(string $id): never
    {
        $this->saveAndRedirect($id, function () use ($id): string {
            $file = (new AppraisalPhChunkUploadService())->finish($id, $this->user['id']);
            $typology = (string) ($_POST['ph_typology'] ?? '');
            $analysis = (new AppraisalPhDocumentUploadService())->storePrepared($file, $id, $this->user['id'], $typology, $this->ph);
            if (($analysis['message'] ?? '') !== '') return (string) $analysis['message'];
            return ($analysis['has_text'] ?? true) === false
                ? 'Soporte PH cargado, pero no se extrajo texto útil para diligenciar campos.'
                : '';
        }, 'Soporte PH analizado. Se llenaron los campos vacíos sugeridos por la lectura preliminar.');
    }

    public function deleteDocument(string $id, string $documentId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $result = $this->ph->deleteDocument($documentId, $id, $this->user['id']);
            $path = $result['filename'] !== '' ? AppraisalPhDocumentStorage::path($result['filename']) : '';
            if ($path !== '' && is_file($path)) @unlink($path);
            Session::flash('ph_message', $result['cleared']
                ? 'Soporte PH eliminado. Se limpió la lectura automática porque no quedan soportes cargados.'
                : 'Soporte PH eliminado.');
        } catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#ph');
    }

    public function loadDocument(string $id, string $documentId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $typology = (string) ($_POST['ph_typology'] ?? ($this->ph->profile($id, $this->user['id'])['ph_typology'] ?? ''));
            $analysis = (new AppraisalPhDocumentReanalysisService())->reanalyze($documentId, $id,
                $this->user['id'], $typology, $this->ph);
            Session::flash('ph_message', ($analysis['has_text'] ?? true) === false
                ? 'Soporte PH cargado, pero no se extrajo texto útil para diligenciar campos.'
                : 'Soporte PH cargado en la ficha. Se llenaron los campos vacíos sugeridos.');
        } catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#ph');
    }

    private function saveAndRedirect(string $id, callable $save, string $message): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $customMessage = $save();
            Session::flash('ph_message', is_string($customMessage) && $customMessage !== '' ? $customMessage : $message);
        }
        catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#ph');
    }
}
