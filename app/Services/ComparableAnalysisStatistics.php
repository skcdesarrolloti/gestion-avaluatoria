<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ComparableAnalysisStatistics
{
    public static function normalize(mixed $items): array
    {
        if (!is_array($items) || count($items)>80) throw new HttpException(422,'Historial estadístico inválido: máximo 80 etapas.');
        $out=[];
        foreach ($items as $v) {
            if (!is_object($v) || !in_array($v->action ?? null,['initial','filter','restore','remove','factors'],true)
                || !is_string($v->at ?? null) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/D',$v->at) || strtotime($v->at)===false
                || !is_string($v->changed ?? null) || ($v->changed!=='' && !preg_match('/^[a-f0-9]{32}$/D',$v->changed))
                || !in_array($v->scope ?? null,['subject','all'],true) || !in_array($v->regime ?? null,['','si','no'],true)
                || !is_int($v->count ?? null) || $v->count<0 || $v->count>160 || !is_array($v->factors ?? null) || count($v->factors)>160
                || !property_exists($v,'complete') || ($v->complete!==null && (!is_int($v->complete) || $v->complete<0 || $v->complete>$v->count)))
                throw new HttpException(422,'Etapa estadística inválida.');
            foreach ($v->factors as $factor) if (!is_string($factor) || mb_strlen($factor)>180) throw new HttpException(422,'Factor histórico inválido.');
            $out[]=['at'=>$v->at,'action'=>$v->action,'changed'=>$v->changed,'scope'=>$v->scope,'regime'=>$v->regime,'count'=>$v->count,'factors'=>$v->factors,'complete'=>$v->complete,
                'offer'=>self::stats($v->offer ?? null,$v->count),'adjusted'=>self::stats($v->adjusted ?? null,$v->count)];
        }
        return $out;
    }
    private static function stats(mixed $v,int $count): array
    {
        if (!is_object($v) || !is_int($v->n ?? null) || $v->n<0 || $v->n>$count) throw new HttpException(422,'Cantidad estadística inválida.');
        $out=['n'=>$v->n];
        foreach (['mean','median','sd','cv'] as $key) {
            if (!property_exists($v,$key) || ($v->$key!==null && (!is_int($v->$key) && !is_float($v->$key) || !is_finite((float)$v->$key) || $v->$key<0)))
                throw new HttpException(422,'Valor estadístico inválido.');
            $out[$key]=$v->$key;
        }
        return $out;
    }
}
