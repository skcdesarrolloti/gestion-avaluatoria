<?php
declare(strict_types=1);
namespace App\Services;

/** Preliminary calendar: unknown costs/losses remain null, never assumed zero. */
final class IncomeFlowPreview
{
    public static function calculate(array $s,array $context): array
    {
        $p=$s['projection'] ?? [];
        if (!empty($context['value_date'])) $p['date']=$context['value_date'];
        $s['projection']=$p;$source=IncomeValuationContext::rent($p,$context);
        $errors=array_values(array_unique(array_merge($source['errors'],IncomeProjectionChecks::calendarErrors($s,$source))));
        $out=['date'=>$p['date'] ?? '', 'example'=>($p['income_source'] ?? '')==='scenario','errors'=>$errors,'months'=>[],
            'annual_gross'=>$source['rent']===null?null:$source['rent']*12,'annual_net'=>null,'pending'=>[], 'total_gross'=>null];
        if ($errors) return $out;
        $lossKnown=($p['vacancy'] ?? '')!=='' && ($p['arrears'] ?? '')!=='' && ($p['loss_support'] ?? '')!=='';
        $costKnown=!IncomeOperatingCosts::errors($s);
        if (!$lossKnown) $out['pending'][]='Vacancia y pérdidas de cobro pendientes; el ingreso potencial no acredita cobros.';
        if (!$costKnown) $out['pending'][]='Costos del propietario pendientes de validar, incluida PH cuando corresponda. Flujo neto pendiente; no se asigna cero a lo desconocido.';
        $out['pending'][]='Los valores por capitalización directa y FCD requieren tasas compatibles, costos revisados y el tratamiento de salida del FCD en la etapa 4.';
        foreach (IncomeCashFlow::calendar($s,$source) as $row) {
            $effective=$lossKnown?$row['potential']*(1-(float)$p['vacancy']/100)*(1-(float)$p['arrears']/100):null;
            $costs=$cash=null;
            if ($costKnown) {
                $operating=IncomeOperatingCosts::month($row['potential'],$p,$s['costs'] ?? [],$row['fraction'],$row['year']);
                $costs=$operating['costs']+$row['extra'];$cash=$operating['noi']-$row['extra']-$row['capex'];
            }
            $out['months'][]=$row+['effective'=>$effective,'costs'=>$costs,'cash'=>$cash,'pv'=>null];
        }
        if ($costKnown) $out['annual_net']=IncomeOperatingCosts::month($source['rent'],$p,$s['costs'] ?? [])['noi']*12;
        $out['total_gross']=array_sum(array_column($out['months'],'potential'));
        return $out;
    }
}
