<?php
declare(strict_types=1);
namespace App\Services;

final class ComparableTextAttributes
{
    public static function count(string $text, string $label): string
    {
        foreach ([
            '/(?:^|[^\d.,])([0-9]{1,3})\s*(?:'.$label.')(?=$|[\s\d:;,.)])/iu',
            '/(?:^|\b)(?:'.$label.')[\s:：=-]+([0-9]{1,3})(?![\d.,]|\s*(?:m[2²]|mt[s]?\.?[2²]|metros))/iu',
        ] as $pattern) if (preg_match($pattern,$text,$m)) return $m[1];
        return '';
    }

    public static function descriptions(string $text): array
    {
        $facts=[];
        foreach ([
            'Vista descrita'=>'vista\s+(?:panorámica|al mar|frontal al mar|paisajística|exterior|interior)[^.;\n]{0,65}',
            'Acabados descritos'=>'(?:acabados(?: de las oficinas)?\s*:[^.;]{1,160}|(?:excelentes|buenos|modernos) acabados)',
            'Servicios descritos'=>'(?:servicios (?:básicos|públicos)(?: de)?[^.;]{0,100}|agua y electricidad)',
            'Estado descrito'=>'estado del inmueble\s*:[^.;-]{1,65}',
        ] as $label=>$pattern) if (preg_match('/'.$pattern.'/iu',$text,$m)) $facts[$label]=mb_substr(trim($m[0]),0,240);
        foreach (['Ascensor'=>'ascensor(?:es)?','Acceso para discapacitados'=>'acceso para discapacitados',
            'Acceso pavimentado'=>'acceso pavimentado','Balcón'=>'balcón','Recepción'=>'recepción','Vigilancia'=>'vigilancia',
            'Aire acondicionado'=>'aire acondicionado','Planta eléctrica descrita'=>'planta eléctrica',
            'Piscina descrita'=>'piscina','Gimnasio descrito'=>'gimnasio','Terraza descrita'=>'terraza'] as $label=>$attribute) {
            if (!preg_match('/\b(?:'.$attribute.')\b/iu',$text)) continue;
            $negative=preg_match('/(?:sin|no (?:tiene|cuenta con|dispone de))\s+(?:'.$attribute.')\b/iu',$text);
            $facts[$label]=$negative ? 'No · descrito en el anuncio' : 'Mencionado en la descripción · verificar alcance';
        }
        return $facts;
    }
}
