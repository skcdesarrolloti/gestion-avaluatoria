<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class LeaseAdjustments
{
    public static function input(mixed $rows,string $group='rent_adjustments'): array
    {
        if (!is_array($rows) || count($rows)>600) throw new HttpException(422,'Revisa las modificaciones del canon.');
        $out=[];$errors=[];
        foreach ($rows as $id=>$row) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{16}$/D',$id) || !is_array($row)
                || array_diff(array_keys($row),['date','rate','amount','source'])) throw new HttpException(422,'Modificación del canon no admitida.');
            foreach (['date','rate','amount','source'] as $key) {
                if (!array_key_exists($key,$row)) continue;
                $v=$row[$key] ?? '';$name='lease['.$group.']['.$id.']['.$key.']';
                if (!is_string($v) || mb_strlen($v)>1500) {$errors[$name]='Usa texto de hasta 1500 caracteres.';continue;}
                $v=trim($v);
                if ($v!=='' && $key==='date' && !IncomeValuationInput::date($v)) $errors[$name]='Fecha inválida.';
                if ($v!=='' && $key==='amount' && ($v=LeaseInput::money($v))===null) $errors[$name]='Importe mensual no negativo inválido.';
                if ($v!=='' && $key==='rate') {
                    $v=str_replace(',','.',$v);
                    if (!preg_match('/^-?\d+(\.\d{1,6})?$/D',$v) || (float)$v<=-100 || (float)$v>1000) $errors[$name]='Porcentaje mayor que −100 y hasta 1000.';
                }
                $out[$id][$key]=$v;
            }
        }
        if ($errors) throw new HttpException(422,'Revisa las modificaciones señaladas.',$errors);
        return $out;
    }
    public static function merge(array $old,array $changes): array
    {
        foreach ($changes as $key=>$row) {
            if (!array_filter($row,static fn($v)=>$v!=='')) unset($old[$key]);
            else $old[$key]=array_replace($old[$key] ?? [],$row);
        }
        if (count($old)>600) throw new HttpException(422,'Máximo 600 modificaciones por contrato.');
        return $old;
    }
}
