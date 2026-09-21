<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, HttpException, Session};
use App\Models\{AppraisalPhRepository, AppraisalRepository};
use App\Services\{AppraisalPhChunkUploadService, AppraisalPhClientText, AppraisalPhDocumentReanalysisService, AppraisalPhDocumentStorage, AppraisalPhDocumentUploadService, AppraisalPhExternalOcrService, AppraisalPhInput};

final class AppraisalPhController
{
    public function __construct(private AppraisalRepository $appraisals, private AppraisalPhRepository $ph,
        private array $user) {}

    public function save(string $id): never
    {
        $this->saveAndRedirect($id, fn () => $this->ph->save($id, $this->user['id'], AppraisalPhInput::data(), $this->version()),
            'Propiedad horizontal guardada correctamente.');
    }

    public function autosave(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $version = $this->version();
        $this->ph->save($id, $this->user['id'], AppraisalPhInput::data(), $version);
        Http::json(['ok' => true, 'version' => $version + 1, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]);
    }

    public function upload(string $id): never
    {
        $this->saveAndRedirect($id, function () use ($id): string {
            $typology = (string) ($_POST['ph_typology'] ?? '');
            $clientText = AppraisalPhClientText::uploaded($_FILES['ph_client_text'] ?? []);
            $analysis = (new AppraisalPhDocumentUploadService())->store($_FILES['ph_document'] ?? [], $id,
                $this->user['id'], $typology, $this->ph, $clientText, $this->version());
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
        catch (HttpException $error) { throw $error; } catch (\Throwable $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 422); }
    }

    public function finishChunkUpload(string $id): never
    {
        $this->saveAndRedirect($id, function () use ($id): string {
            $file = (new AppraisalPhChunkUploadService())->finish($id, $this->user['id']);
            $typology = (string) ($_POST['ph_typology'] ?? '');
            $clientText = AppraisalPhClientText::uploaded($_FILES['ph_client_text'] ?? []);
            $analysis = (new AppraisalPhDocumentUploadService())->storePrepared($file, $id, $this->user['id'], $typology, $this->ph, $clientText, $this->version());
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
                ? 'Soporte PH eliminado. Se conservan los campos diligenciados.'
                : 'Soporte PH eliminado.');
        } catch (HttpException $error) { throw $error; } catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect($this->safeReturn($id));
    }

    public function documentText(string $id, string $documentId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $document = $this->ph->documentForAnalysis($documentId, $id, $this->user['id']);
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        echo (string) ($document['extracted_text'] ?? '');
        exit;
    }

    public function loadDocument(string $id, string $documentId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $typology = (string) ($_POST['ph_typology'] ?? ($this->ph->profile($id, $this->user['id'])['ph_typology'] ?? ''));
            $analysis = (new AppraisalPhDocumentReanalysisService())->reanalyze($documentId, $id,
                $this->user['id'], $typology, $this->ph, $this->version());
            $message = ($analysis['has_text'] ?? true) === false
                ? 'Soporte PH cargado, pero no se extrajo texto útil para diligenciar campos.'
                : 'Soporte PH cargado en la ficha. Se llenaron los campos vacíos sugeridos.';
            Session::flash('ph_message', $message);
            $this->flashDocumentAction($documentId, 'ok', $message);
        } catch (HttpException $error) { throw $error; } catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect($this->safeReturn($id));
    }

    public function externalOcr(string $id, string $documentId): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $typology = (string) ($_POST['ph_typology'] ?? ($this->ph->profile($id, $this->user['id'])['ph_typology'] ?? ''));
            $analysis = (new AppraisalPhExternalOcrService())->reanalyze($documentId, $id,
                $this->user['id'], $typology, $this->ph, $this->version());
            $message = ($analysis['has_text'] ?? false)
                ? 'OCR externo aplicado. Se llenaron los campos vacíos sugeridos.'
                : 'El OCR externo no devolvió texto útil para diligenciar campos.';
            Session::flash('ph_message', $message);
            $this->flashDocumentAction($documentId, 'ok', $message);
        } catch (HttpException $error) { throw $error; } catch (\Throwable $error) {
            $message = 'No se ejecutó la lectura IA/OCR: ' . $error->getMessage();
            Session::flash('ph_error', $message);
            $this->flashDocumentAction($documentId, 'error', $message);
        }
        Http::redirect($this->safeReturn($id));
    }

    private function flashDocumentAction(string $documentId, string $tone, string $message): void
    {
        Session::flash('ph_action_document_id', $documentId);
        Session::flash('ph_action_tone', $tone);
        Session::flash('ph_action_message', $message);
    }

    private function saveAndRedirect(string $id, callable $save, string $message): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $customMessage = $save();
            Session::flash('ph_message', is_string($customMessage) && $customMessage !== '' ? $customMessage : $message);
        }
        catch (HttpException $error) { throw $error; } catch (\Throwable $error) { Session::flash('ph_error', $error->getMessage()); }
        Http::redirect($this->safeReturn($id));
    }

    private function version(): int
    {
        $value = $_POST['version'] ?? '';
        if (!is_scalar($value) || !ctype_digit((string) $value)) throw new HttpException(422, 'Falta la versión de la ficha PH.');
        return (int) $value;
    }

    private function safeReturn(string $id): string
    {
        $target = (string) ($_POST['return_to'] ?? '');
        return $target === 'avaluos/' . $id . '/bien-sujeto#ph' ? $target : 'avaluos/' . $id . '/bien-sujeto#ph';
    }
}
