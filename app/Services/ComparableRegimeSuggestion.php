<?php
declare(strict_types=1);
namespace App\Services;

/** Advertisement clues guide prioritization; they never establish a legal regime. */
final class ComparableRegimeSuggestion
{
    public static function hint(array $row): array
    {
        $facts=json_decode((string)($row['published_attributes'] ?? ''),true) ?: [];
        $positive=false; $negative=false; $private=false;
        foreach ($facts as $label=>$value) {
            $label=self::normal((string)$label); $value=self::normal((string)$value);
            if (preg_match('/^(?:regimen(?: de)? |en )?(?:propiedad horizontal|ph)$/',$label)) {
                $positive=$positive || in_array($value,['si','ph','propiedad horizontal'],true);
                $negative=$negative || in_array($value,['no','no ph','sin ph'],true);
            }
            if (preg_match('/^area privada(?: construida| libre)?(?:\s*[·(].*)?$/u',$label) && self::positiveArea($value)) $private=true;
        }
        $text=self::normal(implode("\n",array_map(static fn($key)=>(string)($row[$key] ?? ''),['published_text','latest_source_excerpt','listing_title'])));
        $negativePattern='/\b(?:no (?:esta |es |se encuentra )?(?:sometid[oa] (?:a |al regimen de )?)?|sin )(?:propiedad horizontal|regimen ph|ph)\b/u';
        $negative=$negative || preg_match($negativePattern,$text)===1;
        $affirmative=preg_replace($negativePattern,'',$text);
        $positive=$positive || preg_match('/\b(?:sometid[oa] (?:a |al regimen de )|regimen de |bajo (?:el )?regimen de )propiedad horizontal\b/u',$affirmative)===1;
        $private=$private || (preg_match('/\barea privada(?: construida| libre)?\s*[:=]?\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|metros)/u',$text,$m)===1 && self::positiveArea($m[1]))
            || self::positiveArea((string)($row['private_built_m2'] ?? ''));
        if ($negative && ($positive || $private)) return ['regime'=>'','reason'=>'Indicios contradictorios: revisar PH y área privada'];
        if ($negative) return ['regime'=>'no','reason'=>'No PH anunciado · pendiente de soporte'];
        if ($positive) return ['regime'=>'si','reason'=>'PH anunciado · pendiente de soporte'];
        if ($private) return ['regime'=>'si','reason'=>'PH probable: área privada publicada · pendiente de soporte'];
        return ['regime'=>'','reason'=>'Sin indicios suficientes del régimen'];
    }

    private static function positiveArea(string $value): bool
    {
        return preg_match('/^\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|metros cuadrados)?\s*$/u',$value,$m)===1 && (float)str_replace(',','.',$m[1])>0;
    }

    private static function normal(string $value): string
    {
        return strtr(mb_strtolower(trim($value)),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
    }
}
