<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalReportNoteRepository, AppraisalSubjectRepository, AppraisalUrbanNormRepository, UrbanNormativeRepository};
use App\Services\{AppraisalUrbanNormMidasFields, AppraisalUrbanNormScenarioInput, MidasPredioSearch, UrbanNormCategoryAdoption, UrbanNormMidasTextParser, UrbanNormMidasUsageSearch};
use App\Support\{AppraisalCatalog, AppraisalReportNoteCatalog, UrbanNormativeAcademy, UrbanNormativeScenarioCatalog};

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
            'urbanCategoryGroups' => $this->categoryGroups($categories),
            'urbanRouteGroups' => $this->routeGroups($categories),
            'propertyTypeLabel' => $this->propertyTypeLabel((string) ($record['tipo_inmueble'] ?? '')),
            'academyBlocks' => UrbanNormativeAcademy::blocks(),
            'sourceOptions' => UrbanNormativeAcademy::sourceOptions(),
            'useResults' => UrbanNormativeAcademy::useResults(),
            'normativeScenarioRoutes' => UrbanNormativeScenarioCatalog::routes(),
            'normativeScenarioResults' => UrbanNormativeScenarioCatalog::results(),
            'normativeScenarios' => AppraisalUrbanNormScenarioInput::decode($profile['normative_scenarios_json'] ?? ''),
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
            Session::flash('urban_norm_message', 'Cambios del numeral 5 guardados. La lectura MIDAS solo se actualiza con el botÃ³n Actualizar MIDAS.');
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        $target = (string) ($_POST['next'] ?? '') === 'deliverable'
            ? 'avaluos/' . $id . '/entregable'
            : 'avaluos/' . $id . '/normatividad-urbana' . $this->tabFragment((string) ($_POST['active_tab'] ?? ''));
        Http::redirect($target);
    }


    public function applyUseCategory(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $version = $this->profiles->save($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $_POST);
            $categorySlug = trim((string) ($_POST['category_slug'] ?? ''));
            if ($categorySlug === '') throw new \RuntimeException('Selecciona una categorÃ­a del cuadro antes de leerlo.');
            $category = $this->library->categoryWithRules($categorySlug);
            $profile = $this->profiles->profile($id, $this->user['id']);
            $fields = (new UrbanNormCategoryAdoption())->fields($category);
            $this->profiles->save($id, $this->user['id'], $version, array_replace($profile, $fields));
            Session::flash('urban_norm_message', 'Cuadro normativo leÃ­do y aplicado al numeral 5. Revisa la categorÃ­a y ajusta el criterio del perito si hace falta.');
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/normatividad-urbana#uso');
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
            $reference = $this->midasReference($_POST, $subject);
            $predio = (new MidasPredioSearch())->consult($reference);
            $midasFields = new AppraisalUrbanNormMidasFields($this->library);
            $fields = [];
            if (!empty($predio['predio'])) {
                $this->subjects->applyMidasPredio($id, $this->user['id'], $predio['predio']);
                $this->appraisals->applyMidasAreasToFirstUnit($id, $this->user['id'], $predio['predio']);
                $fields = $midasFields->profileFieldsFromPredio($predio['predio']);
                $reference = $midasFields->usageReference($predio['predio'], $reference);
            }
            $usage = (new UrbanNormMidasUsageSearch())->consult($reference);
            $fields = array_replace($fields, $usage['fields'] ?? []);
            $fields = $midasFields->withMatchedCategory($fields);
            if ($fields !== []) {
                $profile = $this->profiles->profile($id, $this->user['id']);
                $fields = array_filter($fields, static fn ($value): bool => trim((string) $value) !== '');
                $fields['midas_consulted'] = '1'; $fields['source_status'] = 'midas';
                $this->profiles->save($id, $this->user['id'], $version, array_replace($profile, $fields));
            }
            $ok = ($predio['ok'] ?? false) || ($usage['ok'] ?? false) || ($fields['category_slug'] ?? '') !== '';
            Session::flash($ok ? 'urban_norm_message' : 'urban_norm_error', $midasFields->consultMessage($predio, $usage, $fields));
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/normatividad-urbana#midas');
    }

    public function processMidasText(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $parsed = (new UrbanNormMidasTextParser())->parse((string) ($_POST['midas_pasted_text'] ?? ''));
            $predio = $parsed['predio']; $usage = $parsed['usage']; $raw = (string) $parsed['raw'];
            if ($raw === '' || ($predio === [] && $usage === [])) {
                throw new \RuntimeException('Pega la lectura completa de MIDAS antes de procesarla.');
            }
            $midasFields = new AppraisalUrbanNormMidasFields($this->library);
            $data = $midasFields->withMatchedCategory(array_replace($_POST, $usage, $midasFields->profileFieldsFromPredio($predio)));
            if ($predio !== []) $data['midas_predio_raw'] = $raw;
            if ($usage !== []) $data['midas_usage_raw'] = $raw;
            $data['midas_consulted'] = '1'; $data['source_status'] = 'midas';
            $this->profiles->save($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $data);
            if ($predio !== []) {
                $predio['_raw'] = $raw;
                $this->subjects->applyMidasPredio($id, $this->user['id'], $predio);
                $this->appraisals->applyMidasAreasToFirstUnit($id, $this->user['id'], $predio);
            }
            $msg = $predio !== [] && $usage !== []
                ? 'Lectura MIDAS procesada: datos del predio enviados al numeral 3 y reglamentaciÃ³n guardada en el numeral 5.'
                : ($predio !== [] ? 'Lectura del predio MIDAS enviada al numeral 3.' : 'ReglamentaciÃ³n de Uso Suelo guardada en el numeral 5.');
            Session::flash('urban_norm_message', $msg);
        } catch (\Throwable $error) { Session::flash('urban_norm_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/normatividad-urbana#midas');
    }

    private function tabFragment(string $tab): string
    { return in_array($tab, ['midas', 'uso', 'escenarios', 'determinantes', 'fuentes', 'cierre'], true) ? '#' . $tab : '#midas'; }

    private function routeGroups(array $categories): array
    {
        $labels = ['residencial' => 'Residencial', 'institucional' => 'Institucional / dotacional',
            'comercial' => 'Comercial', 'industrial' => 'Industrial', 'turistica' => 'Turistica / hotelera',
            'portuaria' => 'Portuaria', 'mixta' => 'Mixta', 'rural_suburbano' => 'Rural suburbana', 'rural' => 'Rural'];
        $groups = [];
        foreach ($categories as $cat) {
            $key = (string) ($cat['activity_group'] ?? '');
            $groups[$labels[$key] ?? ($key !== '' ? $key : 'Otras rutas')][] = $cat;
        }
        return $groups;
    }

    private function propertyTypeLabel(string $type): string
    { $options = AppraisalCatalog::selectFields()['tipo_inmueble'][4] ?? []; return (string) ($options[$type] ?? ($type !== '' ? $type : 'No definido')); }

    private function categoryGroups(array $categories): array
    {
        $groups = [];
        foreach ($categories as $cat) {
            $label = trim((string) ($cat['table_code'] ?? '') . ' Â· ' . (string) ($cat['table_title'] ?? ''), ' Â·');
            $groups[$label === '' ? 'Otros cuadros' : $label][] = $cat;
        }
        return $groups;
    }

    private function prefilledProfile(array $profile, array $subject): array
    {
        $subjectReference = (string) ($subject['cadastral_reference'] ?? '');
        if ($subjectReference !== '') {
            $profile['cadastral_reference'] = $subjectReference;
        }
        $digits = $this->digits($subjectReference);
        if ($digits !== '' && (string) ($profile['cadastral_reference_short'] ?? '') === ''
            && (string) ($profile['cadastral_reference_long'] ?? '') === '') {
            $profile[mb_strlen($digits) >= 20 ? 'cadastral_reference_long' : 'cadastral_reference_short'] = $digits;
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

    private function midasReference(array $input, array $subject): string
    {
        foreach (['cadastral_reference_long', 'cadastral_reference_short', 'cadastral_reference'] as $key) {
            $digits = $this->digits((string) ($input[$key] ?? ''));
            if ($digits !== '') return $digits;
        }
        return $this->digits((string) ($subject['cadastral_reference'] ?? ''));
    }

    private function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }


}

