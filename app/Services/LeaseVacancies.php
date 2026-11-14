<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class LeaseVacancies
{
    public static function input(mixed $rows): array
    {
        if (!is_array($rows) || count($rows)>600) throw new HttpException(422,'Máximo 600 períodos de vacancia.');
        $out=$errors=[];
        foreach ($rows as $id=>$row) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{16}$/D',$id) || !is_array($row)
                || array_diff(array_keys($row),['start','end','source'])) throw new HttpException(422,'Período de vacancia no admitido.');
            foreach ($row as $key=>$v) {
                $name='lease[vacancy_periods]['.$id.']['.$key.']';
                if (!is_string($v) || mb_strlen($v)>1500) {$errors[$name]='Usa texto de hasta 1500 caracteres.';continue;}
                $v=trim($v);
                if ($v!=='' && $key!=='source' && !IncomeValuationInput::date($v)) $errors[$name]='Fecha inválida (1900–2199).';
                $out[$id][$key]=$v;
            }
        }
        if ($errors) throw new HttpException(422,'Revisa las vacancias señaladas.',$errors);
        return $out;
    }
    public static function merge(array $old,array $changes): array
    {
        $merged=LeaseAdjustments::merge($old,$changes);
        foreach ($merged as $id=>$row) if (!empty($row['start']) && !empty($row['end']) && $row['end']<$row['start'])
            throw new HttpException(422,'Revisa las fechas de vacancia.',
                ['lease[vacancy_periods]['.$id.'][end]'=>'La fecha final no puede ser anterior al inicio.']);
        return $merged;
    }
}
