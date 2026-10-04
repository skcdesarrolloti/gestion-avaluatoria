<?php
declare(strict_types=1);
namespace App\Services;

/** Read-only snapshot: no adoption of values, merging or statistical fitting. */
final class ResearchPlanEvidence
{
    public static function build(array $catalog,array $unit,array $rows,array $context,array $subject=[]): array
    {
        $definitions=\App\Support\AppraisalFunctionalVariableCatalog::definitions();
        $attributes=json_decode((string)($unit['special_attributes_json'] ?? '{}'),true) ?: [];
        $subjects=[];
        foreach ($catalog as $key=>$factor) {
            $raw=$unit[$factor['subject']] ?? '';
            if (in_array($key,['built','area'],true) && $factor['subject']==='built_area_adopted_m2' && ($raw==='' || $raw===null)) $raw=$unit['area_built_m2'] ?? '';
            if ($key==='stratum' && (MarketSubjectEvidence::decode($unit)['identity_scope'] ?? '')==='sujeto') $raw=$subject['stratum'] ?? '';
            if ($key==='floor') $raw=\App\Support\AppraisalSpecialAttributeOptions::floor()[$attributes['piso_altura']['value'] ?? ''] ?? '';
            if ($key==='elevator') $raw=\App\Support\AppraisalSpecialAttributeOptions::level()[$attributes['ascensores']['value'] ?? $attributes['ascensores_edificio']['value'] ?? ''] ?? '';
            if ($key==='generator') {
                $attribute=match(ComparablePortalProfiles::defaultType((string)($context['tipo_inmueble'] ?? ''))) {
                    'apartamento','casa'=>'planta_electrica_vivienda','hotel'=>'planta_electrica_hotel',default=>'planta_electrica_oficina',
                };
                $raw=\App\Support\AppraisalSpecialAttributeOptions::generatorCoverage()[$attributes[$attribute]['value'] ?? ''] ?? '';
            }
            if ($key==='destination') $raw=MarketSubjectEvidence::decode($unit)['observed_use'] ?? '';
            if (isset($definitions[$factor['subject']]['options'])) $raw=$definitions[$factor['subject']]['options'][$raw] ?? $raw;
            $subjects[$key]=(string)($raw ?? '');
        }
        $groups=[];
        $excluded=0;
        $operation=($context['tipo_negocio'] ?? '')==='arriendo'?'Arriendo':'Venta';
        $type=ComparablePortalProfiles::defaultType((string)($context['tipo_inmueble'] ?? ''));
        foreach (ComparableIntake::groups($rows) as $id=>$ads) {
            $items=[]; $contextPending=false;
            foreach ($ads as $row) {
                if (in_array($row['intake_state'] ?? '',['not_selected'],true) || ($row['status'] ?? '')==='descartada') continue;
                $rowType=ComparablePortalProfiles::defaultType((string)($row['property_type'] ?? ''));
                // Unknown context is retained as pending, never counted as confirmed comparability.
                $contextPending=$contextPending || ($row['operation'] ?? '')!==$operation || $rowType!==$type;
                if (($context['regimen_ph'] ?? '')==='si') $contextPending=$contextPending || ($row['ph_regime'] ?? '')!=='si';
                if (($context['regimen_ph'] ?? '')==='no') $contextPending=$contextPending || ($row['ph_regime'] ?? '')!=='no';
                $values=[];
                foreach ($catalog as $key=>$factor) {
                    $raw=$row[$factor['sample']] ?? '';
                    if ($key==='area' && ($context['regimen_ph'] ?? '')!=='si') $raw=$row['built_m2'] ?? '';
                    $values[$key]=(string)($raw ?? '');
                }
                $items[]=['portal'=>trim((string)($row['source_name'] ?? '')) ?: 'Fuente pendiente','values'=>$values,
                    'id'=>(string)($row['id'] ?? ''),'code'=>(string)($row['listing_code'] ?? ''),
                    'url'=>(string)($row['source_url'] ?? ''),'publishedArea'=>(string)($row['area_m2'] ?? ''),
                    'areaBasis'=>(string)($row['area_basis'] ?? ''),
                    'revision'=>trim((string)($row['source_updates'] ?? ''))!==''];
            }
            if ($items===[]) { $excluded++; continue; }
            $first=$ads[0];
            $groups[]=['id'=>(string)$id,'title'=>(string)(($first['project_name'] ?? '') ?: ($first['property_type'] ?? 'Inmueble')),
                'ads'=>$items,'contextPending'=>$contextPending];
        }
        return ['subjects'=>$subjects,'groups'=>$groups,'excluded'=>$excluded,'operation'=>$operation];
    }
}
