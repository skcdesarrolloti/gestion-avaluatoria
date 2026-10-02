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
        private \App\Models\MethodologyWorkflowRepository $workflow) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $units = $this->appraisals->units($id, $this->user['id']);
        $phProfile = $this->ph->profile($id, $this->user['id']);
        $comparableRows = $this->comparables->forAppraisal($id, $this->user['id']);
        $allComparableRows = $comparableRows;
        $components = \App\Services\MethodologyWorkflow::components($record, $units);
        $componentKey = is_string($_GET['component'] ?? '') ? ($_GET['component'] ?? '') : '';
        \App\Services\MethodologyWorkflow::validateKey($componentKey, $components);
        $comparableRows = \App\Services\MethodologyComparableScope::rows($allComparableRows, $componentKey);
        $flow = \App\Services\MethodologyWorkflow::saved($record);
        $selected = $flow[$componentKey] ?? [];
        $method = is_string($_GET['method'] ?? null) ? $_GET['method'] : 'mercado';
        if (!isset(\App\Services\MethodologyWorkflow::METHODS[$method])) $method = 'mercado';
        $stage = is_string($_GET['stage'] ?? null) ? $_GET['stage'] : 'components';
        if (!in_array($stage, ['components', 'integration', 'decision', 'report', '1', '2', '3', '4', '5'], true)) $stage = 'components';
        $searchRecord = \App\Services\ComparableSearchContext::record($record, $units, $componentKey);
        $methodologyChapter = (new AppraisalMethodologyChapterReport())->build($record, $subject, $units);
        $marketNeighborhoods = array_map(static function (array $row): array {
            try { $url = \App\Services\FincaraizAreaSearch::url((string) $row['name']); }
            catch (\InvalidArgumentException) { $url = ''; }
            return $row + ['search_url' => $url];
        }, $this->geo->activeNeighborhoodsForCity((string) ($subject['city_id'] ?? '')));
        view('appraisals/valuation-methodology', ['title' => 'Metodología valuatoria',
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
        if (isset($changes['analysis']) || isset($changes['conclusion'])) {
            if ((\App\Services\MethodologyWorkflow::saved($record)[$key]['method'] ?? '') !== 'mercado') throw new \App\Core\HttpException(422, 'Selecciona Mercado antes de guardar su análisis.');
            $rows = \App\Services\MethodologyComparableScope::rows($this->comparables->forAppraisal($id, $this->user['id']), $key);
            $changes['evidence_hash'] = \App\Services\MethodologyWorkflow::fingerprint($rows);
        }
        $next = $this->workflow->save($id, $this->user['id'], $version, $key, $changes);
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) \App\Core\Http::json(['ok' => true, 'version' => $next, 'saved_at' => gmdate('c')]);
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria?component=' . rawurlencode($key) . '&stage=2');
    }

    public function assignComparables(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $components = \App\Services\MethodologyWorkflow::components($record, $this->appraisals->units($id, $this->user['id']));
        $key = is_string($_POST['component'] ?? null) ? $_POST['component'] : '';
        \App\Services\MethodologyWorkflow::validateKey($key, $components);
        $source = is_string($_POST['source_scope'] ?? null) ? $_POST['source_scope'] : '';
        if ($source === $key) throw new \App\Core\HttpException(422, 'Selecciona un destino distinto.');
        $ids = $_POST['samples'] ?? [];
        if (!is_array($ids) || $ids === []) throw new \App\Core\HttpException(422, 'Marca las muestras que deseas asignar.');
        $rows = $this->comparables->forAppraisal($id, $this->user['id']);
        // An old synthetic or inactive source may be recovered only from this owned collection.
        if ($source !== '' && \App\Services\MethodologyComparableScope::rows($rows, $source) === []) throw new \App\Core\HttpException(422, 'La asignación anterior no contiene muestras de este expediente.');
        $found = [];
        foreach ($rows as &$row) if (in_array($row['id'], $ids, true)) {
            if (($row['component_key'] ?? '') !== $source) throw new \App\Core\HttpException(409, 'La muestra cambió de componente. Recarga para revisar.');
            $row['component_key'] = $key;
            $found[] = $row['id'];
        }
        unset($row);
        if (count(array_unique($ids)) !== count($found)) throw new \App\Core\HttpException(422, 'Una muestra no pertenece al expediente.');
        $this->comparables->saveAll($id, $this->user['id'], $rows, (int) ($_POST['version'] ?? -1));
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria?component=' . rawurlencode($key) . '&stage=3');
    }

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
    {
        $this->appraisals->find($id, $this->user['id']);
        if (($_POST['matrix_complete'] ?? '') !== '1') throw new \App\Core\HttpException(422, 'El envío de la matriz llegó incompleto; no se guardaron cambios.');
        $version = filter_var($_POST['version'] ?? null, FILTER_VALIDATE_INT);
        if ($version === false || $version === null || $version < 0) throw new \App\Core\HttpException(422, 'Falta la versión de la matriz. Recarga antes de guardar.');
        $key = is_string($_POST['component_scope'] ?? null) ? $_POST['component_scope'] : '';
        $components = \App\Services\MethodologyWorkflow::components($this->appraisals->find($id, $this->user['id']), $this->appraisals->units($id, $this->user['id']));
        \App\Services\MethodologyWorkflow::validateKey($key, $components);
        $rows = \App\Services\MethodologyComparableScope::merge($this->comparables->forAppraisal($id, $this->user['id']), AppraisalComparableInput::rows($_POST), $key);
        return $this->comparables->saveAll($id, $this->user['id'], $rows, $version);
    }

    public function readComparable(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        (new \App\Services\RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume('comparable-reader:' . $this->user['id'], 60, 900);
        try {
            $url = $_POST['source_url'] ?? '';
            if (!is_string($url)) throw new \InvalidArgumentException('El enlace debe ser texto.');
            $result = (new \App\Services\FincaraizListingReader())->read($url);
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
        (new \App\Services\RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume('comparable-search:' . $this->user['id'], 30, 900);
        try {
            $city = mb_strtolower(trim((string) ($subject['city_name'] ?? '')) ?: trim((string) ($record['municipio'] ?? '')));
            if (($record['tipo_inmueble'] ?? '') !== 'oficina' || ($record['tipo_negocio'] ?? '') !== 'venta'
                || !in_array($city, ['cartagena', 'cartagena de indias'], true)) {
                throw new \InvalidArgumentException('La búsqueda por barrio está disponible para oficinas en venta en Cartagena. Usa la lectura individual para otros casos.');
            }
            $neighborhoodId = $_POST['neighborhood_id'] ?? '';
            $page = filter_var($_POST['page'] ?? 1, FILTER_VALIDATE_INT);
            if (!is_string($neighborhoodId) || $page === false) throw new \InvalidArgumentException('Barrio o página inválidos.');
            $neighborhood = $this->geo->marketNeighborhood($neighborhoodId, (string) ($subject['city_id'] ?? ''));
            $portal = $_POST['portal'] ?? 'fincaraiz';
            if (!in_array($portal, ['fincaraiz', 'metrocuadrado'], true)) throw new \InvalidArgumentException('Portal no admitido.');
            if ($portal === 'metrocuadrado') {
                if ($page !== 1) throw new \InvalidArgumentException('Metrocuadrado admite la lectura inicial del barrio.');
                Http::json(['ok' => true] + (new \App\Services\MetrocuadradoAreaSearch())->search($neighborhood));
            }
            Http::json(['ok' => true] + (new \App\Services\FincaraizAreaSearch())->search($neighborhood, $page));
        } catch (\App\Core\HttpException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], $error->status); }
        catch (\InvalidArgumentException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 422); }
        catch (\RuntimeException $error) { Http::json(['ok' => false, 'message' => $error->getMessage()], 502); }
    }
}
