<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ComparableRegressionPreparation
{
    public static function normalize(mixed $value): array
    {
        if (!is_array($value) || !in_array($value['basis'] ?? '',['offer','adjusted'],true)
            || !is_bool($value['confirmed'] ?? null) || !is_array($value['codes'] ?? null) || count($value['codes'])>160)
            throw new HttpException(422,'Preparación de regresión inválida.');
        $codes=[];
        foreach ($value['codes'] as $key=>$code) {
            $pair=json_decode((string)$key,true);
            if (!is_array($pair) || count($pair)!==2 || !array_is_list($pair) || !is_string($pair[0]) || !is_string($pair[1])
                || mb_strlen($key)>600 || !is_scalar($code) || is_bool($code)
                || ((string)$code!=='' && (!is_numeric($code) || !is_finite((float)$code) || (float)$code<0 || (float)$code>100000000000)))
                throw new HttpException(422,'Código numérico de regresión inválido.');
            $codes[$key]=(string)$code;
        }
        return ['basis'=>$value['basis'],'confirmed'=>$value['confirmed'],'codes'=>(object)$codes];
    }
}
