<?php
declare(strict_types=1);
namespace App\Services;

/** Original portal labels; never statistical grades or verified location. */
final class ComparableSourceFacts
{
    public static function extract(string $text): array
    {
        $facts=[];
        foreach (preg_split('/[\n;]+/u',$text) ?: [] as $line) {
            if (count($facts)>=80) break;
            if (preg_match('/^([^:：]{2,80})[:：]\s*(.{1,500})$/u',trim($line),$m)
                && !preg_match('/https?$/i',$m[1])) $facts[trim($m[1])]=trim($m[2]);
        }
        return $facts;
    }
    public static function normalize(string $value): string
    {
        if ($value==='') return '';
        $facts=json_decode($value,true,4);
        if (!is_array($facts) || array_is_list($facts) && $facts!==[] || count($facts)>80)
            throw new \App\Core\HttpException(422,'Atributos publicados: formato inválido.');
        foreach ($facts as $label=>$fact) {
            if (!is_string($label) || mb_strlen($label)>80 || !is_string($fact) || mb_strlen($fact)>500)
                throw new \App\Core\HttpException(422,'Atributos publicados: usa etiquetas y valores de texto.');
        }
        return json_encode((object)$facts,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    }
    public static function structured(array $node,string $prefix='',int $depth=0): array
    {
        $facts=[];
        foreach ($node as $key=>$value) {
            if (count($facts)>=80) break;
            $label=mb_substr($prefix.(string)$key,0,80);
            if (is_scalar($value) && trim((string)$value)!=='') $facts[$label]=mb_substr(is_bool($value)?($value?'Sí':'No'):(string)$value,0,500);
            elseif (is_array($value) && $depth<2) $facts+=self::structured($value,$label.' · ',$depth+1);
        }
        return array_slice($facts,0,80,true);
    }
}
