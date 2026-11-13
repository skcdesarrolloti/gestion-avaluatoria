<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ComparableAreaInput
{
    public static function read(array $post): array
    {
        $items=$post['areas'] ?? null;$out=[];$errors=[];
        if (!is_array($items) || !$items || count($items)>100) throw new HttpException(422,'Selecciona entre 1 y 100 muestras.');
        foreach ($items as $id=>$fields) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D',$id) || !is_array($fields)
                || array_diff(array_keys($fields),['area_m2','area_basis','areas_source'])) throw new HttpException(422,'Campos de área inválidos.');
            foreach (['area_m2','area_basis','areas_source'] as $field) {
                $value=$fields[$field] ?? null;$name="areas[$id][$field]";
                if (!is_string($value) || mb_strlen($value)>1600) { $errors[$name]='Usa texto de hasta 1600 caracteres.';continue; }
                $value=trim($value);
                if ($field==='area_m2' && $value!=='') {
                    $value=str_replace(',','.',$value);
                    if (!preg_match('/^\d+(\.\d{1,2})?$/D',$value) || (float)$value<=0 || (float)$value>100000000000) {
                        $errors[$name]='Usa un área positiva, sin separador de miles y hasta dos decimales.';continue;
                    }
                    $value=number_format((float)$value,2,'.','');
                }
                $out[$id][$field]=$value;
            }
        }
        if ($errors) throw new HttpException(422,'Revisa los campos señalados.',$errors);
        return $out;
    }
}
