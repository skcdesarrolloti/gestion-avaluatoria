<?php
declare(strict_types=1);
namespace App\Services;

/** Supplementary evidence by actual listing type; no inference from another type. */
final class ResearchFactorSources
{
    public static function portals(string $type,string $key): array
    {
        $rows=[
            'apartamento'=>[
                ['https://www.fincaraiz.com.co/venta/apartamentos/bucaramanga/santander/usados',['pool','gym','security','covered_parking']],
                ['https://www.fincaraiz.com.co/venta/apartamentos/con-ascensor',['balcony','air_conditioning','landscape_view']],
            ],
            'local'=>[['https://www.fincaraiz.com.co/venta/locales',['shopfront']]],
            'bodega'=>[['https://www.fincaraiz.com.co/venta/bodegas?addeletedid=11103858',['frontage','depth','vehicle_access','loading_access','loading_bays','mezzanine','security']]],
            'lote'=>[['https://www.fincaraiz.com.co/venta/lotes/tolima',['topography','corner']]],
            'finca'=>[['https://www.fincaraiz.com.co/venta/fincas/caldas',['irrigation','terrace']]],
            'edificio'=>[['https://www.fincaraiz.com.co/venta/edificios/bogota/bogota-dc/con-cochera',['covered_parking','security','balcony','landscape_view']]],
        ];
        $out=[];
        foreach ($rows[$type] ?? [] as [$url,$keys]) if (in_array($key,$keys,true)) {
            $out[]=['label'=>'FincaRaíz','url'=>$url,'status'=>'Evidencia pública complementaria · revisada 2026-10-04'];
            break;
        }
        return $out;
    }
}
