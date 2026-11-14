<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class LeaseInput
{
    public static function fields(mixed $post, string $prefix='lease', ?array $only=null): array
    {
        if (!is_array($post) || array_is_list($post)) throw new HttpException(422,'Revisa la ficha del arriendo.');
        $out=$errors=[];
        $definitions=LeaseFields::all();
        foreach ($post as $key=>$value) {
            if (!isset($definitions[$key]) || ($only!==null && !in_array($key,$only,true)))
                throw new HttpException(422,'La ficha contiene campos no admitidos.');
            $field=$definitions[$key]; $error='';
            if ($field['type']==='history') { $out[$key]=LeaseMonthlyHistory::input($value); continue; }
            if ($field['type']==='adjustments') { $out[$key]=LeaseAdjustments::input($value,$key); continue; }
            if ($field['type']==='vacancies') { $out[$key]=LeaseVacancies::input($value); continue; }
            if (!is_string($value)) $error='Revisa el formato del dato.';
            else {
                $value=trim($value);
                if (mb_strlen($value)>1500) $error='Escribe como máximo 1500 caracteres.';
                elseif ($value!=='' && $field['type']==='select' && !isset($field['options'][$value])) $error='Selecciona una opción de la lista.';
                elseif ($value!=='' && $field['type']==='area_reference'
                    && !preg_match('/^[a-f0-9]{32}:(area_private_m2|area_adopted_m2|built_area_adopted_m2)$/D',$value)) $error='Selecciona un área de las unidades del contrato.';
                elseif ($value!=='' && $field['type']==='date') {
                    $date=\DateTimeImmutable::createFromFormat('!Y-m-d',$value);
                    if (!$date || $date->format('Y-m-d')!==$value) $error='Indica una fecha válida.';
                } elseif ($value!=='' && in_array($field['type'],['points','percent'],true)) {
                    $value=str_replace(',','.',$value);
                    if (!preg_match('/^-?\d+(\.\d{1,6})?$/D',$value) || (float)$value<($field['type']==='points'?-99:0) || (float)$value>100)
                        $error='Usa un porcentaje válido: 0 a 100, o −99 a 100 para puntos adicionales.';
                } elseif ($value!=='' && $field['type']==='money') {
                    $value=self::money($value);
                    if ($value===null) $error='Usa un importe no negativo, hasta 12 enteros y 2 decimales.';
                }
            }
            if ($error!=='') $errors[$prefix.'['.$key.']']=$error;
            else $out[$key]=$value;
        }
        if ($errors) throw new HttpException(422,'Revisa los campos señalados.',$errors);
        return $out;
    }

    public static function money(string $value): ?string
    {
        $value=str_replace(['$',' '],'',$value);
        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d{1,2})?$/D',$value)) $value=str_replace('.','',$value);
        $value=str_replace(',','.',$value);
        return preg_match('/^\d{1,12}(\.\d{1,2})?$/D',$value)?$value:null;
    }

    public static function validate(array $data): void
    {
        foreach (['start_date','end_date','first_adjustment_date','history_admin_date'] as $key)
            if (!empty($data[$key]) && !IncomeValuationInput::date($data[$key]))
                throw new HttpException(422,'Revisa las fechas.',['lease['.$key.']'=>'Fecha válida entre 1900 y 2199.']);
        if (!empty($data['start_date']) && !empty($data['end_date']) && $data['end_date']<$data['start_date'])
            throw new HttpException(422,'Revisa las fechas del contrato.',['lease[end_date]'=>'La terminación no puede ser anterior al inicio.']);
    }
}
