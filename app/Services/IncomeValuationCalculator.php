<?php
declare(strict_types=1);
namespace App\Services;

final class IncomeValuationCalculator
{
    public static function calculate(array $s,array $context): array
    {
        $p=$s['projection'] ?? [];$a=$s['adoption'] ?? [];
        if (($context['value_date'] ?? '')!=='') {$p['date']=$context['value_date'];$s['projection']=$p;}
        $rates=IncomeRateCalculator::all($s['rates'] ?? [],$a);$source=IncomeValuationContext::rent($p,$context);
        if (($p['date'] ?? '')==='') $source['errors'][]='Indica la fecha del avalúo.';
        $directErrors=$source['errors'];$direct=self::chosen($rates,$a,'direct',$directErrors);
        if (($a['direct_reason'] ?? '')==='') $directErrors[]='Justifica la elección de la tasa directa.';
        if ($direct && $direct['basis']==='net') $directErrors=array_merge($directErrors,IncomeOperatingCosts::errors($s));
        $d=['errors'=>$directErrors,'value'=>null];
        if (!$directErrors) {
            $annual=$source['rent']*12;
            if ($direct['basis']==='net') $annual=IncomeOperatingCosts::month($source['rent'],$p,$s['costs'] ?? [])['noi']*12;
            if ($annual<=0) $d['errors'][]='El ingreso representativo debe ser positivo para capitalizarlo.';
            else $d+=['annual_income'=>$annual,'rate'=>$direct['value'],'basis'=>$direct['basis']];
            if (!$d['errors']) $d['value']=$annual/$direct['value'];
        }
        $fErrors=array_merge($source['errors'],IncomeProjectionChecks::errors($s,$source));
        $discount=self::chosen($rates,$a,'discount',$fErrors);$exit=null;
        if (($a['terminal_mode'] ?? '')==='capitalized') {
            $exit=self::chosen($rates,$a,'exit',$fErrors);
            if ($exit && $exit['basis']==='cash' && ($a['terminal_capex'] ?? '')==='') $fErrors[]='Indica reposición anual estabilizada; cero requiere soporte.';
            if ($discount && ($a['terminal_growth'] ?? '')!=='' && (float)$a['terminal_growth']/100>=$discount['value']) $fErrors[]='El crecimiento perpetuo debe ser menor que el descuento.';
        }
        $f=['errors'=>array_values(array_unique($fErrors)),'value'=>null];
        if (!$fErrors) {
            $flow=IncomeCashFlow::forecast($s,$source,$discount['value']);$terminal=0;$terminalIncome=0;
            if ($exit) {
                $terminalIncome=($flow['terminal_noi']-($exit['basis']==='cash'?(float)$a['terminal_capex']:0))*(1+(float)$a['terminal_growth']/100);
                if ($terminalIncome<=0) $f['errors'][]='El ingreso terminal estabilizado no es positivo; revisa continuidad, costos y reposiciones.';
                else $terminal=$terminalIncome/$exit['value'];
            }
            if (!$f['errors']) {
                $selling=$exit?$terminal*(float)$a['selling_cost']/100:0;
                $pvTerminal=($terminal-$selling)/pow(1+$discount['value'],$flow['terminal_time']);
                $f=array_merge($f,$flow,['value'=>$flow['pv_flows']+$pvTerminal,'discount'=>$discount['value'],'terminal_income'=>$terminalIncome,
                    'terminal_value'=>$terminal,'selling_cost'=>$selling,'pv_terminal'=>$pvTerminal,'exit_rate'=>$exit['value'] ?? null]);
            }
        }
        $warnings=[];
        if (($p['income_source'] ?? '')==='scenario') $warnings[]='Escenario de prueba: el canon y sus supuestos no constituyen evidencia de un contrato ni de renta de mercado verificados.';
        foreach ($s['rates'] ?? [] as $r) if (($p['date'] ?? '')!=='' && ($r['date'] ?? '')>$p['date']) $warnings[]=$r['name'].': evidencia posterior a la fecha del avalúo; revisa su procedencia y pertinencia para esa fecha.';
        if ($source['lease'] && ($source['lease']['effective_date'] ?? '')==='') $warnings[]='El contrato no indica fecha de referencia del canon vigente; comprueba que corresponda al origen de la proyección.';
        if ($f['value']!==null && $f['value']<=0) $warnings[]='El escenario produce valor presente no positivo. Revisa viabilidad y alcance antes de interpretarlo como valor del inmueble.';
        return ['rates'=>$rates,'direct'=>$d,'dcf'=>$f,'warnings'=>$warnings,'date'=>$p['date'] ?? '',
            'convention'=>'COP nominales; flujo del activo antes de deuda e impuesto de renta. Cobros al cierre mensual; descuento efectivo anual con días reales/365. Costos anuales distribuidos en doce meses. Primera fracción desde el día siguiente al avalúo; incrementos de costos cada doce meses proyectados. Directa: año representativo sin eventos extraordinarios. Salida: NOI mensual al cierre, sin eventos, anualizado y ajustado al año siguiente.'];
    }
    private static function chosen(array $rates,array $a,string $purpose,array &$errors): ?array
    {
        $r=$rates[$a[$purpose.'_id'] ?? ''] ?? null;
        if (!$r || $r['purpose']!==$purpose || $r['value']===null) {
            $errors[]='Selecciona una tasa válida de '.(['direct'=>'capitalización directa','discount'=>'descuento','exit'=>'salida'][$purpose]).'.';return null;
        }
        return $r;
    }
}
