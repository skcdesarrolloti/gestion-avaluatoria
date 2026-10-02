<?php
declare(strict_types=1);
namespace App\Services;

final class MarketSubjectChecklist
{
    public function build(array $record, array $subject, array $unit, array $ph, array $flow = [], array $units = []): array
    {
        $data = MarketSubjectEvidence::effective($unit, $subject, $ph);
        $own = MarketSubjectEvidence::decode($unit);
        $inherit = ($own['identity_scope'] ?? '') === 'sujeto';
        $phRegime = (string) ($record['regimen_ph'] ?? '');
        $nature = $data['legal_nature'] ?? '';
        $rows = [MarketUnitDescriptionCheck::row($unit)];
        $add = static function (string $key, string $label, string $value, string $source, string $state,
            string $message, string $section = 'tipologias', string $detail = '') use (&$rows): void {
            $rows[] = compact('key', 'label', 'value', 'source', 'state', 'message', 'section', 'detail');
        };
        $legalSource = trim((string) ($data['legal_source'] ?? ''));
        $identityReady = in_array($data['identity_scope'] ?? '', ['propia', 'sujeto'], true) && $nature !== '' && $legalSource !== '';
        $add('identity', 'Identificación y naturaleza de esta unidad',
            ['privada'=>'Privada independiente', 'integrada'=>'Parte integrada de otra unidad', 'comun_exclusivo'=>'Común de uso exclusivo'][$nature] ?? 'Sin definir',
            '3.1 · ' . ($legalSource ?: 'Sin soporte'), $identityReady ? 'ok' : 'missing',
            $identityReady ? 'Origen vinculado a esta unidad.' : 'Diligencia origen, naturaleza jurídica y documento que identifica esta unidad.');

        $private = $phRegime === 'si' || ($unit['method_structure'] ?? '') === 'area_privada';
        $areaKey = $private ? 'area_private_m2' : 'area_adopted_m2';
        $area = MarketSubjectEvidence::number($unit[$areaKey] ?? '');
        $areaSource = trim((string) ($private ? ($unit['surface_source'] ?? '') : ($unit['area_adopted_source'] ?? '')));
        $areaReady = $area !== null && $area > 0 && $areaSource !== '';
        $areaState = $areaReady ? 'ok' : 'missing';
        $areaMessage = $areaReady ? 'Dato positivo y fuente registrados para esta unidad.' : 'Diligencia el área y su soporte en 3.2; no se toma el área de otra unidad.';
        $adopted = MarketSubjectEvidence::number($unit['area_adopted_m2'] ?? '');
        if ($private && ($unit['method_structure'] ?? '') === 'area_privada' && $area !== null && $adopted !== null
            && round($area, 2) !== round($adopted, 2)) {
            $areaState = 'difference'; $areaMessage = 'Área privada y superficie adoptada para área privada difieren: ' . $area . ' / ' . $adopted . ' m². Corrige o define el alcance en 3.2.';
        }
        $add('area', $private ? 'Área privada y soporte' : 'Área adoptada y fuente',
            $area === null ? 'Sin área válida' : $area . ' m²', '3.2 · ' . ($areaSource ?: 'Sin fuente'), $areaState, $areaMessage, 'surface', 'superficie');

        $observed = trim((string) ($data['observed_use'] ?? ''));
        $approved = trim((string) ($data['approved_use'] ?? ''));
        $useSource = trim((string) ($data['use_source'] ?? ''));
        $useReady = $observed !== '' && $approved !== '' && $useSource !== '';
        $useState = $useReady ? 'ok' : 'missing';
        $useMessage = 'Diligencia uso observado, uso aprobado y soporte. El uso potencial no prueba aprobación.';
        if ($useReady) {
            $same = MarketSubjectEvidence::normalized($observed) === MarketSubjectEvidence::normalized($approved);
            $explanation = trim((string) ($data['use_reconciliation'] ?? ''));
            $resolved = ($data['use_contrast'] ?? '') === 'compatible' && $explanation !== '';
            $useState = $same || $resolved ? 'ok' : 'difference';
            $useMessage = $same ? 'Los usos registrados coinciden.' : ($resolved ? 'Compatibilidad sustentada por el analista: ' . $explanation
                : 'Los usos difieren o tienen restricciones pendientes. Registra resultado del contraste y explicación en 3.1. ' . $explanation);
        }
        $add('use', 'Confrontación del uso observado y aprobado', 'Observado: ' . ($observed ?: 'Pendiente') . ' / aprobado: ' . ($approved ?: 'Pendiente'),
            '3.1' . ($inherit ? ' + ficha del sujeto / PH 3.5' : '') . ' · ' . ($useSource ?: 'Sin soporte'), $useState, $useMessage);

        $state = (string) ($unit['construction_state'] ?? '');
        $stateLabel = ['completa'=>'Completa', 'en_construccion'=>'En construcción', 'desmantelamiento'=>'En desmantelamiento', 'inconclusa'=>'Inconclusa'][$state] ?? '';
        $add('work_state', 'Estado de la obra', $stateLabel ?: 'Sin definir', '3.3 · Estado de obra',
            $stateLabel !== '' ? 'ok' : 'missing', $stateLabel !== '' ? 'Estado registrado para esta unidad.' : 'Diligencia el estado de obra en 3.3.', 'construction', 'estado');
        $result = json_decode((string) ($unit['conservation_result_json'] ?? '{}'), true);
        $items = is_array($result['items'] ?? null) ? $result['items'] : [];
        $supported = array_filter($items, static fn ($entry) => is_array($entry)
            && trim((string) ($entry['state_adopted'] ?? '')) !== '' && trim((string) ($entry['evidence'] ?? '')) !== '');
        $conservation = trim((string) ($result['state_global_adopted'] ?? ''));
        $conservationReady = $conservation !== '' && count($supported) > 0;
        $add('conservation', 'Estado de conservación y evidencia', ($conservation ?: 'Sin estado sustentado') . ' · ' . count($supported) . ' componente(s) con estado y evidencia',
            '3.3 · Conservación', $conservationReady ? 'ok' : 'missing', $conservationReady
                ? 'Estado global y evidencias registrados; consulta los componentes para evaluar suficiencia.'
                : 'Diligencia estado adoptado y evidencia de los componentes. Un texto generado sin evidencia no completa este control.', 'construction', 'conservacion');

        $registry = trim((string) ($data['registry'] ?? ''));
        $registryState = $identityReady && $registry !== '' ? 'ok' : 'missing';
        $registryMessage = $registryState === 'ok' ? 'Identificador y soporte vinculados.' : 'Diligencia matrícula o vínculo registral y soporte de esta unidad.';
        $subjectRegistry = trim((string) ($subject['property_registry'] ?? ''));
        if ($inherit && $registry !== '' && $subjectRegistry !== '' && MarketSubjectEvidence::normalized($registry) !== MarketSubjectEvidence::normalized($subjectRegistry)) {
            $registryState = 'difference'; $registryMessage = 'Matrícula propia ' . $registry . ' difiere de ficha del sujeto ' . $subjectRegistry . '. Revisa el origen seleccionado.';
        }
        foreach ($units as $other) {
            if (($other['id'] ?? '') === ($unit['id'] ?? '') || ($other['unit_kind'] ?? '') === 'common') continue;
            $otherData = MarketSubjectEvidence::effective($other, $subject, $ph);
            if ($nature === 'privada' && ($otherData['legal_nature'] ?? '') === 'privada' && $registry !== ''
                && MarketSubjectEvidence::normalized($registry) === MarketSubjectEvidence::normalized((string) ($otherData['registry'] ?? ''))) {
                $registryState = 'difference'; $registryMessage = 'Dos unidades declaradas independientes comparten matrícula: '
                    . ($other['label'] ?? 'otra unidad') . '. Corrige el vínculo o la naturaleza según soporte.';
            }
        }
        $add('registry', $nature === 'privada' ? 'Matrícula independiente' : 'Vínculo registral', $registry ?: 'Sin matrícula',
            '3.1 · ' . ($legalSource ?: 'Sin soporte'), $registryState, $registryMessage);
        if ($phRegime === 'no') {
            $add('coefficient', 'Coeficiente PH', 'No aplica: expediente NPH', 'Régimen del expediente', 'na', 'Sin exigir coeficiente PH.');
        } elseif ($nature === 'comun_exclusivo' && $identityReady) {
            $add('coefficient', 'Coeficiente propio del anexo', 'Común de uso exclusivo', '3.1 · ' . $legalSource, 'na', 'Se registra su vínculo con la unidad principal; no se inventa un coeficiente independiente.');
        } else {
            $coefficient = MarketSubjectEvidence::number($data['coefficient'] ?? '');
            $coefSource = trim((string) ($data['coefficient_source'] ?? ''));
            $coefReady = $phRegime === 'si' && $identityReady && $coefficient !== null && $coefficient > 0 && $coefficient <= 100 && $coefSource !== '';
            $coefState = $coefReady ? 'ok' : 'missing';
            $coefMessage = $coefReady ? 'Coeficiente y soporte vinculados a esta unidad.' : 'Diligencia coeficiente, soporte y vínculo; confirma primero el régimen PH.';
            $phCoef = MarketSubjectEvidence::number($ph['coefficient'] ?? '');
            if ($inherit && $coefficient !== null && $phCoef !== null && abs($coefficient - $phCoef) > 0.000001) {
                $coefState = 'difference'; $coefMessage = 'Coeficiente de unidad y ficha PH difieren: ' . $coefficient . ' / ' . $phCoef . ' %. Revisa el origen.';
            }
            $add('coefficient', 'Coeficiente y vínculo con esta unidad', $coefficient === null ? 'Sin coeficiente válido' : $coefficient . ' %',
                '3.1' . ($inherit ? ' + PH 3.5' : '') . ' · ' . ($coefSource ?: 'Sin soporte'), $coefState, $coefMessage);
        }
        $scope = trim((string) ($data['included_components'] ?? ''));
        $scopeSource = trim((string) ($data['scope_source'] ?? ''));
        $scopeState = $scope !== '' && $scopeSource !== '' ? 'ok' : 'missing';
        $scopeMessage = $scopeState === 'ok' ? 'Componentes y soporte registrados; el precio y sus ajustes se confrontan en insumos de Mercado.' : 'Diligencia componentes incluidos, exclusiones y soporte en 3.1.';
        if ($nature === 'comun_exclusivo' && ($flow['treatment'] ?? '') === 'separado') {
            $scopeState = 'difference'; $scopeMessage = 'Figura común de uso exclusivo en 3.1 y valor separado en capítulo 8. Corrige el dato de origen o el tratamiento.';
        }
        $registeredTreatment = ['integrado'=>'integrado','separado_mercado'=>'separado','reposicion'=>'separado','descriptivo'=>'descriptivo'][$unit['valuation_treatment'] ?? ''] ?? '';
        if ($registeredTreatment !== '' && !empty($flow['treatment']) && $registeredTreatment !== $flow['treatment']) {
            $scopeState = 'difference'; $scopeMessage = 'El tratamiento registrado en la composición y el adoptado en capítulo 8 difieren. Revisa qué unidad comprende cada valor.';
        }
        $add('scope', 'Componentes incluidos y tratamiento', $scope ?: 'Sin alcance documentado', '3.1 · ' . ($scopeSource ?: 'Sin soporte'), $scopeState, $scopeMessage);
        return ['rows'=>$rows, 'ok'=>count(array_filter($rows, static fn ($row) => $row['state'] === 'ok')),
            'pending'=>count(array_filter($rows, static fn ($row) => in_array($row['state'], ['missing','difference'], true)))];
    }
}
