<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormMidasUsageSearch
{
    private const ENDPOINT = 'https://midas.cartagena.gov.co:2083/api/Search/Criterio';

    public function consult(string $reference): array
    {
        $reference = trim($reference);
        if ($reference === '') throw new \InvalidArgumentException('Primero registra la referencia catastral en el bien sujeto.');
        $json = $this->request($reference);
        if (!is_array($json)) return ['ok' => false, 'message' => 'MIDAS no respondió. Abre MIDAS y registra la lectura manual.'];
        $record = $this->bestRecord($json, $reference);
        if ($record === []) return ['ok' => false, 'message' => 'MIDAS no devolvió un predio asociado a esa referencia.'];
        $flat = $this->flatten($record);
        $usage = $this->field($flat, ['uso_suelo', 'uso del suelo', 'uso', 'uso_principal', 'actividad', 'actividad_economica']);
        $zone = $this->field($flat, ['zona', 'zona_normativa', 'sector_normativo', 'area_actividad']);
        $treatment = $this->field($flat, ['tratamiento', 'tratamiento_urbanistico']);
        $classification = $this->field($flat, ['clasificacion', 'clasificacion_suelo', 'clase_suelo']);
        [$short, $long] = $this->references($flat, $reference);
        $summary = $this->summary($usage, $zone, $treatment, $classification);
        return ['ok' => $summary !== '', 'message' => $summary !== '' ? 'Consulta MIDAS incorporada al numeral 5.' : 'MIDAS respondió, pero no se identificó el campo de uso del suelo.',
            'fields' => ['midas_consulted' => '1', 'midas_query_option' => 'Uso del suelo',
                'midas_consulted_on' => date('Y-m-d'), 'midas_usage_result' => $summary,
                'midas_activity' => $usage, 'land_classification' => $classification,
                'activity_area' => $zone, 'urban_treatment' => $treatment,
                'cadastral_reference_short' => $short, 'cadastral_reference_long' => $long,
                'midas_support_reference' => 'Consulta MIDAS por referencia catastral ' . $reference,
                'source_status' => 'midas']];
    }

    private function request(string $reference): ?array
    {
        foreach ([['criterio' => $reference], ['search' => $reference], ['q' => $reference]] as $payload) {
            $headers = "Content-Type: application/json\r\nAccept: application/json, text/plain, */*\r\n"
                . "Origin: https://midas.cartagena.gov.co\r\nReferer: https://midas.cartagena.gov.co/\r\n";
            $context = stream_context_create(['http' => ['method' => 'POST', 'header' => $headers,
                'content' => json_encode($payload, JSON_THROW_ON_ERROR), 'timeout' => 8, 'ignore_errors' => true]]);
            $response = @file_get_contents(self::ENDPOINT, false, $context);
            $json = is_string($response) ? json_decode($response, true) : null;
            if (is_array($json)) return $json;
        }
        return null;
    }

    private function bestRecord(array $json, string $reference): array
    {
        $records = [];
        $this->collect($json, $this->key($reference), $records);
        usort($records, static fn (array $a, array $b): int => ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0));
        unset($records[0]['_score']);
        return $records[0] ?? [];
    }

    private function collect(mixed $value, string $needle, array &$records): void
    {
        if (!is_array($value)) return;
        $text = $this->key(implode(' ', array_map(static fn ($v): string => is_scalar($v) ? (string) $v : '', $value)));
        $score = str_contains($text, $needle) ? 2 : 0;
        $score += $this->hasKeys($value, ['uso', 'suelo', 'tratamiento', 'predial', 'referencia']) ? 1 : 0;
        if ($score > 1) $records[] = $value + ['_score' => $score];
        foreach ($value as $child) $this->collect($child, $needle, $records);
    }

    private function summary(string ...$parts): string
    {
        $text = array_filter($parts, static fn (string $part): bool => trim($part) !== '');
        return $text ? 'MIDAS reporta: ' . implode('; ', array_unique($text)) . '.' : '';
    }

    private function flatten(array $record, string $prefix = ''): array
    {
        $flat = [];
        foreach ($record as $key => $value) {
            $name = $this->key($prefix . ' ' . (string) $key);
            if (is_array($value)) $flat += $this->flatten($value, $name);
            elseif (is_scalar($value)) $flat[$name] = trim((string) $value);
        }
        return $flat;
    }

    private function field(array $flat, array $aliases): string
    {
        foreach ($aliases as $alias) foreach ($flat as $field => $value) {
            $key = $this->key($alias);
            if (($field === $key || str_ends_with($field, $key)) && $value !== '') return $value;
        }
        return '';
    }

    private function references(array $flat, string $queried): array
    {
        $numbers = [$queried];
        foreach ($flat as $key => $value) {
            if (!str_contains($key, 'predial') && !str_contains($key, 'catastral') && !str_contains($key, 'referencia')) continue;
            $digits = preg_replace('/\D+/', '', $value) ?? '';
            if ($digits !== '') $numbers[] = $digits;
        }
        $short = $long = '';
        foreach (array_unique($numbers) as $digits) {
            if (mb_strlen($digits) >= 20 && $long === '') $long = $digits;
            if (mb_strlen($digits) < 20 && $short === '') $short = $digits;
        }
        return [$short, $long];
    }

    private function hasKeys(array $record, array $keys): bool
    {
        $haystack = $this->key(implode(' ', array_keys($record)));
        foreach ($keys as $key) if (str_contains($haystack, $this->key($key))) return true;
        return false;
    }

    private function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}
