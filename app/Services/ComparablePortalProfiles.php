<?php
declare(strict_types=1);
namespace App\Services;

/** Evidence from public listings; not a publisher API/schema or extraction guarantee. */
final class ComparablePortalProfiles
{
    public const REVIEWED = '2026-10-04';
    public static function types(): array
    {
        return ['oficina'=>'Oficina','apartamento'=>'Apartamento','casa'=>'Casa','lote'=>'Lote',
            'local'=>'Local','bodega'=>'Bodega','consultorio'=>'Consultorio','edificio'=>'Edificio',
            'finca'=>'Finca','hotel'=>'Hotel / hospedaje','parqueadero'=>'Garaje / parqueadero',
            'deposito'=>'Depósito / cuarto útil'];
    }
    public static function portals(): array
    {
        return ['fincaraiz'=>'FincaRaíz','ciencuadras'=>'Ciencuadras','metrocuadrado'=>'Metrocuadrado',
            'properati'=>'Properati','mercadolibre'=>'Mercado Libre Inmuebles'];
    }
    public static function defaultType(string $label): string
    {
        $label=mb_strtolower($label);
        if (str_contains($label,'depósito') || str_contains($label,'deposito') || str_contains($label,'cuarto útil')) return 'deposito';
        if (str_contains($label,'garaje') || str_contains($label,'parqueadero') || str_contains($label,'parqueo')) return 'parqueadero';
        foreach (self::types() as $key=>$name) if (str_contains($label,$key)) return $key;
        return '';
    }
    public static function all(): array
    {
        $profiles=[];
        foreach (['fincaraiz','ciencuadras','otros'] as $file) {
            $rows=require BASE_PATH.'/app/Services/portal-profiles/'.$file.'.php';
            foreach ($rows as $portal=>$types) foreach ($types as $type=>$row) $profiles[$portal][$type]=$row;
        }
        return $profiles;
    }
    public static function profile(string $portal, string $type): array
    {
        return self::all()[$portal][$type] ?? ['status'=>'Pendiente de investigación', 'basics'=>[],
            'descriptive'=>[], 'notes'=>['No se ha documentado una ficha suficiente para esta combinación. No se extrapolan campos de otro tipo o portal.'], 'url'=>''];
    }
}
