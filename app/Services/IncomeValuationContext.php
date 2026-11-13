<?php
declare(strict_types=1);
namespace App\Services;

final class IncomeValuationContext
{
    public static function build(array $record,array $component): array
    {
        $unit=$component['unit'] ?? [];$leases=LeaseRegister::forUnit($record,(string)($unit['id'] ?? ''));
        $out=[];
        foreach ($leases as $id=>$lease) {
            $economy=LeaseRegister::economic($lease,$record);$amount=$economy['rent_amount'] ?? '';
            $factor=['mensual'=>1,'trimestral'=>3,'anual'=>12][$economy['rent_period'] ?? ''] ?? null;
            $out[$id]=['title'=>LeaseRegister::title($lease),'rent'=>$factor && is_numeric($amount)?(float)$amount/$factor:null,
                'start'=>$lease['start_date'] ?? '', 'end'=>$lease['end_date'] ?? '', 'status'=>$lease['status'] ?? '',
                'scope'=>$lease['scope'] ?? '', 'support'=>$lease['support'] ?? '', 'increase'=>$lease['adjustment_rule'] ?? '', 'effective_date'=>$lease['effective_date'] ?? '',
                'history'=>$lease['monthly_history'] ?? [],'unit_ids'=>$lease['unit_ids'] ?? []];
        }
        return ['unit'=>$unit['id'] ?? '', 'label'=>$component['label'] ?? '', 'leases'=>$out,'value_date'=>(string)($record['value_date'] ?? ''),
            'hash'=>self::hash([$unit,$leases,(string)($record['value_date'] ?? ''),array_intersect_key($record,array_flip(LeaseFields::ECONOMIC))])];
    }
    public static function rent(array $p,array $context): array
    {
        $errors=[];$rent=null;$lease=null;
        if (($p['income_source'] ?? '')==='market') {
            if (($p['market_rent'] ?? '')!=='') $rent=(float)$p['market_rent'];
        } elseif (($p['income_source'] ?? '')==='lease') {
            $lease=$context['leases'][$p['lease_id'] ?? ''] ?? null;
            $rent=$lease['rent'] ?? null;
            if (!$lease) $errors[]='Vincula un contrato de esta unidad en la etapa 1.';
            elseif ($lease['status']!=='current') $errors[]='Verifica la vigencia del contrato en la etapa 1.';
        }
        if ($rent===null || $rent<=0) $errors[]='Falta un canon mensual positivo y compatible con el alcance.';
        if (empty($p['income_support'])) $errors[]='Documenta el canon representativo y los anexos incluidos.';
        return compact('rent','lease','errors');
    }
    public static function fingerprint(array $state,array $context): string
    { unset($state['calculation'],$state['history']);return self::hash([$state,$context['hash']]); }
    private static function hash(array $data): string
    {
        $sort=static function(array $a) use (&$sort): array {
            foreach ($a as &$v) if (is_array($v)) $v=$sort($v);
            unset($v);if (!array_is_list($a)) ksort($a);return $a;
        };
        return hash('sha256',json_encode($sort($data),JSON_THROW_ON_ERROR));
    }
}
