<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalSpecialAttributeCatalog as Attributes;

/** Observed attributes can be researched; historical ratings and weights stay intact. */
final class SubjectAttributeResearch
{
    public const METADATA=['evidencia_fotografica','impacto_valuatorio','otro_atributo_especial'];
    public static function key(string $attribute): string { return 'd_'.substr(hash('sha256',$attribute),0,20); }
    public static function all(): array
    {
        static $definitions=null;
        if ($definitions!==null) return $definitions;
        $out=[];
        foreach (Attributes::allGroups() as [$group,$items]) foreach ($items as $key=>[$label,$help,$options]) {
            if (in_array($key,self::METADATA,true)) continue;
            $id=self::key($key);
            $out[$id]=['label'=>$label,'kind'=>'categorical','unit'=>'clase','subject'=>'research_'.$id,'sample'=>'research_'.$id,
                'why'=>$help.' Conservar clase observada y soporte; la calificación y el peso valuatorios anteriores son independientes.',
                'section'=>'attributes','categories'=>implode("\n",array_values(array_diff_key($options,[''=>true]))),
                'group'=>'Unidad privada','legacy_keys'=>[$key],'supplemental'=>true];
        }
        return $definitions=$out;
    }
    public static function catalog(array $base,string $type,string $part='',bool $historical=false): array
    {
        if ($base===[]) return [];
        $definitions=ResearchFactorCatalog::all();
        $map=['vista_vivienda'=>'view','vista_oficina'=>'view','acabados_interiores'=>'finish_quality',
            'altura_libre_comercial'=>'height','altura_libre_industrial'=>'height','frente_comercial'=>'frontage','piso_altura'=>'floor'];
        foreach (Attributes::groups($type) as $group=>[$label,$items]) {
            if ($part==='terreno' && !in_array($group,['comun','lote','finca_rural'],true)) continue;
            if ($part==='construccion' && $group==='lote') continue;
            foreach ($items as $key=>$item) {
                if (in_array($key,self::METADATA,true)) continue;
                $target=$map[$key] ?? '';
                if ($target!=='' && isset($base[$target]) && !str_starts_with($base[$target]['group'] ?? '','Datos anteriores')) {
                    $base[$target]['legacy_keys'][]=$key;
                } else $base[self::key($key)]=$definitions[self::key($key)];
            }
        }
        return UserResearchFactors::scope($base,$type,$historical,$part);
    }
    public static function previous(array $unit,array $factor): array
    {
        $saved=json_decode((string)($unit['special_attributes_json'] ?? '{}'),true) ?: [];
        $out=[];
        foreach (Attributes::allGroups() as [$group,$items]) foreach ($factor['legacy_keys'] ?? [] as $key) {
            if (!isset($items[$key],$saved[$key])) continue;
            $item=$saved[$key];
            $out[]=['label'=>$items[$key][0],'observed'=>$items[$key][2][$item['value'] ?? ''] ?? 'Pendiente',
                'rating'=>$item['rating'] ?? '', 'weight'=>$item['weight'] ?? '', 'notes'=>$item['notes'] ?? ''];
        }
        return $out;
    }
}
