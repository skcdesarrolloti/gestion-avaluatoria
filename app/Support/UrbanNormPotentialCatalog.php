<?php
declare(strict_types=1);
namespace App\Support;

final class UrbanNormPotentialCatalog
{
    public static function standards(): array
    {
        $rows = [
            'res-generico' => self::manual('Residencial por definir', 'Cuadro No. 1', 'Definir Residencial A, B, C o D antes de calcular modalidad, AML, frente e indice.'),
            'com-1' => self::manual('Comercial 1', 'Cuadro No. 3', 'Rige por indicadores del area residencial de la cual forma parte; verificar la residencial adoptada antes de calcular.'),
            'com-2' => self::standard('Comercial 2', 'Cuadro No. 3', ['general' => [
                'Uso comercial 2', 250, 10, 1.0, '2 pisos', '1 m2 libre por cada 1.5 m2 construidos',
                '1 estacionamiento por cada 50 m2 de local comercial u oficina.',
                'Aislamientos residenciales del area de la cual forma parte.',
                null,
                'Locales especializados, vitrinas, zona de descargue y parqueos de empleados; intercambio comercial pausado.',
            ]]),
            'com-3' => self::standard('Comercial 3', 'Cuadro No. 3', ['general' => [
                'Uso comercial 3', 250, 10, 2.4, '2 pisos', '1 m2 libre por cada 4 m2 construidos',
                '1 estacionamiento por cada 50 m2 de local comercial u oficina y visitantes 1 por cada 5 unidades o fraccion superior a 3.',
                'Aislamientos residenciales del area de la cual forma parte.',
                null,
                'Centros o conjuntos comerciales con zonas comunales, descargue y parqueos para empleados y visitantes.',
            ]]),
            'com-4' => self::standard('Comercial 4', 'Cuadro No. 3', ['general' => [
                'Uso comercial 4', 260, 10, null, 'Concertada con Planeacion y autoridad competente',
                'Plataforma basica 85%; 100% solo si asegura iluminacion, ventilacion y acceso a bomberos.',
                'Zona de descargue y parqueos para empleados y visitantes.',
                'Concertar aislamientos, plataforma, mezanine y alturas segun autoridad competente.',
                0.85,
                'Instalaciones de impacto intermunicipal o regional; no compatible con uso residencial.',
            ]]),
            'inst-1' => self::standard('Institucional 1', 'Cuadro No. 2', [
                '1_piso' => ['1 piso', null, 12, 0.6, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; segun actividad.', 'Antejardin vial; posterior 7 m; lateral segun barrio o 2.5 m.', null, 'Cobertura local, bajo impacto; no debe generar congestion ni usos complementarios significativos.'],
                '2_pisos' => ['2 pisos', null, 12, 1.2, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 7 m; lateral segun barrio o 2.5 m.', null, 'Revisar si la actividad puede ubicarse dentro de vivienda o exige predio independiente.'],
            ]),
            'inst-2' => self::standard('Institucional 2', 'Cuadro No. 2', [
                '1_piso' => ['1 piso', null, 20, 0.6, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 3 m.', null, 'Cobertura zonal compatible con residencial; revisar actividad dotacional.'],
                '2_pisos' => ['2 pisos', null, 20, 1.2, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 3 m.', null, 'Puede requerir englobe o soporte de actividad.'],
                'hasta_4_pisos' => ['Hasta 4 pisos', null, 20, 1.8, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 3 m.', null, 'Ruta condicionada por actividad y cabida.'],
            ]),
            'inst-3' => self::standard('Institucional 3', 'Cuadro No. 2', [
                '1_piso' => ['1 piso', null, 30, 0.6, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 7 m; lateral 4 m.', null, 'Cobertura distrital; debe ubicarse en area de actividad mixta.'],
                '2_pisos' => ['2 pisos', null, 30, 1.2, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 7 m; lateral 4 m.', null, 'Requiere revisar impacto, actividad y soporte oficial.'],
                'hasta_4_pisos' => ['Hasta 4 pisos', null, 30, 1.8, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 7 m; lateral 4 m.', null, 'Requiere cabida y cumplimiento de estacionamientos.'],
                'mayor_altura' => ['Mayores alturas', null, 30, 2.4, 'Condicionada por area libre, indice y ascensor', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 7 m; lateral 4 m.', null, 'Mayor altura solo con soporte tecnico y oficial.'],
            ]),
            'inst-4' => self::standard('Institucional 4', 'Cuadro No. 2', [
                '1_piso' => ['1 piso', null, 50, 0.6, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 5 m.', null, 'Alto impacto; area especializada; no compatible con residencial.'],
                '2_pisos' => ['2 pisos', null, 50, 1.2, 'Segun area libre e indice; ascensor si supera 4 pisos', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 5 m.', null, 'Debe evaluarse por actividad y autoridad competente.'],
                'mayor_altura' => ['Mayores alturas', null, 50, 2.4, 'Condicionada por area libre, indice y ascensor', '1 m2 libre por cada 1.5 m2 construidos', 'Autosuficiente dentro del lote; visitantes segun actividad.', 'Antejardin vial; posterior 10 m; lateral 5 m.', null, 'Mayor altura solo con soporte tecnico y oficial.'],
            ]),
            'ind-1' => self::standard('Industrial 1', 'Cuadro No. 4', ['general' => ['Uso industrial 1', 600, 20, 1.3, '2 pisos', 'Ocupacion hasta 60% del lote', '1 cupo privado por cada 30 m2 construidos y visitantes 1 por cada cupo privado.', 'Antejardin 7 m vias principales y 5 m secundarias; patio interior 15 m2 minimo.', 0.60, 'Bajo impacto ambiental y urbanistico; maximo 3 empleados segun clasificacion.']]),
            'ind-2' => self::standard('Industrial 2', 'Cuadro No. 4', ['general' => ['Uso industrial 2', 2000, 30, 1.4, '3 pisos en administracion y operacion', 'Ocupacion hasta 70% del lote', '15% del lote para cargue, descargue y estacionamiento; 2 cupos privados y visitantes 1 por cada 2 privados.', 'Antejardin 10 m vias principales y 5 m secundarias; patio interior 25 m2 minimo.', 0.70, 'Mediano impacto; no compatible con residencial; requiere manejo de molestias.']]),
            'ind-3' => self::standard('Industrial 3', 'Cuadro No. 4', ['general' => ['Uso industrial 3', 2500, 40, 1.0, '3 pisos en administracion y operacion', 'Ocupacion hasta 50% del lote', '15% del lote para cargue, descargue y estacionamiento; 2 cupos privados y visitantes 1 por cada 2 privados.', '10 m sobre cada lindero; patio interior 42 m2 minimo.', 0.50, 'Alto impacto; requiere sectores especializados y control ambiental.']]),
            'tur-bocagrande-boquilla' => self::standard('Turistica Bocagrande y La Boquilla', 'Cuadro No. 5', [
                'lote_1' => ['Lote 1', 960, 24, 2.4, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos', 'Bocagrande: 2 garajes por apartamento; visitantes 3 por cada 5 aptos; comercio 1 por cada 50 m2.', 'Antejardin 9 m vias principales y 7 m secundarias; posterior 5 m; lateral 3.5 m o 5 m en La Boquilla.', null, 'Turistico Bocagrande - La Boquilla.'],
                'lote_2' => ['Lote 2', 1440, 24, 3.5, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos', 'La Boquilla: 1 garaje por cada 3 habitaciones; visitantes 1 por cada 5 habitaciones; taxi 1 por cada 20 habitaciones.', 'Antejardin 9 m vias principales y 7 m secundarias; posterior 5 m; lateral 3.5 m o 5 m en La Boquilla.', null, 'Turistico Bocagrande - La Boquilla.'],
                'lote_3' => ['Lote 3', 3000, 50, 2.0, 'Segun area libre e indice', '1 m2 libre por cada 0.80 m2 construidos', 'Estacionamientos segun producto: vivienda, hotel, oficina o comercio.', 'Antejardin 9 m vias principales y 7 m secundarias; posterior 5 m; lateral 3.5 m o 5 m en La Boquilla.', null, 'Turistico Bocagrande - La Boquilla.'],
            ]),
            'port-1' => self::manual('Portuario 1', 'Cuadro No. 6', 'Aplican Ministerio de Transporte, DIMAR, autoridad ambiental y condiciones del terminal.'),
            'port-2' => self::manual('Portuario 2', 'Cuadro No. 6', 'Definir con norma portuaria, ambiental y urbanistica especifica.'),
            'port-3' => self::manual('Portuario 3', 'Cuadro No. 6', 'Definir con autoridad portuaria, ambiental y urbanistica.'),
            'port-4' => self::manual('Portuario 4', 'Cuadro No. 6', 'Definir con autoridad portuaria, ambiental y urbanistica.'),
            'rural-suburbano-turistico' => self::standard('Rural suburbano turistico', 'Cuadro No. 8', [
                'ha_1_10' => ['1 a 10 ha', 10000, 30, null, '1 piso', 'Area libre 90%; indice de ocupacion 10%; potencial requiere cabida', 'Residentes 1 por vivienda; comercio/oficina 1 por cada 80 m2; hotel 1 por cada 200 m2 o 5 trabajadores.', 'Aislamiento 10 m minimo en cada lindero.', 0.10, 'Actividad suburbana: turistica, residencial y vivienda temporal.'],
                'ha_10_30' => ['10.1 a 30 ha', 101000, 100, null, '2 pisos', 'Area libre 90%; indice de ocupacion 10%; potencial requiere cabida', 'Residentes 1 por vivienda; comercio/oficina 1 por cada 80 m2; hotel 1 por cada 200 m2 o 5 trabajadores.', 'Aislamiento 10 m minimo en cada lindero.', 0.10, 'Actividad suburbana: turistica, residencial y vivienda temporal.'],
                'ha_mas_30' => ['Mas de 30 ha', 300001, 200, null, '3 pisos', 'Area libre 90%; indice de ocupacion 10%; potencial requiere cabida', 'Residentes 1 por vivienda; comercio/oficina 1 por cada 80 m2; hotel 1 por cada 200 m2 o 5 trabajadores.', 'Aislamiento 10 m minimo en cada lindero.', 0.10, 'Permite flexibilidad entre 2 y 3 pisos mas altillo sin exceder indice.'],
            ]),
            'rural-parcelaciones' => self::standard('Parcelaciones rurales', 'Cuadro No. 9', ['general' => ['Residencial unifamiliar rural', 100000, 50, null, 'Condicionada por norma rural', '20 m2 libres por cada 20 m2 construidos', 'Acceso vehicular a cada parcela; estacionamientos internos.', 'Aislamiento 10 m por cualquier lindero; parcela minima 10.000 m2.', null, 'No se permite subdividir por debajo de parcela minima; revisar UAF si aplica.']]),
            'rural-agroindustrial' => self::standard('Agroindustrial rural', 'Cuadro No. 9', ['general' => ['Actividad rural productora agroindustrial', 10000, 70, null, 'Condicionada por actividad', '5 m2 libres por cada 2 m2 construidos', 'Cargue, maniobras y estacionamientos dentro del predio segun actividad.', 'Aislamiento 10 m por cualquier lindero.', null, 'Labores agricolas, bodegas, silos, establos y plantas de procesamiento.']]),
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
            'parking' => $option[6] ?? '', 'isolation' => $option[7] ?? '',
            'occupancy_index' => $option[8] ?? null, 'scope' => $option[9] ?? '',
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
