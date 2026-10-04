<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

/** Observations of the subject, with the same definitions used for market samples. */
final class SubjectFactorCapture
{
    public static function decode(array $unit): array
    {
        $data=json_decode((string)($unit['subject_factors_json'] ?? '{}'),true);
        return is_array($data)?$data:[];
    }
    public static function catalog(array $unit,array $record,array $scales=[]): array
    {
        $type=self::type($unit,$record);
        $catalog=ResearchFactorCatalog::forType($type,'',false,true);
        $saved=self::decode($unit);
        if ($type==='apartamento') $catalog=ApartmentResearchFactors::preserve($catalog,$saved);
        if ($type==='casa') $catalog=HouseResearchFactors::preserve($catalog,$saved);
        foreach (['landscape_view','panoramic_view'] as $key) if (!isset($saved[$key])) unset($catalog[$key]);
        return ResearchFactorScaleInput::catalog(array_diff_key($catalog,array_flip(['area','built','land','destination'])),$scales);
    }
    public static function type(array $unit,array $record): string
    {
        if (($unit['unit_kind'] ?? '')==='annex') return ComparablePortalProfiles::defaultType((string)($unit['construction_type'] ?? ''));
        $type=(string)($unit['property_type'] ?? '');
        if ($type==='' && ($record['factor_principal_count'] ?? 1)===1) $type=(string)($record['tipo_inmueble'] ?? '');
        return ComparablePortalProfiles::defaultType($type);
    }
    public static function compatible(array $item,array $factor): bool
    {
        return ($factor['scale_valid'] ?? true) && ($item['scale_kind'] ?? '')===$factor['kind']
            && ($item['scale_categories'] ?? '')===$factor['categories'];
    }
    public static function validValue(string $value,array $factor): bool
    {
        return $factor['kind']==='numeric'?(bool)preg_match('/^\d+(?:[.,]\d+)?$/D',$value)
            :in_array($value,explode("\n",$factor['categories']),true);
    }
    public static function input(mixed $posted,array $catalog,array $previous): array
    {
        if (!is_array($posted) || count($posted)>count(ResearchFactorCatalog::all())) throw new HttpException(422,'Revisa los factores del sujeto.');
        $out=$previous;
        foreach ($posted as $key=>$item) {
            if (!isset($catalog[$key]) || !is_array($item)) throw new HttpException(422,'El factor no corresponde a esta unidad.');
            foreach (['value'=>120,'support'=>600,'scale_kind'=>20,'scale_categories'=>1200] as $field=>$max) {
                if (!is_string($item[$field] ?? '') || mb_strlen($item[$field] ?? '')>$max) throw new HttpException(422,'Revisa el dato y soporte de '.$catalog[$key]['label'].'.');
                $item[$field]=trim($item[$field] ?? '');
            }
            $item['scale_categories']=str_replace(["\r\n","\r"],"\n",$item['scale_categories']);
            $old=$previous[$key] ?? null;
            if (!$old && $item['value']==='' && $item['support']==='') continue;
            if ($old && $item['value']===($old['value'] ?? '') && $item['support']===($old['support'] ?? '') && ($item['confirm_scale'] ?? '')!=='1') continue;
            if ($item['value']!=='') {
                if (!self::compatible($item,$catalog[$key])) throw new HttpException(409,'La clasificación de '.$catalog[$key]['label'].' cambió. Recarga antes de confirmar el dato.');
                if (!self::validValue($item['value'],$catalog[$key])) throw new HttpException(422,'El dato de '.$catalog[$key]['label'].' no corresponde a la clasificación.');
                if ($item['support']==='') throw new HttpException(422,'Indica el soporte de '.$catalog[$key]['label'].'.');
            }
            $out[$key]=array_intersect_key($item,array_flip(['value','support','scale_kind','scale_categories']))+['saved_at'=>gmdate('c')];
        }
        return $out;
    }
    public static function code(string $value,array $factor): string
    {
        if (($factor['scale_valid'] ?? true)===false || $value==='' || !self::validValue($value,$factor)) return 'Pendiente';
        if ($factor['kind']==='numeric') return 'Medida: '.$value.' '.$factor['unit'];
        if ($factor['kind']==='categorical') return 'Clase: '.$value.' · sin jerarquía';
        return 'Código: '.array_search($value,explode("\n",$factor['categories']),true);
    }
}
