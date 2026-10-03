<?php
declare(strict_types=1);
namespace App\Services;

/** Presence review, not adoption of prices, rights or a valuation. */
final class CostMethodReview
{
    public static function checks(array $unit, array $selected): array
    {
        $result=json_decode((string)($unit['conservation_result_json'] ?? '{}'),true);
        $state=is_array($result) ? (string)($result['state_global_adopted'] ?? '') : '';
        $quantity=trim((string)($unit['construction_quantity'] ?? ''));
        $measure=trim((string)($unit['construction_measure_unit'] ?? ''));
        $area=trim((string)($unit['built_area_adopted_m2'] ?? ''));
        $scope=$selected['cost_scope'] ?? [];
        $rows=[];
        $add=static function(string $label,string $value,bool $ok,string $source,string $destination,string $help) use (&$rows): void {
            $rows[]=['label'=>$label,'value'=>$value,'state'=>$ok?'ok':'missing','source'=>$source,'destination'=>$destination,'help'=>$help];
        };
        $positive=static fn(string $v): bool => is_numeric(str_replace(',','.',$v)) && (float)str_replace(',','.',$v)>0;
        $add('Descripción propia',trim((string)($unit['notes'] ?? '')),trim((string)($unit['notes'] ?? ''))!=='','3.1','tipologias','Describe esta unidad; la oficina no completa la descripción de sus anexos.');
        $add('Cantidad y unidad',($measure==='m2' && $area!=='' ? $area : $quantity).' '.$measure,
            $measure!=='' && ($measure==='m2' ? $positive($area) && !empty($unit['built_area_adopted_source']) : $positive($quantity)),
            '3 · construcción','construccion','En m²: adopta área y fuente. En otras unidades: confirma cantidad y unidad; no conviertas ml o m³ en m².');
        $age=trim((string)($unit['construction_age_years'] ?? ''));
        $add('Edad adoptada',$age,$age!=='' && is_numeric(str_replace(',','.',$age)) && (float)str_replace(',','.',$age)>=0,'3 · vetustez','construccion','Cero años es válido si está confirmado. La fecha de valoración debe ser la referencia.');
        $life=trim((string)($unit['construction_useful_life_years'] ?? ''));
        $add('Vida útil adoptada',$life,$positive($life),'3 · vetustez','construccion','Sustenta la referencia y las condiciones de prolongación o excepción; una pista del catálogo no es adopción.');
        $add('Conservación adoptada',$state,in_array($state,['1','1.0','1.5','2','2.0','2.5','3','3.0','3.5','4','4.0','4.5','5','5.0'],true),
            '3 · conservación','construccion','Consulta la calificación y la evidencia para Ross–Heideck; no usar una escala genérica de seis niveles como equivalencia automática.');
        foreach (CostMethodScope::options() as $key=>$option) {
            $value=(string)($scope[$key] ?? '');
            $add($option['label'],$option['values'][$value] ?? '',isset($option['values'][$value]),'C2','scope','Define el alcance de este componente.');
        }
        foreach (['source_notes','direct_scope','indirect_scope','removal_scope','integration_notes'] as $key) {
            $value=trim((string)($scope[$key] ?? ''));
            $add(CostMethodScope::texts()[$key][0],$value,$value!=='','C2','scope','Registra alcance o no aplicación justificada. Este control verifica presencia, no suficiencia del presupuesto.');
        }
        if (($scope['objective'] ?? '')==='removal') {
            foreach ($rows as &$row) if (in_array($row['label'],['Edad adoptada','Vida útil adoptada','Conservación adoptada'],true)) {
                $row['state']='na'; $row['help']='No aplica como depreciación de un presupuesto de retiro. Conserva la inspección física y las cantidades propias.';
            }
            unset($row);
            $add('Coherencia del presupuesto de retiro',(string)($scope['process'] ?? ''),($scope['process'] ?? '')==='not_applicable',
                'C2','scope','Para sólo presupuesto de retiro, no aplicar reposición/reproducción ni depreciación como si fuera valor de la construcción.');
        }
        return $rows;
    }
}
