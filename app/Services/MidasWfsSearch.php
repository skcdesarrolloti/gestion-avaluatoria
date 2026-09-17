<?php
declare(strict_types=1);
namespace App\Services;

final class MidasWfsSearch
{
    public static function suggestions(array $subject): array
    {
        if (PHP_SAPI === 'cli') return [];
        $name = trim((string) ($subject['neighborhood_name'] ?? ''));
        if ($name === '') return [];
        $json = self::request(MidasWfsLayerCatalog::url('barrios'));
        return is_array($json) ? self::fromFeatureCollection($json, $name) : [];
    }

    public static function fromFeatureCollection(array $json, string $name): array
    {
        $record = self::bestFeature($json, $name);
        if ($record === []) return [];
        $flat = self::flatten($record);
        $section = [
            'pais' => 'Colombia',
            'departamento' => 'Bolívar',
            'municipio_distrito' => 'Cartagena de Indias',
            'barrio' => self::field($flat, ['barrio', 'nombre', 'territorio', 'nom_barrio']) ?: $name,
            'localidad' => self::field($flat, ['localidad', 'loc_nombre', 'nom_localidad']),
            'comuna' => self::field($flat, ['ucg', 'comuna', 'cod_ucg']),
            'fuente_base_delimitacion' => 'MIDAS Cartagena: capa Barrios.',
            'mapa_barrio_url' => 'https://midas.cartagena.gov.co/#/home',
        ];
        self::put($section, 'area_hectareas', self::number($flat, ['area_ha', 'area ha', 'area']));
        self::put($section, 'perimetro_metros', self::number($flat, ['perimetro_m', 'perimetro m', 'perimetro']));
        $norma = self::field($flat, ['fuente', 'norma', 'acto']);
        $suggestions = ['01' => array_filter($section, static fn ($v): bool => trim((string) $v) !== '')];
        if ($norma !== '') {
            $suggestions['05'] = [
                'norma_base' => $norma,
                'fuente_normativa' => 'MIDAS Cartagena / capa Barrios.',
                'midas_lectura_manual' => 'MIDAS reporta para ' . $name . ': ' . $norma . '.',
            ];
        }
        return $suggestions;
    }

    private static function request(string $url): ?array
    {
        if ($url === '') return null;
        $headers = "Accept: application/json,*/*\r\n"
            . "Referer: https://midas.cartagena.gov.co/\r\n"
            . "User-Agent: Mozilla/5.0\r\n";
        $context = stream_context_create(['http' => ['method' => 'GET', 'header' => $headers,
            'timeout' => 4, 'ignore_errors' => true]]);
        $response = @file_get_contents($url, false, $context);
        if (!is_string($response) || !str_starts_with(ltrim($response), '{')) return null;
        $json = json_decode($response, true);
        return is_array($json) ? $json : null;
    }

    private static function bestFeature(array $json, string $name): array
    {
        $features = is_array($json['features'] ?? null) ? $json['features'] : [];
        $needle = self::key($name);
        $best = [];
        $score = -1;
        foreach ($features as $feature) {
            if (!is_array($feature)) continue;
            $flat = self::flatten($feature);
            $text = self::key(implode(' ', array_map('strval', $flat)));
            $current = str_contains($text, $needle) ? 10 : 0;
            $current += self::hasAny($flat, ['area', 'perimetro', 'ucg', 'localidad', 'fuente']) ? 2 : 0;
            if ($current > $score) {
                $best = $feature;
                $score = $current;
            }
        }
        return $score > 0 ? $best : [];
    }

    private static function flatten(array $value, string $prefix = ''): array
    {
        $flat = [];
        foreach ($value as $key => $item) {
            $name = self::key($prefix . ' ' . (string) $key);
            if (is_array($item)) {
                $flat += self::flatten($item, $name);
            } elseif (is_scalar($item)) {
                $flat[$name] = trim((string) $item);
            }
        }
        return $flat;
    }

    private static function field(array $flat, array $aliases): string
    {
        foreach ($aliases as $alias) {
            $key = self::key($alias);
            foreach ($flat as $field => $value) {
                if (($field === $key || str_ends_with($field, $key)) && $value !== '') return $value;
            }
        }
        return '';
    }

    private static function number(array $flat, array $aliases): string
    {
        $value = self::field($flat, $aliases);
        if ($value === '') return '';
        $normalized = str_replace(',', '.', preg_replace('/[^\d,.-]/', '', $value) ?? '');
        return is_numeric($normalized) ? number_format((float) $normalized, 2, ',', '.') : $value;
    }

    private static function put(array &$target, string $field, string $value): void
    {
        if ($value !== '') $target[$field] = $value;
    }

    private static function hasAny(array $flat, array $needles): bool
    {
        $keys = implode(' ', array_keys($flat));
        foreach ($needles as $needle) if (str_contains($keys, self::key($needle))) return true;
        return false;
    }

    private static function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}
