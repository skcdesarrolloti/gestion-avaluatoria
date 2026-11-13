<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\AppraisalComparableRepository;
use App\Models\AppraisalPhRepository;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSubjectRepository;
use App\Services\AppraisalComparableInput;
use App\Services\AppraisalComparableSearchGuide;
use App\Services\AppraisalMethodologyChapterReport;
final class AppraisalValuationMethodologyController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalSubjectRepository $subjects, private AppraisalPhRepository $ph,
        private AppraisalComparableRepository $comparables,
        private AppraisalComparableSearchGuide $guide, private array $user,
        private \App\Models\GeoMasterRepository $geo,
        private \App\Models\MethodologyWorkflowRepository $workflow,
        private \App\Models\ResearchFactorScaleRepository $factorScales) {}
    public function show(string $id): void
    {
        header('Cache-Control: no-store');
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $units = $this->appraisals->units($id, $this->user['id']);
        $phProfile = $this->ph->profile($id, $this->user['id']);
        $components = \App\Services\MethodologyWorkflow::components($record, $units);
        $componentKey = is_string($_GET['component'] ?? '') ? ($_GET['component'] ?? '') : '';
        \App\Services\MethodologyWorkflow::validateKey($componentKey, $components);
        $stage = is_string($_GET['stage'] ?? null) ? $_GET['stage'] : 'plan';
        if ($componentKey === '' && in_array($stage, ['components', '1', '2'], true)
            && !($stage==='1' && ($_GET['consult_method'] ?? '')==='costo')) {
            $componentKey = (string)(array_key_first(\App\Services\MethodologyValuationPlan::working($components)) ?? '');
        }
        $flow = \App\Services\MethodologyWorkflow::saved($record);
        $selected = \App\Services\MethodStudyState::visible($flow[$componentKey] ?? []);
        $method = ($selected['method'] ?? '') ?: (is_string($_GET['method'] ?? null) ? $_GET['method'] : 'mercado');
        if (!isset(\App\Services\MethodologyWorkflow::METHODS[$method])) $method = 'mercado';
        $stage = is_string($_GET['stage'] ?? null) ? $_GET['stage'] : 'plan';
        if ($stage==='components') $stage='1';
        if (!in_array($stage, ['plan', 'components', 'integration', 'decision', 'report', '1', '2', '3', '4', '5'], true)) $stage = 'plan';
        if (!empty($components[$componentKey]['container']) && in_array($stage, ['components','1','2','3','4','5'], true)) $stage = 'plan';
        if ($stage==='1' && ($_GET['consult_method'] ?? '')==='costo') $method='costo';
        if (isset(\App\Services\MethodologyWorkflow::METHODS[$flow[$componentKey]['method'] ?? '']))
            $selected=\App\Services\MethodStudyState::visible($flow[$componentKey],\App\Services\ComparableStudy::context($record,$componentKey)['token']);
        $deferIncome=in_array($stage,['3','4','5'],true) && $method==='renta' && ($_GET['renta_paso'] ?? '')!=='capture';
        $allComparableRows=$deferIncome?[]:$this->comparables->forAppraisal($id,$this->user['id']);
        $comparableRows=\App\Services\MethodologyComparableScope::rows($allComparableRows,$componentKey,$record);
        $searchRecord = \App\Services\ComparableSearchContext::forMethod($record, $units, $componentKey, $method);
        $methodologyChapter = (new AppraisalMethodologyChapterReport())->build($record, $subject, $units);
        $searchOperation=\App\Services\ComparableStudy::operation($searchRecord['tipo_negocio'] ?? '') ?: 'venta';
        $marketNeighborhoods = $deferIncome?[]:array_map(static function (array $row) use ($searchOperation): array {
            try { $url = \App\Services\FincaraizAreaSearch::url((string) $row['name'],1,$searchOperation); }
            catch (\InvalidArgumentException) { $url = ''; }
            return $row + ['search_url' => $url];
        }, $this->geo->activeNeighborhoodsForCity((string) ($subject['city_id'] ?? '')));
        view('appraisals/valuation-methodology', ['title' => 'Metodología valuatoria',
            'factorScales'=>$deferIncome?[]:$this->factorScales->all($this->user['id']),
            'record' => $record, 'subject' => $subject, 'units' => $units, 'phProfile' => $phProfile,
            'methodologyChapter' => $methodologyChapter, 'comparableRows' => $comparableRows,
            'marketNeighborhoods' => $marketNeighborhoods,
            'components' => $components, 'componentKey' => $componentKey, 'flow' => $flow, 'selected' => $selected,
            'method' => $method, 'stage' => $stage, 'allComparableRows' => $allComparableRows,
            'guide' => $this->guide->build($searchRecord, $subject, $componentKey === '' ? $units : [$components[$componentKey]['unit']], $phProfile)]);
    }

    public function saveWorkflow(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $components = \App\Services\MethodologyWorkflow::components($record, $this->appraisals->units($id, $this->user['id']));
        $key = is_string($_POST['component'] ?? null) ? $_POST['component'] : '';
        \App\Services\MethodologyWorkflow::validateKey($key, $components);
        if ($key === '') throw new \App\Core\HttpException(422, 'Selecciona un componente.');
        $version = filter_var($_POST['version'] ?? null, FILTER_VALIDATE_INT);
        if ($version === false || $version === null || $version < 0) throw new \App\Core\HttpException(422, 'Versión inválida.');
        $changes = \App\Services\MethodologyWorkflow::input($_POST);
        if (isset($changes['research_plan'])) {
            $study=\App\Services\ComparableStudy::assertRequest($record,$key,$_POST);
            $changes['research_plan']['study_scope']=$study['token'];
            $previousPlan=\App\Services\MethodStudyState::visible(\App\Services\MethodologyWorkflow::saved($record)[$key] ?? [],$study['token'])['research_plan'] ?? [];
            if (count($changes)!==1) throw new \App\Core\HttpException(422,'Guarda el plan separado de la configuración del método.');
            $scopeRecord=\App\Services\ComparableSearchContext::forMethod($record,$this->appraisals->units($id,$this->user['id']),$key,
                \App\Services\MethodologyWorkflow::saved($record)[$key]['method'] ?? '');
            \App\Services\ResearchPlanInput::validateScope($changes['research_plan'],
                \App\Services\ComparablePortalProfiles::defaultType((string)($scopeRecord['tipo_inmueble'] ?? '')),
                \App\Services\MethodologyWorkflow::saved($record)[$key]['method'] ?? '',$components[$key]['part'] ?? '');
            \App\Services\ResearchAssessmentInput::validateScope($changes['research_plan'],\App\Services\MethodologyComparableScope::rows($this->comparables->forAppraisal($id,$this->user['id']),$key,$record),$previousPlan);
            \App\Services\ResearchFactorReference::validateFixed($changes['research_plan'],$previousPlan,$this->factorScales->all($this->user['id']));
        }
        if (isset($components[$key]['alternate_method'])) {
            if (isset($changes['additional_methods']) || (isset($changes['method']) && $changes['method']!==$components[$key]['alternate_method']))
                throw new \App\Core\HttpException(422, 'Configura los métodos desde el alcance original; este recorrido conserva su método de contraste.');
        }
        if (isset($changes['plan_parts'])) {
            if (count($changes) !== 1) throw new \App\Core\HttpException(422, 'Guarda la organización antes de definir el método de cada parte.');
            \App\Services\MethodologyValuationPlan::validateParts($record, $components[$key], $changes['plan_parts']);
        } else \App\Services\MethodologyValuationPlan::validateWorkKey($key, $components);
        if (isset($changes['analysis']) || isset($changes['conclusion'])) {
            \App\Services\ComparableStudy::assertRequest($record,$key,$_POST);
            if ((\App\Services\MethodologyWorkflow::saved($record)[$key]['method'] ?? '') !== 'mercado') throw new \App\Core\HttpException(422, 'Selecciona Mercado antes de guardar su análisis.');
            $rows = \App\Services\MethodologyComparableScope::rows($this->comparables->forAppraisal($id, $this->user['id']), $key,$record);
            $changes['evidence_hash'] = \App\Services\MethodologyWorkflow::fingerprint($rows);
        }
        $next = $this->workflow->save($id, $this->user['id'], $version, $key, $changes);
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) \App\Core\Http::json(['ok' => true, 'version' => $next, 'saved_at' => gmdate('c')]);
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria?component=' . rawurlencode($key) . '&stage=' .
            (isset($changes['plan_parts']) ? 'plan' : (isset($changes['research_plan'])?'3&research=1':'2')));
    }

    public function saveIncomeConcept(string $id): never { \App\Services\IncomeConceptAction::save($this->appraisals,$this->workflow,$this->user['id'],$id); }
    public function saveLease(string $id): never { \App\Services\LeaseAction::save($this->appraisals,$this->user['id'],$id); }
    public function saveLeaseAntecedent(string $id): never { \App\Services\LeaseAction::save($this->appraisals,$this->user['id'],$id,true); }
    public function saveFactorScale(string $id): never
    { \App\Services\ResearchFactorScaleAction::save($this->appraisals,$this->factorScales,$this->user['id'],$id); }

    public function savePhScope(string $id, string $unitId): never
    { \App\Services\MarketPhScopeAction::save($this->appraisals, $this->user['id'], $id, $unitId); }
    public function previewExcel(string $id): never
    { \App\Services\ComparableExcelAction::run($this->appraisals,$this->comparables,$this->user['id'],$id); }

    public function saveExcel(string $id): never
    { \App\Services\ComparableExcelAction::run($this->appraisals,$this->comparables,$this->user['id'],$id,true); }
    public function assignComparables(string $id): never
    { \App\Services\ComparableStudyAction::assign($this->appraisals,$this->comparables,$this->user['id'],$id); }

    public function saveComparables(string $id): never
    {
        try {
            $this->persistComparables($id);
            Session::flash('methodology_message', 'Comparables guardados correctamente.');
        } catch (\Throwable $error) {
            Session::flash('methodology_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria');
    }

    public function autosaveComparables(string $id): never
    {
        $version = $this->persistComparables($id);
        Http::json(['ok' => true, 'version' => $version, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]);
    }

    private function persistComparables(string $id): int
    { return \App\Services\ComparableStudyAction::persist($this->appraisals,$this->comparables,$this->user['id'],$id); }

    public function readComparable(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        (new \App\Services\RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume('comparable-reader:' . $this->user['id'], 60, 900);
        try {
            $url = $_POST['source_url'] ?? '';
            if (!is_string($url)) throw new \InvalidArgumentException('El enlace debe ser texto.');
            $result = (new \App\Services\ComparableDetailReader())->read($url);
            Http::json(['ok' => true] + $result);
        } catch (\InvalidArgumentException $error) {
            Http::json(['ok' => false, 'message' => $error->getMessage()], 422);
        } catch (\RuntimeException $error) {
            Http::json(['ok' => false, 'message' => $error->getMessage()], 502);
        }
    }

    public function searchComparables(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $key = is_string($_POST['component'] ?? null) ? $_POST['component'] : '';
        $units = $this->appraisals->units($id, $this->user['id']);
        $savedMethod = \App\Services\MethodologyWorkflow::saved($record)[$key]['method'] ?? 'mercado';
        $record = \App\Services\ComparableSearchContext::forMethod($record, $units, $key, $savedMethod);
        (new \App\Services\RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume('comparable-search:' . $this->user['id'], 30, 900);
        try {
            $city = mb_strtolower(trim((string) ($subject['city_name'] ?? '')) ?: trim((string) ($record['municipio'] ?? '')));
            if (($record['tipo_inmueble'] ?? '') !== 'oficina' || !in_array($record['tipo_negocio'] ?? '',['venta','arriendo'],true)
                || !in_array($city, ['cartagena', 'cartagena de indias'], true)) {
                throw new \InvalidArgumentException('La búsqueda por barrio está disponible para oficinas en venta o arriendo en Cartagena. Usa la lectura individual para otros casos.');
            }
            $neighborhoodId = $_POST['neighborhood_id'] ?? '';
            $page = filter_var($_POST['page'] ?? 1, FILTER_VALIDATE_INT);
            if (!is_string($neighborhoodId) || $page === false) throw new \InvalidArgumentException('Barrio o página inválidos.');
            $neighborhood = $this->geo->marketNeighborhood($neighborhoodId, (string) ($subject['city_id'] ?? ''));
            $portal = $_POST['portal'] ?? 'fincaraiz';
            if (!in_array($portal, ['fincaraiz', 'metrocuadrado'], true)) throw new \InvalidArgumentException('Portal no admitido.');
            if ($portal === 'metrocuadrado') {
                if (($record['tipo_negocio'] ?? '')!=='venta') throw new \InvalidArgumentException('Metrocuadrado conserva la búsqueda por barrio para ventas. En arriendos usa el portal y pega los resultados.');
                if ($page !== 1) throw new \InvalidArgumentException('Metrocuadrado admite la lectura inicial del barrio.');
                Http::json(['ok' => true] + (new \App\Services\MetrocuadradoAreaSearch())->search($neighborhood));
            }
            Http::json(['ok' => true] + (new \App\Services\FincaraizAreaSearch())->search($neighborhood, $page,$record['tipo_negocio']));
        } catch (\App\Core\HttpException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], $error->status); }
        catch (\InvalidArgumentException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 422); }
        catch (\RuntimeException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 502); }
    }
}
