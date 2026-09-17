<?php
declare(strict_types=1);
namespace App\Services;

final class MidasLiveSearch
{
    private const ENDPOINT = 'https://midas.cartagena.gov.co:2083/api/Search/Criterio';

    public static function suggestions(array $subject): array
    {
        if (PHP_SAPI === 'cli') return [];
        $name = trim((string) ($subject['neighborhood_name'] ?? ''));
        if ($name === '') return [];
        foreach (self::payloads($name) as [$body, $contentType]) {
            $json = self::request($body, $contentType);
            if (is_array($json)) {
                $result = self::fromResponse($json, $name);
                if ($result !== []) return $result;
            }
        }
        return [];
    }

    private static function fromResponse(array $json, string $name): array
    {
        $record = self::bestRecord($json, $name);
        if ($record === []) return [];
        $flat = self::flatten($record);
        $section = [
            'pais' => 'Colombia',
            'departamento' => 'Bolívar',
            'municipio_distrito' => 'Cartagena de Indias',
            'barrio' => self::field($flat, ['barrio', 'nombre_barrio', 'nombre', 'territorio']) ?: $name,
            'localidad' => self::field($flat, ['localidad', 'nom_localidad']),
            'comuna' => self::field($flat, ['ucg', 'comuna', 'codigo_ucg']),
            'fuente_base_delimitacion' => 'MIDAS Cartagena: búsqueda de territorios - ' . $name . '.',
            'mapa_barrio_url' => 'https://midas.cartagena.gov.co/#/home',
        ];
        self::put($section, 'area_hectareas', self::number($flat, ['area_ha', 'area_ha_', 'area', 'shape_area']));
        self::put($section, 'perimetro_metros', self::number($flat, ['perimetro_m', 'perimetro', 'shape_length']));
        $norma = self::field($flat, ['fuente', 'norma', 'norma_base']);
        $suggestions = ['01' => array_filter($section, static fn ($v): bool => trim((string) $v) !== '')];
        if ($norma !== '') {
            $suggestions['05'] = ['norma_base' => $norma, 'fuente_normativa' => 'MIDAS Cartagena',
                'midas_lectura_manual' => 'MIDAS reporta para ' . $name . ': ' . $norma . '.'];
        }
        return $suggestions;
    }

    private static function bestRecord(array $json, string $name): array
    {
        $records = [];
        self::collectRecords($json, self::key($name), $records);
        usort($records, static fn (array $a, array $b): int => ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0));
        unset($records[0]['_score']);
        return $records[0] ?? [];
    }

    private static function collectRecords(mixed $value, string $needle, array &$records): void
    {
        if (!is_array($value)) return;
        $text = self::key(implode(' ', array_map(static fn ($v): string => is_scalar($v) ? (string) $v : '', $value)));
        $score = str_contains($text, $needle) ? 1 : 0;
        $score += self::hasAnyKey($value, ['area', 'area_ha', 'perimetro', 'ucg', 'localidad', 'fuente']) ? 1 : 0;
        if ($score > 1) {
            $copy = $value;
            $copy['_score'] = $score;
            $records[] = $copy;
        }
        foreach ($value as $child) self::collectRecords($child, $needle, $records);
    }

    private static function request(string $body, string $contentType): ?array
    {
        $headers = "Content-Type: $contentType\r\nAccept: application/json, text/plain, */*\r\n"
            . "Origin: https://midas.cartagena.gov.co\r\nReferer: https://midas.cartagena.gov.co/\r\n";
        $context = stream_context_create(['http' => ['method' => 'POST', 'header' => $headers,
            'content' => $body, 'timeout' => 3, 'ignore_errors' => true]]);
        $response = @file_get_contents(self::ENDPOINT, false, $context);
        if (!is_string($response) || trim($response) === '') return null;
        $json = json_decode($response, true);
        return is_array($json) ? $json : null;
    }

    private static function payloads(string $name): array
    {
        return [
            [json_encode(['criterio' => $name], JSON_THROW_ON_ERROR), 'application/json'],
            [json_encode(['search' => $name], JSON_THROW_ON_ERROR), 'application/json'],
            [http_build_query(['criterio' => $name]), 'application/x-www-form-urlencoded'],
        ];
    }

    private static function flatten(array $record, string $prefix = ''): array
    {
        $flat = [];
        foreach ($record as $key => $value) {
            $name = self::key($prefix . ' ' . (string) $key);
            if (is_array($value)) {
                $flat += self::flatten($value, $name);
            } elseif (is_scalar($value)) {
                $flat[$name] = trim((string) $value);
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

    private static function hasAnyKey(array $record, array $keys): bool
    {
        $haystack = implode(' ', array_map('strval', array_keys($record)));
        foreach ($keys as $key) if (str_contains(self::key($haystack), self::key($key))) return true;
        return false;
    }

    private static function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}
