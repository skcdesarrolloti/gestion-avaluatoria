<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormMidasUsageSearch
{
    private const ENDPOINT = 'https://midas.cartagena.gov.co:2083/api/Ordenamiento/UsoSuelo';

    public function consult(string $reference): array
    {
        $reference = $this->digits($reference);
        if ($reference === '') throw new \InvalidArgumentException('Primero registra la referencia catastral en el bien sujeto.');
        $json = $this->request($reference);
        if (!is_array($json)) return ['ok' => false, 'message' => 'MIDAS no respondió. Usa el respaldo de pegar la lectura completa.'];
        $error = $this->errorMessage($json);
        if ($error !== '') return ['ok' => false, 'message' => 'MIDAS no respondió Uso Suelo: ' . $error];
        return $this->fieldsFromLandUseResponse($json, $reference);
    }

    public function fieldsFromLandUseResponse(array $json, string $reference): array
    {
        $datos = is_array($json['datos'] ?? null) ? $json['datos'] : [];
        if ($datos === []) return ['ok' => false, 'message' => 'MIDAS respondió, pero no devolvió la reglamentación de Uso Suelo.'];
        $headerHtml = (string) ($datos['encabezado'] ?? '');
        $tableRows = $this->tableRows((string) ($datos['cuadro'] ?? ''));
        $sections = $this->sections((string) ($datos['cuerpo'] ?? ''));
        $usage = $this->title($headerHtml) ?: $this->firstNonEmpty($tableRows);
        $headerText = $this->cleanHtml($headerHtml);
        $summary = trim('MIDAS reporta ' . $usage . ($headerText !== '' ? '. ' . $headerText : ''));
        [$short, $long] = $this->referenceFields($reference, (string) ($datos['referencia'] ?? ''));
        $fields = ['midas_consulted' => '1', 'midas_query_option' => 'Uso del suelo',
            'midas_consulted_on' => date('Y-m-d'), 'midas_usage_result' => $summary,
            'midas_activity' => $usage, 'current_use' => $usage, 'use_regulation_table' => $usage,
            'cadastral_reference_short' => $short, 'cadastral_reference_long' => $long,
            'midas_support_reference' => 'Consulta automática MIDAS Uso Suelo por referencia ' . $reference,
            'source_status' => 'midas', 'midas_usage_raw' => json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''];
        foreach ($this->targets() as $label => $field) $fields[$field] = $this->merge($tableRows[$label] ?? '', $sections[$label] ?? '');
        $hasUse = $usage !== '' || $fields['use_principal_text'] !== '' || $fields['use_compatible_text'] !== '';
        return ['ok' => $hasUse, 'message' => $hasUse
            ? 'Consulta MIDAS automática cargada: Uso Suelo quedó guardado en el numeral 5.'
            : 'MIDAS respondió, pero no se identificó el cuadro de Uso Suelo.', 'fields' => $fields];
    }

    private function request(string $reference): ?array
    {
        return (new MidasHttpClient())->postJson(self::ENDPOINT, ['criterio' => $reference]);
    }

    private function errorMessage(array $json): string
    {
        if (mb_strtolower((string) ($json['estado'] ?? '')) !== 'error') return '';
        return trim((string) ($json['mensaje'] ?? 'Error reportado por MIDAS.'));
    }

    private function tableRows(string $html): array
    {
        $rows = [];
        preg_match_all('/<tr[^>]*>\s*<td[^>]*class="[^"]*label[^"]*"[^>]*>(.*?)<\/td>\s*<td[^>]*class="[^"]*value[^"]*"[^>]*>(.*?)<\/td>\s*<\/tr>/is', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = $this->targetKey($this->cleanHtml($match[1]));
            if ($key !== '') $rows[$key] = $this->cleanHtml($match[2]);
        }
        return $rows;
    }

    private function sections(string $html): array
    {
        $sections = [];
        preg_match_all('/<h2[^>]*>\s*(USO\s+[^<]+)\s*<\/h2>(.*?)(?=<h2[^>]*>\s*USO\s+|\z)/is', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = $this->targetKey($this->cleanHtml($match[1]));
            if ($key !== '') $sections[$key] = $this->cleanHtml($match[2]);
        }
        return $sections;
    }

    private function targets(): array
    {
        return ['principal' => 'use_principal_text', 'compatible' => 'use_compatible_text',
            'complementario' => 'use_complementary_text', 'restringido' => 'use_restricted_text',
            'prohibido' => 'use_prohibited_text'];
    }

    private function targetKey(string $text): string
    {
        $key = $this->key(str_replace('USO ', '', $text));
        foreach (array_keys($this->targets()) as $target) if (str_contains($key, $target)) return $target;
        return '';
    }

    private function title(string $html): string
    {
        return preg_match('/<h2[^>]*>(.*?)<\/h2>/is', $html, $match) ? $this->cleanHtml($match[1]) : (strtok($this->cleanHtml($html), "\n") ?: '');
    }

    private function merge(string $row, string $section): string
    {
        $row = trim($row); $section = trim($section);
        if ($row === '') return $section;
        if ($section === '' || str_contains($this->key($section), $this->key($row))) return $row;
        return $row . "\n\n" . $section;
    }

    private function cleanHtml(string $html): string
    {
        $html = preg_replace('/<\s*(br|\/p|\/div|\/h[1-6]|\/tr)\b[^>]*>/i', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t\x{00a0}]+/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\n{2,}/', "\n", $text) ?? $text);
    }

    private function referenceFields(string $queried, string $html): array
    {
        $numbers = [$queried];
        preg_match_all('/\d{10,}/', $html, $matches);
        foreach ($matches[0] ?? [] as $number) $numbers[] = $number;
        $short = $long = '';
        foreach (array_unique($numbers) as $digits) {
            if (mb_strlen($digits) >= 20 && $long === '') $long = $digits;
            if (mb_strlen($digits) < 20 && $short === '') $short = $digits;
        }
        return [$short, $long];
    }

    private function firstNonEmpty(array $values): string
    { foreach ($values as $value) if (trim((string) $value) !== '') return (string) $value; return ''; }

    private function digits(string $value): string
    { return preg_replace('/\D+/', '', $value) ?? ''; }

    private function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}
