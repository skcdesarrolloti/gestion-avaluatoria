<?php
declare(strict_types=1);
namespace App\Services;

/** Facts explicitly labelled in a single source; never adopt coordinates or legal rights. */
final class ComparablePublishedDetails
{
    public static function parse(string $text): array
    {
        $original=trim(strip_tags($text));
        $facts=ComparableSourceFacts::extract($original);
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
        $out = ['intake_state'=>'review','published_attributes'=>json_encode((object)$facts,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),
            'published_text'=>mb_substr($original,0,15920).(mb_strlen($original)>15920?' [Texto abreviado; consultar ficha original.]':'')];
        if ($text !== '') $out['evidence_detail'] = mb_substr($text, 0, 1520) . (mb_strlen($text) > 1520 ? ' [Texto abreviado; consultar anuncio original.]' : '');
        foreach (['private_built_m2'=>'área (?:privada construida|construida privada)', 'private_free_m2'=>'área privada libre',
            'built_m2'=>'área construida', 'land_m2'=>'área (?:del terreno|terreno)', 'stratum'=>'estrato',
            'floor_level'=>'(?:piso|nivel)', 'age_years'=>'(?:antigüedad|edad)','bathrooms'=>'baños?', 'bedrooms'=>'(?:habitaciones?|alcobas?)'] as $key=>$label) {
            if (preg_match('/(?:' . $label . ')\s*[:：-]?\s*(\d+(?:[.,]\d+)?)/iu', $text, $m)) $out[$key] = str_replace(',', '.', $m[1]);
        }
        foreach (['parking_spaces'=>'(?:parqueaderos?|garajes?|celdas? de parqueo)', 'ph_deposit_count'=>'(?:depósitos?|cuartos? útiles?)'] as $key=>$label) {
            if (preg_match('/' . $label . '\s*[:：-]?\s*(\d{1,3})\b/iu', $text, $m)
                || preg_match('/\b(\d{1,3})\s+' . $label . '\b/iu', $text, $m)) $out[$key] = $m[1];
        }
        if (isset($out['parking_spaces'])) $out['ph_parking_presence'] = (int) $out['parking_spaces'] > 0 ? 'si' : 'no';
        if (isset($out['ph_deposit_count'])) $out['ph_deposit_presence'] = (int) $out['ph_deposit_count'] > 0 ? 'si' : 'no';
        if (preg_match('/administración\s*[:：-]?\s*\$\s*([\d.,]+)/iu', $text, $m)) $out['admin_fee'] = str_replace(['.', ','], ['', '.'], $m[1]);
        if (preg_match('/(?:teléfono|celular|whatsapp|contacto)\s*[:：-]?\s*(\+?[\d ()-]{7,20})/iu', $text, $m)) $out['contact_phone'] = trim($m[1]);
        if (isset($out['private_built_m2']) || isset($out['built_m2'])) $out['areas_source'] = 'Área rotulada en el anuncio; pendiente de corroboración.';
        if (isset($out['parking_spaces']) || isset($out['ph_deposit_count'])) $out['ph_components_source'] = 'Cantidad publicada; inclusión en precio y naturaleza jurídica pendientes.';
        $aliases=['vista'=>'view_quality','acabados'=>'finish_quality','ascensor'=>'elevator','amenidades'=>'amenities','seguridad'=>'security_features',
            'planta eléctrica'=>'research_generator','servicios públicos'=>'market_services','tipo de acceso'=>'research_access',
            'alcoba de servicio'=>'research_service','niveles'=>'research_levels','altura libre'=>'research_height'];
        foreach ($facts as $label=>$value) {
            $field=$aliases[mb_strtolower($label)] ?? '';
            if ($field==='') continue;
            if (in_array($field,['research_levels','research_height'],true)) {
                if (!preg_match('/^\d+(?:[.,]\d+)?(?:\s*m)?$/D',$value)) continue;
                $value=preg_replace('/\s*m$/','',$value);
            }
            $out[$field]=mb_substr($value,0,['elevator'=>20,'view_quality'=>80,'finish_quality'=>80,'amenities'=>240,'security_features'=>180][$field] ?? 500);
        }
        return $out;
    }
}
