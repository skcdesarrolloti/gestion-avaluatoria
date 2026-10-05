<?php
declare(strict_types=1);
namespace App\Services;

/** Owner-scoped definitions loaded afresh for each authenticated request. */
final class UserResearchFactors
{
    private static array $items=[];
    public static function load(array $items): void { self::$items=$items; }
    public static function all(): array { return self::$items; }
    public static function merge(array $base): array
    {
        foreach (self::$items as $key=>$factor) $base[$key]=array_replace($base[$key] ?? [],$factor,['customized'=>true]);
        return $base;
    }
    public static function scope(array $catalog,string $type,bool $historical,string $part=''): array
    {
        foreach (self::$items as $key=>$factor) {
            $assigned=in_array($type,$factor['types'],true);
            if (($part==='terreno' && $factor['group']!=='Terreno') || ($part==='construccion' && $factor['group']==='Terreno')) $assigned=false;
            if ($assigned || ($historical && in_array($type,$factor['previous_types'],true))) {
                $catalog[$key]=ResearchFactorCatalog::all()[$key];
                $catalog[$key]['retired_assignment']=!$assigned;
            } else unset($catalog[$key]);
        }
        return $catalog;
    }
    public static function preserve(array $catalog,array $saved): array
    {
        foreach ($catalog as $key=>$factor) if (!empty($factor['retired_assignment']) && !isset($saved[$key])) unset($catalog[$key]);
        return $catalog;
    }
    public static function types(string $key): array
    {
        if (isset(self::$items[$key])) return self::$items[$key]['types'];
        $out=[];
        foreach (ComparablePortalProfiles::types() as $type=>$label) if (isset(ResearchFactorCatalog::forInvestigation($type)[$key])) $out[]=$type;
        return $out;
    }
}
