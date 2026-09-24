<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalReportNoteRepository, AppraisalSubjectRepository, AppraisalUrbanNormRepository, UrbanNormativeRepository};
use App\Services\UrbanNormMidasUsageSearch;
use App\Support\{AppraisalReportNoteCatalog, UrbanNormativeAcademy};

final class AppraisalUrbanNormController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalUrbanNormRepository $profiles, private UrbanNormativeRepository $library,
        private AppraisalSubjectRepository $subjects, private array $user,
        private ?AppraisalReportNoteRepository $reportNotes = null) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        try { $notes = $this->reportNotes?->byChapter($id, $this->user['id'], '5') ?? []; }
        catch (\Throwable $error) { error_log('Gestion avaluatoria urbano notas ' . get_class($error)); $notes = []; }
        try { $profile = $this->profiles->profile($id, $this->user['id']); }
        catch (\Throwable $error) { error_log('Gestion avaluatoria urbano perfil ' . get_class($error)); $profile = []; }
        try { $documents = $this->library->documentsWithTables(); $categories = $this->library->categories(); }
        catch (\Throwable $error) { error_log('Gestion avaluatoria urbano biblioteca ' . get_class($error)); $documents = []; $categories = []; }
        try { $references = $this->profiles->references($id, $this->user['id']); }
        catch (\Throwable $error) { $references = []; }
        view('appraisals/urban-normative', [
            'title' => 'Normatividad urbana', 'record' => $record, 'subject' => $subject,
            'profile' => $this->prefilledProfile($profile, $subject),
            'urbanDocuments' => $documents,
            'urbanCategories' => $categories,
            'academyBlocks' => UrbanNormativeAcademy::blocks(),
            'sourceOptions' => UrbanNormativeAcademy::sourceOptions(),
            'useResults' => UrbanNormativeAcademy::useResults(),
            'references' => $references,
            'reportNotes' => $notes,
            'reportNoteSections' => AppraisalReportNoteCatalog::withNoteSections('5', $notes),
            'reportNoteChapter' => '5', 'reportNoteReturn' => 'avaluos/' . $id . '/normatividad-urbana',
            'urbanMessage' => Session::pullFlash('urban_norm_message'),
            'urbanError' => Session::pullFlash('urban_norm_error'),
        ]);
    }

    public function save(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->profiles->save($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $_POST);
            Session::flash('urban_norm_message', 'Normatividad urbana guardada correctamente.');
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        $target = (string) ($_POST['next'] ?? '') === 'deliverable'
            ? 'avaluos/' . $id . '/entregable' : 'avaluos/' . $id . '/normatividad-urbana';
        Http::redirect($target);
    }

    public function autosave(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $version = $this->profiles->save($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $_POST);
        Http::json(['ok' => true, 'version' => $version, 'saved_at' => gmdate('c')]);
    }

    public function consultMidas(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        try {
            $version = $this->profiles->save($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $_POST);
            $reference = trim((string) ($_POST['cadastral_reference'] ?? $subject['cadastral_reference'] ?? ''));
            $result = (new UrbanNormMidasUsageSearch())->consult($reference);
            if (!empty($result['fields'])) {
                $profile = $this->profiles->profile($id, $this->user['id']);
                $this->profiles->save($id, $this->user['id'], $version, array_replace($profile, $result['fields']));
            }
            $key = ($result['ok'] ?? false) ? 'urban_norm_message' : 'urban_norm_error';
            Session::flash($key, (string) ($result['message'] ?? 'Consulta MIDAS finalizada.'));
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/normatividad-urbana#midas');
    }

    private function prefilledProfile(array $profile, array $subject): array
    {
        if ((string) ($profile['cadastral_reference'] ?? '') === '') {
            $profile['cadastral_reference'] = (string) ($subject['cadastral_reference'] ?? '');
        }
        if ((string) ($profile['midas_query_option'] ?? '') === '') {
            $profile['midas_query_option'] = 'Uso del suelo';
        }
        if ((string) ($profile['current_use'] ?? '') === '') {
            $profile['current_use'] = (string) ($subject['permitted_use'] ?? '');
        }
        if ((string) ($profile['urban_treatment'] ?? '') === '') {
            $profile['urban_treatment'] = (string) ($subject['urban_treatment'] ?? '');
        }
        if ((string) ($profile['restrictions'] ?? '') === '') {
            $profile['restrictions'] = (string) ($subject['legal_urban_affectations'] ?? '');
        }
        return $profile;
    }
}
