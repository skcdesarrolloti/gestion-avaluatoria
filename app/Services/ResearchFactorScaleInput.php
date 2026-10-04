<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ResearchFactorScaleInput
{
    public static function input(array $post): array
    {
        $key=$post['factor_key'] ?? '';
        $base=is_string($key)?(ResearchFactorCatalog::all()[$key] ?? null):null;
        if (!$base || !in_array($base['kind'],['categorical','ordinal'],true) || $key==='destination') throw new HttpException(422,'Este dato conserva su medida o clasificación fija.');
        $kind=$post['kind'] ?? '';
        if (!in_array($kind,['categorical','ordinal'],true)) throw new HttpException(422,'Elige clases o niveles ordenados.');
        $data=ResearchPlanInput::input(json_encode(['factors'=>[$key=>['kind'=>$kind,'categories'=>$post['categories'] ?? '', 'definition'=>$base['why']]]],JSON_THROW_ON_ERROR));
        $scale=$data['factors'][$key];
        $labels=array_map(static fn($v)=>mb_strtolower(trim($v)),explode("\n",$scale['categories']));
        if (count(array_unique($labels))!==count($labels) || array_intersect($labels,['desconocido','pendiente','no publicado','no verificado','por verificar'])) throw new HttpException(422,'No repitas clases ni incluyas desconocido como nivel.');
        if (count($labels)<2) throw new HttpException(422,'Define al menos dos clases o niveles.');
        return ['kind'=>$kind,'categories'=>$scale['categories'],'definition'=>$base['why']];
    }
    public static function catalog(array $catalog,array $scales): array
    {
        foreach ($catalog as $key=>&$factor) if (isset($scales[$key])) {
            $factor['kind']=$scales[$key]['kind']; $factor['categories']=$scales[$key]['categories'];
        }
        return $catalog;
    }
}
