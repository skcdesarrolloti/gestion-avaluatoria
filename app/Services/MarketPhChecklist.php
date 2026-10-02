<?php
declare(strict_types=1);
namespace App\Services;

final class MarketPhChecklist
{
    public static function apply(array $rows, array $unit, array $units, array $subject, array $ph, array $flow): array
    {
        $data = MarketSubjectEvidence::decode($unit);
        $nature = $data['legal_nature'] ?? '';
        $legal = trim((string) ($data['legal_source'] ?? ''));
        $documented = $legal !== '' && in_array($data['identity_scope'] ?? '', ['propia','sujeto'], true);
        $parent = MarketPhScope::parent($data, $unit, $units);
        $linked = ($unit['unit_kind'] ?? '') === 'annex' && in_array($nature, ['integrada','comun_exclusivo'], true);
        foreach ($rows as &$row) {
            if ($linked && $documented && $row['key'] === 'coefficient') {
                $row = array_replace($row, ['state'=>'na', 'value'=>'Sin coeficiente independiente', 'source'=>'3.1 · ' . $legal,
                    'message'=>'El vínculo con la principal se verifica en M2. No se duplica ni se suma su coeficiente al del anexo.']);
            }
            if ($nature === 'comun_exclusivo' && $documented && $row['key'] === 'area') {
                $row = array_replace($row, ['state'=>'na', 'label'=>'Área privada del común de uso exclusivo', 'value'=>'No aplica como área privada',
                    'source'=>'3.1 · ' . $legal, 'message'=>'Describe su superficie física y el derecho de uso en numeral 3; no se agrega al área privada de la principal.']);
                if ((MarketSubjectEvidence::number($unit['area_private_m2'] ?? '') ?? 0) > 0) {
                    $row['state'] = 'difference'; $row['value'] = $unit['area_private_m2'] . ' m² registrados como privados';
                    $row['message'] = 'La naturaleza documentada es común de uso exclusivo, pero hay área registrada como privada. Corrige la clasificación de la superficie en 3.2 según soporte; no se borra automáticamente.';
                }
            }
            if ($linked && $row['key'] === 'registry') {
                $parentData = $parent ? MarketSubjectEvidence::effective($parent, $subject, $ph) : [];
                $registry = trim((string) ($parentData['registry'] ?? ''));
                $parentSource = trim((string) ($parentData['legal_source'] ?? ''));
                $ready = $documented && $parent && $registry !== '' && $parentSource !== '';
                $row = array_replace($row, ['label'=>'Vínculo registral con la principal', 'state'=>$ready ? 'ok' : 'missing',
                    'value'=>($parent['label'] ?? 'Sin principal') . ' · ' . ($registry ?: 'Matrícula principal pendiente'),
                    'source'=>'3.1 + M2 · ' . ($legal ?: 'Sin soporte') . ' · ' . $parentSource,
                    'message'=>$ready ? 'Matrícula de la principal consultada por vínculo explícito; no exige matrícula independiente al anexo.'
                        : 'Define la principal en M2 y diligencia su matrícula y soporte en 3.1, más el soporte del vínculo del anexo.']);
                if (!$parent) $row['section'] = 'methodology';
                elseif ($registry === '' || $parentSource === '' || $ready) $row['target_unit'] = $parent['id'];
                $ownRegistry = trim((string) ($data['registry'] ?? ''));
                if ($nature === 'integrada' && $registry !== '' && $ownRegistry !== ''
                    && MarketSubjectEvidence::normalized($ownRegistry) !== MarketSubjectEvidence::normalized($registry)) {
                    $row['state'] = 'difference'; $row['message'] = 'El anexo se declara privado en la misma matrícula, pero su matrícula registrada difiere de la principal. Confronta los documentos.';
                }
            }
            if ($linked && $row['key'] === 'scope' && ($flow['treatment'] ?? '') === 'separado') {
                $row['state'] = 'difference'; $row['message'] = 'El anexo integrado o común de uso exclusivo figura con valor separado. En M2 define su inclusión en la principal; el análisis diferenciado no implica liquidación independiente.';
                $row['section'] = 'methodology';
            }
            if (($unit['unit_kind'] ?? '') === 'annex' && $nature === 'privada' && $row['key'] === 'scope'
                && ($flow['treatment'] ?? '') === 'integrado') {
                $row['state'] = 'difference'; $row['message'] = 'El anexo se declara privado con matrícula independiente, pero su tratamiento está integrado. Revisa en M2 su valoración global o por m² conforme al mercado y su integración final.';
                $row['section'] = 'methodology';
            }
            if ($row['key'] === 'scope' && ($flow['treatment'] ?? '') === '') {
                $row['state'] = 'missing'; $row['message'] = 'Define el tratamiento y alcance en M2 y conserva en 3.1 los componentes y su soporte.';
                $row['section'] = 'methodology';
            }
        }
        unset($row);
        $rows[] = MarketPhScope::row($unit, $units);
        return $rows;
    }
}
