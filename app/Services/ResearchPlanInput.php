<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ResearchPlanInput
{
    public static function input(mixed $value): array
    {
        if (!is_string($value) || strlen($value)>30000) throw new HttpException(422,'Revisa el formato del plan de investigación.');
        try { $data=json_decode($value,true,8,JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new HttpException(422,'El plan de investigación llegó incompleto.'); }
        if (!is_array($data) || !is_array($data['factors'] ?? null) || count($data['factors'])>20) throw new HttpException(422,'Revisa los factores del plan.');
        $ratio=$data['target_ratio'] ?? 10;
        if (!is_int($ratio) || $ratio<1 || $ratio>100) throw new HttpException(422,'La referencia de inmuebles por factor debe estar entre 1 y 100.');
        $out=['target_ratio'=>$ratio,'factors'=>[],'updated_at'=>gmdate('c')];
        foreach ($data['factors'] as $key=>$factor) {
            if (!isset(ResearchFactorCatalog::all()[$key]) || !is_array($factor)) throw new HttpException(422,'Factor desconocido.');
            $decision=$factor['decision'] ?? '';
            if (!in_array($decision,['','filter','investigate','model','defer'],true)) throw new HttpException(422,'Decisión de factor inválida.');
            if ($key==='destination' && !in_array($decision,['','filter','defer'],true)) throw new HttpException(422,'Destinación es un filtro de investigación, no un factor candidato al modelo.');
            $kind=$factor['kind'] ?? ResearchFactorCatalog::all()[$key]['kind'];
            if (!in_array($kind,['numeric','binary','categorical','ordinal'],true)) throw new HttpException(422,'Tipo de dato inválido.');
            $item=['decision'=>$decision,'kind'=>$kind];
            $collection=$factor['collection'] ?? 'mixed';
            if (!is_string($collection) || !in_array($collection,['portal','manual','mixed'],true)) throw new HttpException(422,'Revisa cómo obtendrás el dato del factor.');
            $item['collection']=$collection;
            foreach (['reason'=>600,'definition'=>600,'categories'=>1200] as $field=>$max) {
                $text=$factor[$field] ?? '';
                if (!is_string($text) || mb_strlen($text)>$max) throw new HttpException(422,'Revisa definición, categorías o justificación: texto demasiado largo.');
                $item[$field]=trim($text);
            }
            $lines=array_values(array_unique(array_filter(array_map('trim',explode("\n",$item['categories'])))));
            if (count($lines)>15) throw new HttpException(422,'Usa hasta 15 categorías por factor.');
            if ($kind==='ordinal') {
                $normalized=array_map(static fn($line)=>trim(preg_replace('/[_\s]+/u',' ',strtr(mb_strtolower($line),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']))),$lines);
                if (count(array_unique($normalized))!==count($normalized) || array_intersect($normalized,['no verificado','por verificar','desconocido','no publicado','pendiente']))
                    throw new HttpException(422,'La escala ordinal no admite niveles repetidos ni datos desconocidos como calificación.');
            }
            $item['categories']=implode("\n",$lines);
            $out['factors'][$key]=$item;
        }
        if (count(array_filter($out['factors'],static fn($item)=>$item['decision']==='model'))>4)
            throw new HttpException(422,'Selecciona como máximo cuatro factores candidatos para el modelo. Puedes investigar todos los demás.');
        return $out;
    }
    public static function validateScope(array $plan,string $type,string $method,string $part=''): void
    {
        if (!in_array($method,['mercado','renta'],true)) throw new HttpException(422,'El plan de investigación corresponde a Mercado o Renta.');
        foreach ($plan['factors'] as $key=>$item) if (!isset(ResearchFactorCatalog::forType($type,$part)[$key]))
            throw new HttpException(422,'El factor no corresponde al tipo actual. Revisa el plan de esta unidad.');
    }
}
