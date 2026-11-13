<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\{AppraisalCatalog,AppraisalSubjectCatalog};

/** Consultas vivas al origen. No copia respuestas, decide áreas ni atribuye datos globales a partes. */
final class IncomeSubjectContext
{
    public static function build(array $record,array $subject,array $units,array $component,array $ph): array
    {
        $unit=$component['unit'] ?? [];
        $id=(string)($unit['id'] ?? '');
        $active=array_filter($units,static fn($item)=>($item['unit_kind'] ?? '')!=='common');
        $same=count($active)===1 && $id!=='' && (string)(reset($active)['id'] ?? '')===$id
            && ($unit['unit_kind'] ?? '')==='property' && empty($component['part']);
        $base='avaluos/'.($record['id'] ?? '');
        $subjectUrl=$base.'/bien-sujeto?unit='.rawurlencode($id);
        $evidence=MarketSubjectEvidence::effective($unit,$subject,$ph);
        $rows=[];
        $add=static function(string $label,mixed $value,string $source,string $section,string $support='') use (&$rows,$subjectUrl): void {
            $value=is_scalar($value)?trim((string)$value):'';
            $hash=['surface'=>'superficies','construction'=>'construccion'][$section] ?? 'ficha-basica';
            $rows[]=compact('label','value','source','support')+['url'=>$subjectUrl.'&section='.$section.'#'.$hash];
        };
        $add('Unidad y tipología',$component['label'] ?? $unit['label'] ?? '', '3.1 · Unidad del recorrido','tipologias');
        $add('Matrícula de la unidad',$evidence['registry'] ?? ($same?($subject['property_registry'] ?? ''):''),
            '3.1 · Identificación','tipologias',$evidence['legal_source'] ?? '');
        $observed=$evidence['observed_use'] ?? '';
        if ($observed==='' && $same) $observed=self::subjectLabel('current_use',$subject['current_use'] ?? '');
        $add('Uso observado',$observed,'3.1 · Uso de esta unidad','tipologias');
        $add('Uso aprobado documentado',$evidence['approved_use'] ?? '', '3.1 · Soporte de uso','tipologias',$evidence['use_source'] ?? '');
        $add('Componentes identificados en el sujeto',$evidence['included_components'] ?? '',
            '3.1 · Alcance registrado para Mercado; contrastar con Renta','tipologias',$evidence['scope_source'] ?? '');
        foreach (['area_private_m2'=>['Área privada (m²)','surface_source'],
            'area_adopted_m2'=>['Superficie adoptada (m²)','area_adopted_source'],
            'built_area_adopted_m2'=>['Área construida adoptada (m²)','built_area_adopted_source']] as $key=>[$label,$source])
            $add($label,$unit[$key] ?? '',str_starts_with($key,'built')?'3.3 · Construcción':'3.2 · Superficies',
                str_starts_with($key,'built')?'construction':'surface',(string)($unit[$source] ?? ''));
        $add('Descripción constructiva',$unit['construction_general_aspects'] ?? '', '3.3 · Construcción','construction');
        $add('Unidades rentables registradas',$unit['construction_rentable_units'] ?? '', '3.3 · Cantidad informada, sin identificar contratos','construction');
        $context=[];
        foreach (['finalidad'=>'Finalidad','tipo_derecho'=>'Derecho objeto del avalúo','value_date'=>'Fecha de valoración'] as $key=>$label)
            $context[]=['label'=>$label,'value'=>self::recordLabel($key,$record[$key] ?? ''),'source'=>'Encargo · Referencia del expediente',
                'support'=>'','url'=>$base.'/expediente'];
        foreach (['adopted_address'=>'Ubicación adoptada','neighborhood_name'=>'Barrio','current_occupation'=>'Ocupación global',
            'subject_reference_date'=>'Fecha de información del sujeto','restrictions'=>'Restricciones',
            'legal_urban_affectations'=>'Afectaciones jurídicas o urbanas'] as $key=>$label)
            $context[]=['label'=>$label,'value'=>self::subjectLabel($key,$subject[$key] ?? ''),'source'=>'3.1 · Contexto del expediente',
                'support'=>'','url'=>$subjectUrl];
        $occupancy=$same?(['propio_uso'=>'owner','arrendado'=>'rented_unspecified','desocupado'=>'vacant','adecuacion'=>'works'][$subject['current_occupation'] ?? ''] ?? ''):'';
        return ['rows'=>$rows,'context'=>$context,'occupancy'=>$occupancy,'same_scope'=>$same,'source_url'=>$subjectUrl,
            'physical'=>IncomeSubjectDetails::physical($unit,$record), 'economic'=>IncomeSubjectDetails::economic($record,$ph,$subject,$base),
            'references'=>[
                ['label'=>'3.4 · Características y soportes por tipología','url'=>$subjectUrl.'#factores'],
                ['label'=>'3.5 · PH, usos y restricciones de operación','url'=>$subjectUrl.'#ph'],
                ['label'=>'3.6 · Obsolescencias y evidencias','url'=>$subjectUrl.'#obsolescencias'],
                ['label'=>'3.7 · Registro fotográfico','url'=>$subjectUrl.'#fotos'],
            ]];
    }

    public static function subjectLabel(string $key,mixed $value): string
    { return is_scalar($value)?(AppraisalSubjectCatalog::selects()[$key][1][(string)$value] ?? (string)$value):''; }

    public static function occupancyConflict(string $source,string $saved): bool
    {
        if ($source==='' || $saved==='' || $saved==='pending' || $source===$saved) return false;
        return !($source==='rented_unspecified' && in_array($saved,['rented','partial','rented_unspecified'],true));
    }

    private static function recordLabel(string $key,mixed $value): string
    { return is_scalar($value)?(AppraisalCatalog::selectFields()[$key][4][(string)$value] ?? (string)$value):''; }
}
