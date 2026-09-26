<?php
declare(strict_types=1);
namespace App\Support;

final class UrbanNormPotentialCatalog
{
    public static function standards(): array
    {
        $rows = [
            'res-generico' => self::manual('Residencial por definir', 'Cuadro No. 1', 'Definir Residencial A, B, C o D antes de calcular modalidad, AML, frente e indice.'),
            'com-1' => self::manual('Comercial 1', 'Cuadro No. 3', 'Rige por indicadores del area residencial de la cual forma parte.'),
            'com-2' => self::standard('Comercial 2', 'Cuadro No. 3', ['general' => ['Uso comercial 2', 250, 10, 1.0, '2 pisos', '1 m2 libre por cada 1.5 m2 construidos']]),
            'com-3' => self::standard('Comercial 3', 'Cuadro No. 3', ['general' => ['Uso comercial 3', 250, 10, 2.4, '2 pisos', '1 m2 libre por cada 4 m2 construidos']]),
            'com-4' => self::manual('Comercial 4', 'Cuadro No. 3', 'AML 260 m2 y frente 10 m; plataforma basica 85%. Requiere concertacion con Planeacion y autoridad competente.'),
            'inst-1' => self::manual('Institucional 1', 'Cuadro No. 2', 'Frente minimo 12 m; indice por establecimiento y tabla de pisos. Requiere revisar actividad dotacional.'),
            'inst-2' => self::manual('Institucional 2', 'Cuadro No. 2', 'Frente minimo 20 m; autosuficiencia de estacionamientos y tabla institucional.'),
            'inst-3' => self::manual('Institucional 3', 'Cuadro No. 2', 'Frente minimo 30 m; revisar impacto, estacionamientos y condiciones del establecimiento.'),
            'inst-4' => self::manual('Institucional 4', 'Cuadro No. 2', 'Frente minimo 50 m; requiere soporte especifico por escala e impacto.'),
            'ind-1' => self::standard('Industrial 1', 'Cuadro No. 4', ['general' => ['Uso industrial 1', 600, 20, 1.3, '2 pisos', 'Ocupacion hasta 60% del lote']]),
            'ind-2' => self::standard('Industrial 2', 'Cuadro No. 4', ['general' => ['Uso industrial 2', 2000, 30, 1.4, '3 pisos', 'Ocupacion hasta 70% del lote']]),
            'ind-3' => self::standard('Industrial 3', 'Cuadro No. 4', ['general' => ['Uso industrial 3', 2500, 40, 1.0, '3 pisos', 'Ocupacion hasta 50% del lote']]),
            'tur-bocagrande-boquilla' => self::standard('Turistica Bocagrande y La Boquilla', 'Cuadro No. 5', [
                'lote_1' => ['Lote 1', 960, 24, 2.4, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos'],
                'lote_2' => ['Lote 2', 1440, 24, 3.5, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos'],
                'lote_3' => ['Lote 3', 3000, 50, 2.0, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos'],
            ]),
            'port-1' => self::manual('Portuario 1', 'Cuadro No. 6', 'Aplican Ministerio de Transporte, DIMAR, autoridad ambiental y condiciones del terminal.'),
            'port-2' => self::manual('Portuario 2', 'Cuadro No. 6', 'Definir con norma portuaria, ambiental y urbanistica especifica.'),
            'port-3' => self::manual('Portuario 3', 'Cuadro No. 6', 'Definir con autoridad portuaria, ambiental y urbanistica.'),
            'port-4' => self::manual('Portuario 4', 'Cuadro No. 6', 'Definir con autoridad portuaria, ambiental y urbanistica.'),
            'rural-suburbano-turistico' => self::standard('Rural suburbano turistico', 'Cuadro No. 8', [
                'ha_1_10' => ['1 a 10 ha', 10000, 30, null, '1 piso', 'Indice de ocupacion 10%; potencial requiere cabida'],
                'ha_10_30' => ['10.1 a 30 ha', 101000, 100, null, '2 pisos', 'Indice de ocupacion 10%; potencial requiere cabida'],
                'ha_mas_30' => ['Mas de 30 ha', 300001, 200, null, '2 pisos', 'Indice de ocupacion 10%; potencial requiere cabida'],
            ]),
            'rural-parcelaciones' => self::manual('Parcelaciones rurales', 'Cuadro No. 9', 'AML 10 ha; parcela minima 10.000 m2; area libre y aislamientos condicionan cabida.'),
            'rural-agroindustrial' => self::manual('Agroindustrial rural', 'Cuadro No. 9', 'AML 10.000 m2 y frente 70 m; area libre 5 m2 por cada 2 m2 construidos.'),
        ];
        foreach (UrbanResidentialNormCatalog::standards() as $slug => $standard) {
            $rows[$slug] = ['label' => $standard['label'], 'table' => 'Cuadro No. 1',
                'status' => 'calculable', 'options' => $standard['data']];
        }
        return $rows;
    }

    public static function routesForProfile(array $profile): array
    {
        $standards = self::standards();
        $routes = [];
        foreach (['principal' => 'use_principal_text', 'compatible' => 'use_compatible_text'] as $type => $field) {
            foreach (self::slugsFromText((string) ($profile[$field] ?? ''), (string) ($profile['category_slug'] ?? '')) as $slug) {
                if (!isset($standards[$slug])) continue;
                $routes[$type . ':' . $slug] = ['type' => $type, 'slug' => $slug] + $standards[$slug];
            }
        }
        return array_values($routes);
    }

    private static function standard(string $label, string $table, array $options): array
    {
        foreach ($options as &$option) $option = [
            'label' => $option[0], 'min_area_m2' => $option[1], 'min_front_m' => $option[2],
            'construction_index' => $option[3], 'height' => $option[4], 'free_area' => $option[5],
        ];
        return ['label' => $label, 'table' => $table, 'status' => 'calculable', 'options' => $options];
    }

    private static function manual(string $label, string $table, string $note): array
    {
        return ['label' => $label, 'table' => $table, 'status' => 'manual',
            'options' => ['manual' => ['label' => 'Revision tecnica', 'min_area_m2' => null,
                'min_front_m' => null, 'construction_index' => null, 'height' => '', 'free_area' => $note]]];
    }

    private static function slugsFromText(string $text, string $categorySlug): array
    {
        $haystack = self::plain($text);
        if ($haystack === '') return [];
        $slugs = [];
        if (str_contains($haystack, 'residencial')) $slugs[] = str_starts_with($categorySlug, 'res-') ? $categorySlug : 'res-generico';
        foreach ([
            '(?:comercial|comercio)' => 'com',
            'industrial' => 'ind',
            'institucional' => 'inst',
            'portuari[oa]' => 'port',
        ] as $pattern => $prefix) {
            if (!preg_match_all('/\b' . $pattern . '\s*(?:tipo\s*)?([0-9,\sy]+)\b/u', $haystack, $matches)) continue;
            foreach ($matches[1] as $group) foreach (self::numbers($group) as $number) $slugs[] = $prefix . '-' . $number;
        }
        if (str_contains($haystack, 'turistic')) $slugs[] = 'tur-bocagrande-boquilla';
        if (str_contains($haystack, 'agroindustrial')) $slugs[] = 'rural-agroindustrial';
        if (str_contains($haystack, 'parcelacion')) $slugs[] = 'rural-parcelaciones';
        if (str_contains($haystack, 'rural') && str_contains($haystack, 'suburbano')) $slugs[] = 'rural-suburbano-turistico';
        return array_values(array_unique($slugs));
    }

    private static function plain(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        return trim(preg_replace('/[^a-z0-9]+/u', ' ', $text) ?? '');
    }

    private static function numbers(string $value): array
    {
        preg_match_all('/\b([1-5])\b/u', $value, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }
}
