<?php
declare(strict_types=1);
namespace App\Services;

final class MethodStudyState
{
    private const FIELDS=['research_plan','analysis','conclusion','evidence_hash','income_concept','cost_scope'];
    public static function change(array $item,array $changes): array
    {
        $old=$item['method'] ?? '';$next=$changes['method'] ?? $old;
        if ($old!=='' && $old!==$next && array_intersect(array_keys($changes),['research_plan','analysis','conclusion','income_concept','evidence_hash']))
            throw new \App\Core\HttpException(422,'Guarda el cambio de método separado de los datos de la investigación.');
        if ($old!==$next) {
            $snapshot=array_intersect_key($item,array_flip(self::FIELDS));
            if ($snapshot!==[]) $item['method_studies'][$old ?: 'sin_identificar']=$snapshot;
            foreach (self::FIELDS as $field) unset($item[$field]);
            $item=array_replace($item,$item['method_studies'][$next] ?? []);
        }
        if (isset($changes['research_plan'])) {
            if (!empty($item['research_plan']) && !isset($item['research_plan']['study_scope']))
                $item['legacy_research_plan'] ??= $item['research_plan'];
            if (isset($item['research_plan']['study_scope']) && ($changes['research_plan']['study_scope'] ?? '')!==$item['research_plan']['study_scope'])
                $item['study_plan_history'][$item['research_plan']['study_scope']]=$item['research_plan'];
            $changes['research_plan']['study_scope'] ??= $next;
        }
        return array_replace($item,$changes);
    }
    public static function visible(array $item,?string $token=null): array
    {
        if (($item['method'] ?? '')==='renta' && !empty($item['research_plan'])
            && ($item['research_plan']['study_scope'] ?? '')!=='renta:arriendo') unset($item['research_plan']);
        if ($token!==null && isset($item['research_plan']['study_scope']) && $item['research_plan']['study_scope']!==$token) unset($item['research_plan']);
        return $item;
    }
}
