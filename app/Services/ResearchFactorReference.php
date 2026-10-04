<?php
declare(strict_types=1);
namespace App\Services;

/** Stable shared definitions; portal references are evidence of possible fields, not extraction promises. */
final class ResearchFactorReference
{
    public static function scale(array $factor): string
    {
        if ($factor['kind']==='numeric') return 'Medida original en '.$factor['unit'];
        if ($factor['kind']==='binary') return '0 = No · 1 = Sí';
        $levels=explode("\n",$factor['categories']);
        if ($factor['kind']==='ordinal') return implode(' · ',array_map(static fn($i,$label)=>$i.' = '.$label,array_keys($levels),$levels));
        return 'Clases: '.implode(' · ',$levels).' (sin orden numérico impuesto)';
    }
    public static function portals(string $type,string $key): array
    {
        $pattern=match($key) {
            'area','built'=>'/construid|privada|superficie/iu','land'=>'/terreno|lote/iu',
            'bathrooms'=>'/baños/iu','bedrooms'=>'/habitaciones|alcobas/iu','parking'=>'/parqueadero|garaje/iu',
            'age'=>'/antigüedad|año de construcción/iu','levels'=>'/niveles|cantidad de pisos/iu','floor'=>'/piso/iu',
            'elevator'=>'/ascensor/iu','view'=>'/vista/iu','finishes'=>'/acabados/iu',
            'service'=>'/servicio/iu','height'=>'/altura/iu','access'=>'/acceso/iu',
            'stratum'=>'/estrato/iu','deposit'=>'/depósito|almacenamiento/iu','generator'=>'/planta eléctrica/iu',default=>'/$^/',
        };
        static $catalog=null;
        $catalog ??= ComparablePortalProfiles::all();
        $out=[];
        foreach ($catalog as $portal=>$profiles) {
            $profile=$profiles[$type] ?? null;
            if (!$profile || empty($profile['url'])) continue;
            $text=implode(' ',array_merge($profile['basics'],$profile['descriptive']));
            if (preg_match($pattern,$text)) $out[]=['label'=>ComparablePortalProfiles::portals()[$portal] ?? $portal,'url'=>$profile['url'],'status'=>$profile['status']];
        }
        return $out;
    }
    public static function validateFixed(array $plan,array $previous,array $scales=[]): void
    {
        foreach ($plan['factors'] as $key=>$item) {
            $base=ResearchFactorScaleInput::catalog(ResearchFactorCatalog::all(),$scales)[$key];
            $old=$previous['factors'][$key] ?? [];
            $matchesCatalog=true;
            foreach (['kind','categories','definition'] as $field) $matchesCatalog=$matchesCatalog && $item[$field]===($field==='definition'?$base['why']:$base[$field]);
            if ($matchesCatalog) continue;
            foreach (['kind','categories','definition'] as $field) {
                $expected=$old[$field] ?? ($field==='definition'?$base['why']:$base[$field]);
                if ($item[$field]!==$expected) throw new \App\Core\HttpException(422,'La definición y escala son permanentes. Elige el uso y la obtención sin cambiar la clasificación del factor.');
            }
        }
    }
}
