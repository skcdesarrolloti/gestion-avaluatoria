<?php
declare(strict_types=1);
namespace App\Services;

/** Facts explicitly labelled in a single source; never adopt coordinates or legal rights. */
final class ComparablePublishedDetails
{
    public static function parse(string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
        $out = ['intake_state'=>'review'];
        if ($text !== '') $out['evidence_detail'] = mb_substr($text, 0, 1520) . (mb_strlen($text) > 1520 ? ' [Texto abreviado; consultar anuncio original.]' : '');
        foreach (['private_built_m2'=>'área (?:privada construida|construida privada)', 'private_free_m2'=>'área privada libre',
            'built_m2'=>'área construida', 'land_m2'=>'área (?:del terreno|terreno)', 'stratum'=>'estrato',
            'floor_level'=>'(?:piso|nivel)', 'age_years'=>'(?:antigüedad|edad)'] as $key=>$label) {
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
        return $out;
    }
}
