<?php
declare(strict_types=1);
namespace App\Services;

/** Public technical sheet and facilities for the exact listing, not search filters. */
final class FincaraizFichaDetails
{
    public static function parse(\DOMDocument $doc,string $url): array
    {
        $script=(new \DOMXPath($doc))->query('//script[@id="__NEXT_DATA__"]')->item(0);
        if (!$script) return [];
        $page=json_decode($script->textContent,true,64)['props']['pageProps'] ?? [];
        $data=$page['data'] ?? [];
        if (!is_array($data) || (string)($data['id'] ?? '')!==basename($url)
            || ($data['link'] ?? '')!==parse_url($url,PHP_URL_PATH)) return [];
        $text=is_string($data['description'] ?? null)?$data['description']:'';
        $facts=[]; $row=[];
        // Portal owner identifies the publisher, not legal ownership of the property.
        $publisher=is_array($data['owner'] ?? null)?$data['owner']:[];
        $name=is_string($publisher['name'] ?? null)?mb_substr(trim(strip_tags($publisher['name'])),0,120):'';
        if ($name!=='') {
            $row['contact_name']=$name;
            $facts['Anunciante']=$name;
            $text.="\nAnunciante: ".$name.';';
            if (is_string($publisher['type'] ?? null) && trim($publisher['type'])!=='') {
                $facts['Tipo de anunciante']=mb_substr(trim(strip_tags($publisher['type'])),0,80);
                $text.="\nTipo de anunciante: ".$facts['Tipo de anunciante'].';';
            }
        }
        $fields=['bathrooms'=>'bathrooms','garage'=>'parking_spaces','floor'=>'floor_level','m2Built'=>'built_m2'];
        foreach (is_array($data['technicalSheet'] ?? null)?$data['technicalSheet']:[] as $entry) {
            if (!is_array($entry) || !is_string($entry['text'] ?? null) || !is_scalar($entry['value'] ?? null)) continue;
            $label=mb_substr(trim($entry['text']),0,80); $value=mb_substr(trim((string)$entry['value']),0,500);
            if ($label==='' || $value==='') continue;
            $facts[$label]=$value; $text.="\n".$label.': '.$value.';';
            $field=$fields[$entry['field'] ?? ''] ?? '';
            if ($field && preg_match('/^\d+(?:[.,]\d+)?(?:\s*m[²2])?$/u',$value)) $row[$field]=preg_replace('/\s*m[²2]$/u','',str_replace(',','.',$value));
        }
        foreach (is_array($data['facilities'] ?? null)?$data['facilities']:[] as $facility) {
            if (!is_array($facility) || !is_string($facility['name'] ?? null)) continue;
            $label=mb_substr(trim($facility['name']),0,80);
            if ($label==='') continue;
            $group=is_string($facility['group'] ?? null)?$facility['group']:'';
            $facts[$label]=mb_substr('Publicado en ficha'.($group?' · '.$group:'').' · verificar alcance',0,500);
            $text.="\n".$label.': '.$facts[$label].';';
        }
        $parsed=ComparablePublishedDetails::parse($text);
        $parsed['published_attributes']=json_encode((object)array_slice($facts+json_decode($parsed['published_attributes'],true),0,80,true),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        // A published age interval is not a measured age and a private area is not private built area.
        if (!empty($facts['Antigüedad']) && !preg_match('/^\d+(?:[.,]\d+)?(?:\s*años?)?$/u',$facts['Antigüedad'])) unset($parsed['age_years']);
        return $row+$parsed;
    }
}
