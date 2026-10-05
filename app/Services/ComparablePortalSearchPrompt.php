<?php
declare(strict_types=1);
namespace App\Services;

/** Search words and filter instructions, distinct from the preserved capture guide. */
final class ComparablePortalSearchPrompt
{
    public static function build(array $guide, array $source): array
    {
        $search=$guide['source_search'] ?? [];
        $parts=array_column($search['query_parts'] ?? [], 'value', 'label');
        $operation=trim((string)($parts['Operación'] ?? $guide['business_label'] ?? ''));
        $type=trim((string)($guide['type_label'] ?? ''));
        if ($type==='Tipología pendiente') $type='';
        $city=trim((string)($search['city'] ?? ''));
        $zone=trim((string)($search['neighborhood'] ?? '')) ?: trim((string)($parts['Localidad'] ?? ''));
        $label=$source['label'] ?? '';
        $domain=match($label) {
            'FincaRaiz','FincaRaíz'=>'fincaraiz.com.co', 'Metrocuadrado'=>'metrocuadrado.com',
            'Ciencuadras'=>'ciencuadras.com', 'Properati'=>'properati.com.co',
            'Mercado Libre Inmuebles'=>'mercadolibre.com.co', default=>'',
        };
        $query=implode(' ',array_filter([$type,$operation,$zone,$city],static fn($v)=>$v!==''));
        $locationOnly=in_array($label,['FincaRaiz','FincaRaíz'],true);
        $input=$locationOnly ? ($zone ?: $city) : $query;
        $category=$label==='Properati' && in_array($type,['Oficina','Consultorio'],true) ? 'Oficinas / Consultorios (verifica el uso del aviso)' : $type;
        $filters=implode(' → ',array_filter([$operation,$category,$city,$zone],static fn($v)=>$v!==''));
        $help=match($label) {
            'FincaRaiz','FincaRaíz'=>'En «Busca por ubicación o palabra clave», pega sólo el barrio y selecciona la sugerencia de Barrio correspondiente a tu ciudad. Si no tienes barrio, selecciona la ciudad. Usa operación y tipo en sus filtros. Quita cualquier «Palabra clave» anterior: no pegues la frase completa ni pulses Enter sin elegir la ubicación.',
            'Metrocuadrado'=>'Selecciona operación, tipo y ciudad; comprueba el barrio o sector en los resultados y en la ficha.',
            'Ciencuadras'=>'Selecciona operación, tipo y ubicación. Revisa variantes del nombre del barrio, como Bocagrande / Boca Grande.',
            'Properati'=>'Usa operación, categoría y ubicación; comprueba ciudad y barrio de cada aviso de la red.',
            'Mercado Libre Inmuebles'=>'Prueba el texto breve en el buscador y comprueba categoría Inmuebles, operación y ubicación. La coincidencia por palabras no confirma el barrio.',
            default=>'Selecciona los filtros que ofrezca esta inmobiliaria y comprueba la ubicación de cada aviso.',
        };
        $alternatives=[];
        $typeLower=mb_strtolower($type);
        if (str_contains($typeLower,'parqueadero') || str_contains($typeLower,'garaje')) {
            foreach (['garaje','parqueadero','celda de parqueo'] as $term) $alternatives[]=trim($term.' '.$operation.' '.$zone.' '.$city);
        } elseif (str_contains($typeLower,'depósito') || str_contains($typeLower,'deposito')) {
            foreach (['depósito','cuarto útil','bodega de almacenamiento'] as $term) $alternatives[]=trim($term.' '.$operation.' '.$zone.' '.$city);
        }
        return ['query'=>$query, 'input'=>$input, 'location_only'=>$locationOnly,
            'google'=>$domain!==''?'site:'.$domain.' '.$query:'', 'filters'=>$filters,
            'help'=>$help, 'alternatives'=>$alternatives, 'missing'=>$type==='' || $operation==='' || $city===''];
    }
}
