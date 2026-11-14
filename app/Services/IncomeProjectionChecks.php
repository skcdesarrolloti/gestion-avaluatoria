<?php
declare(strict_types=1);
namespace App\Services;
use DateTimeImmutable as Date;

final class IncomeProjectionChecks
{
    public static function errors(array $s,array $source): array
    {
        $p=$s['projection'] ?? [];$a=$s['adoption'] ?? [];
        $errors=array_merge(IncomeOperatingCosts::errors($s),self::calendarErrors($s,$source));
        $lease=$source['lease'];
        if (!in_array($a['terminal_mode'] ?? '',['capitalized','none'],true)) $errors[]='Define el tratamiento del valor remanente.';
        foreach (['discount_reason','exit_reason'] as $f) if (($a[$f] ?? '')==='') $errors[]='Completa '.IncomeValuationFields::group('adoption')[$f]['label'].'.';
        if (($a['terminal_mode'] ?? '')==='capitalized') {
            if ($lease && ($p['after_contract'] ?? '')==='stop') $errors[]='Una salida perpetua necesita continuidad de renta sustentada después del contrato; revisa recolocación o elige sin remanente.';
            foreach (['terminal_growth','selling_cost'] as $f) if (($a[$f] ?? '')==='') $errors[]='Completa '.IncomeValuationFields::group('adoption')[$f]['label'].'.';
        }
        return array_values(array_unique($errors));
    }
    public static function calendarErrors(array $s,array $source): array
    {
        $p=$s['projection'] ?? [];$errors=[];
        foreach (['date','months','growth','projection_support'] as $f)
            if (($p[$f] ?? '')==='') $errors[]='Completa '.IncomeValuationFields::group('projection')[$f]['label'].'.';
        if ((int)($p['months'] ?? 0)<1 || (int)$p['months']>600) $errors[]='El horizonte debe tener entre 1 y 600 meses.';
        if (!empty($p['date']) && !IncomeValuationInput::date($p['date'])) $errors[]='Fecha de avalúo inválida.';
        if ((float)($p['growth'] ?? 0)!==0.0 && ($p['next_increase'] ?? '')==='') $errors[]='Indica la primera fecha de incremento.';
        if (($p['next_increase'] ?? '')!=='' && ($p['date'] ?? '')!=='' && $p['next_increase']<=$p['date']) $errors[]='El primer incremento proyectado debe ser posterior al avalúo; el canon inicial ya debe estar actualizado a esa fecha.';
        $lease=$source['lease'];
        if ($lease) {
            if ($lease['start']==='' || $lease['end']==='' || $lease['end']<$lease['start']) $errors[]='Completa y revisa inicio y vencimiento del contrato en la etapa 1.';
            if (($p['after_contract'] ?? '')==='') $errors[]='Define qué ocurre al terminar el contrato.';
            if (($p['after_contract'] ?? '')==='market') {
                if (($p['relet_date'] ?? '')==='' || $p['relet_date']<=$lease['end']) $errors[]='La recolocación debe comenzar después del contrato.';
                if ((float)($p['market_rent'] ?? 0)<=0) $errors[]='Sustenta un canon de mercado positivo para la recolocación.';
            }
        }
        $seen=[];
        foreach ($s['events'] ?? [] as $i=>$e) {
            if (($e['month'] ?? '')==='' || ($e['source'] ?? '')==='') $errors[]='Evento '.($i+1).': indica mes y soporte.';
            if (isset($seen[$e['month']])) $errors[]='Consolida los eventos del mes '.$e['month'].' en una sola fila.';
            $seen[$e['month']]=true;
            if (IncomeValuationInput::date($p['date'] ?? '') && (int)($p['months'] ?? 0)>0) {
                $start=(new Date($p['date']))->modify('+1 day')->modify('first day of this month');
                $end=$start->modify('+'.((int)$p['months']-1).' months');
                if ($e['month']<$start->format('Y-m') || $e['month']>$end->format('Y-m')) $errors[]='El evento '.$e['month'].' está fuera del horizonte.';
            }
        }
        return array_values(array_unique($errors));
    }
}
