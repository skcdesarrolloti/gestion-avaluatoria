<?php
declare(strict_types=1);
namespace App\Support;

/** Familias de consulta; ninguna referencia se adopta por el tipo del inmueble. */
final class IgacTypologySelection
{
    public static function families(): array
    {
        return [
            ...array_map(static fn($category)=>[$category],AppraisalConstructionTypeCatalog::igacCategories()),
            'oficina'=>['COMERCIALES','EDIFICIOS'], 'consultorio'=>['COMERCIALES','EDIFICIOS','INSTITUCIONALES'],
            'apartamento'=>['RESIDENCIALES','EDIFICIOS'], 'casa'=>['RESIDENCIALES','EDIFICIOS'],
            'local'=>['COMERCIALES','EDIFICIOS'], 'bodega'=>['INDUSTRIALES','COMERCIALES'],
            'galpon'=>['INDUSTRIALES'], 'edificio'=>['EDIFICIOS','COMERCIALES','INSTITUCIONALES'],
            'hotel'=>['EDIFICIOS','COMERCIALES','INSTITUCIONALES'],
            'lote'=>[], 'parqueadero'=>['ANEXOS'],
        ];
    }

    public static function category(string $hint, array $catalog): ?string
    {
        foreach ($catalog as $code=>$options)
            foreach ($options as $item) if ($item['value']===$hint) return $code;
        return null;
    }

    public static function options(string $type, string $category, string $selected, array $catalog, bool $all=false): array
    {
        $categories=$all?array_keys($catalog):(self::families()[$type] ?? ($type==='' && $category!==''?[$category]:array_keys($catalog)));
        $out=[];
        foreach ($categories as $code) foreach ($catalog[$code] ?? [] as $item)
            $out[]=array_replace($item,['category'=>$code,'label'=>$item['label'].' · '.$code]);
        if ($all) return $out;
        $out=IgacTypologyFilter::options($out,$type);
        if ($selected!=='' && !in_array($selected,array_column($out,'value'),true))
            foreach ($catalog[self::category($selected,$catalog) ?? ''] ?? [] as $item)
                if ($item['value']===$selected) $out[]=array_replace($item,['category'=>self::category($selected,$catalog),
                    'label'=>'Anterior · revisar tipo: '.$item['label'],'retained'=>true]);
        return $out;
    }
}
