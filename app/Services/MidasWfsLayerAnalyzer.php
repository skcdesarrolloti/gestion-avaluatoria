<?php
declare(strict_types=1);
namespace App\Services;

final class MidasWfsLayerAnalyzer
{
    public static function layerKeys(): array
    {
        return ['acueducto', 'alcantarillado', 'gas', 'aseo', 'alumbrado', 'rutas',
            'paraderos', 'educacion', 'deporte', 'salud', 'cai', 'cuadrantes', 'riesgo',
            'inundacion_pluvial'];
    }

    public static function fromCollections(array $collections, string $name): array
    {
        $neighborhood = MidasWfsSearch::neighborhoodFeature($collections['barrios'] ?? [], $name);
        if ($neighborhood === []) return [];
        $hits = [];
        foreach (self::layerKeys() as $key) {
            $hits[$key] = self::matching($collections[$key] ?? [], $neighborhood);
        }
        return self::toSuggestions($hits);
    }

    private static function toSuggestions(array $hits): array
    {
        $out = [];
        self::services($out, $hits);
        self::mobility($out, $hits);
        self::equipment($out, $hits);
        self::risks($out, $hits);
        return $out;
    }

    private static function services(array &$out, array $hits): void
    {
        $labels = [];
        foreach (['acueducto' => 'acueducto', 'alcantarillado' => 'alcantarillado',
            'gas' => 'gas natural', 'alumbrado' => 'alumbrado público'] as $key => $label) {
            if (($hits[$key] ?? []) === []) continue;
            $labels[] = $label;
            $out['03'][$key === 'alumbrado' ? 'energia' : $key] = 'SI';
            $field = $key === 'alumbrado' ? 'energia_detalle' : $key . '_detalle';
            $out['03'][$field] = self::countText($hits[$key], 'MIDAS reporta cobertura de ' . $label);
        }
        if (($hits['aseo'] ?? []) !== []) {
            $labels[] = 'aseo';
            $providers = self::providers($hits['aseo']);
            if ($providers !== []) $out['03']['aseo_prestadores'] = $providers;
            $out['03']['aseo_detalle'] = self::countText($hits['aseo'], 'MIDAS reporta recolección de residuos');
        }
        if ($labels !== []) {
            $out['03']['fuente_servicios'] = 'MIDAS Cartagena: ' . implode(', ', $labels) . '.';
            $out['16']['literal_g_servicios'] = 'Las capas MIDAS consultadas reportan información sectorial de '
                . implode(', ', $labels) . '; conservar como soporte y validar en visita cuando aplique.';
        }
    }

    private static function mobility(array &$out, array $hits): void
    {
        if (($hits['rutas'] ?? []) !== []) {
            $names = self::names($hits['rutas']);
            $out['11']['servicio_transporte_predominante'] = 'Transporte masivo y colectivo';
            $out['11']['tipos_transporte_identificados'] = ['Transcaribe alimentador', 'Bus urbano'];
            $out['11']['detalle_rutas_transporte'] = self::countText($hits['rutas'],
                'MIDAS/Transcaribe identifica rutas asociadas al barrio', $names);
        }
        if (($hits['paraderos'] ?? []) !== []) {
            $out['07']['amoblamiento_seleccionado'][] = 'Paradero de transporte';
            $out['11']['detalle_paraderos_transporte'] = self::countText($hits['paraderos'],
                'MIDAS/Transcaribe identifica paraderos asociados al barrio');
        }
        if (isset($out['11'])) {
            $parts = array_filter([$out['11']['detalle_rutas_transporte'] ?? '',
                $out['11']['detalle_paraderos_transporte'] ?? '']);
            $out['16']['literal_c_accesibilidad'] = implode(' ', $parts)
                . ' Validar accesos inmediatos, jerarquía vial y operación real frente al inmueble.';
        }
    }

    private static function equipment(array &$out, array $hits): void
    {
        $map = ['educacion' => ['Educativo', 'Educación'], 'deporte' => ['Deportivo', 'Recreativo / deportivo'],
            'salud' => ['Sanitario', 'Salud'], 'cai' => ['Institucional', 'Institucional']];
        $notes = [];
        foreach ($map as $key => [$facility, $category]) {
            if (($hits[$key] ?? []) === []) continue;
            $out['07']['equipamientos_seleccionados'][] = $facility;
            $out['12']['categorias_edificaciones'][] = $category;
            $notes[] = MidasWfsLayerCatalog::layers()[$key]['title'] . ': '
                . self::countText($hits[$key], 'elementos identificados', self::names($hits[$key]));
        }
        if (($hits['paraderos'] ?? []) !== []) $notes[] = 'Paraderos de transporte identificados.';
        if ($notes !== []) {
            $out['12']['edificaciones_ancla'] = implode(' ', $notes);
            $out['07']['comentario_amoblamiento'] = implode(' ', $notes);
            $out['13']['externalidades_positivas'] = ['Proximidad a equipamientos', 'Buena conectividad urbana'];
        }
        self::uniqueArrays($out);
    }

    private static function risks(array &$out, array $hits): void
    {
        $riskHits = array_merge($hits['riesgo'] ?? [], $hits['inundacion_pluvial'] ?? []);
        if ($riskHits === []) return;
        $text = self::countText($riskHits, 'MIDAS reporta capas de amenaza o riesgo intersectadas');
        $out['13']['externalidades_negativas'] = ['Riesgo de inundación'];
        $out['13']['observacion_externalidades'] = $text . '. Revisar tipo de amenaza antes de cerrar el avalúo.';
        $out['16']['literal_e_amenazas'] = $text . '. Validar con autoridad competente y visita de campo.';
    }

    private static function matching(array $collection, array $neighborhood): array
    {
        $features = is_array($collection['features'] ?? null) ? $collection['features'] : [];
        return array_values(array_filter($features,
            static fn ($feature): bool => is_array($feature) && MidasGeometry::intersectsFeature($feature, $neighborhood)));
    }

    private static function countText(array $features, string $prefix, array $names = []): string
    {
        $text = $prefix . ' (' . count($features) . ').';
        return $names === [] ? $text : $text . ' Referencias: ' . implode(', ', array_slice($names, 0, 8)) . '.';
    }

    private static function names(array $features): array
    {
        $names = [];
        foreach ($features as $feature) {
            $props = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
            foreach (['nombre', 'name', 'ruta', 'codigo', 'cod_ruta', 'descripcion', 'establecim'] as $key) {
                $value = trim((string) ($props[$key] ?? ''));
                if ($value !== '') { $names[] = $value; break; }
            }
        }
        return array_values(array_unique($names));
    }

    private static function providers(array $features): array
    {
        $text = mb_strtolower(json_encode($features, JSON_UNESCAPED_UNICODE) ?: '');
        return array_values(array_filter(['Pacaribe', 'Veolia'],
            static fn (string $provider): bool => str_contains($text, mb_strtolower($provider))));
    }

    private static function uniqueArrays(array &$out): void
    {
        foreach ($out as &$fields) {
            foreach ($fields as &$value) if (is_array($value)) $value = array_values(array_unique($value));
        }
    }
}
