<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalEducationMidasReader
{
    public function rows(string $path): array
    {
        if ($path === '' || !is_file($path) || !class_exists(\ZipArchive::class)) return [];
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];
        try {
            $strings = $this->sharedStrings((string) $zip->getFromName('xl/sharedStrings.xml'));
            $sheet = (string) ($zip->getFromName('xl/worksheets/sheet1.xml') ?: '');
            return $this->sheetRows($sheet, $strings);
        } finally {
            $zip->close();
        }
    }

    private function sharedStrings(string $xml): array
    {
        if ($xml === '') return [];
        preg_match_all('/<si\b.*?<\/si>/s', $xml, $matches);
        return array_map(static function (string $si): string {
            preg_match_all('/<t\b[^>]*>(.*?)<\/t>/s', $si, $texts);
            return html_entity_decode(implode('', $texts[1] ?? []), ENT_QUOTES | ENT_XML1, 'UTF-8');
        }, $matches[0] ?? []);
    }

    private function sheetRows(string $xml, array $strings): array
    {
        if ($xml === '') return [];
        preg_match_all('/<row\b([^>]*)>(.*?)<\/row>/s', $xml, $rowMatches, PREG_SET_ORDER);
        $rawRows = [];
        foreach ($rowMatches as $rowMatch) $rawRows[] = $this->cells($rowMatch[1] ?? '', $rowMatch[2] ?? '', $strings);
        $header = $this->header($rawRows);
        if ($header === []) return [];
        $rows = [];
        foreach ($rawRows as $row) {
            if (($row['_index'] ?? 0) <= ($header['_index'] ?? 0)) continue;
            $mapped = [];
            foreach ($header['columns'] as $column => $label) $mapped[$label] = trim((string) ($row[$column] ?? ''));
            if (implode('', $mapped) !== '') $rows[] = $mapped;
        }
        return $rows;
    }

    private function cells(string $rowAttrs, string $rowXml, array $strings): array
    {
        preg_match('/\br="(\d+)"/', $rowAttrs, $rowIndex);
        $row = ['_index' => (int) ($rowIndex[1] ?? 0)];
        preg_match_all('/<c\b([^>]*)>(.*?)<\/c>/s', $rowXml, $cells, PREG_SET_ORDER);
        foreach ($cells as $cell) {
            preg_match('/\br="([A-Z]+)/', $cell[1], $column);
            $key = $column[1] ?? '';
            if ($key === '') continue;
            preg_match('/<v>(.*?)<\/v>/s', $cell[2], $value);
            $raw = html_entity_decode($value[1] ?? '', ENT_QUOTES | ENT_XML1, 'UTF-8');
            $row[$key] = str_contains($cell[1], 't="s"') ? (string) ($strings[(int) $raw] ?? '') : $raw;
        }
        return $row;
    }

    private function header(array $rows): array
    {
        foreach (array_slice($rows, 0, 20) as $row) {
            $columns = [];
            foreach ($row as $column => $value) {
                if ($column === '_index') continue;
                $label = $this->normalize((string) $value);
                if ($label !== '') $columns[$column] = $label;
            }
            if (in_array('sector', $columns, true) || in_array('localidad', $columns, true)) {
                return ['_index' => (int) ($row['_index'] ?? 0), 'columns' => $columns];
            }
        }
        return [];
    }

    private function normalize(string $value): string
    {
        $plain = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $plain = strtolower((string) preg_replace('/\s+/', ' ', trim($plain)));
        return match ($plain) {
            'nombre institucional' => 'nombre',
            'sector' => 'sector',
            'codigo localidad', 'cod localidad' => 'codigo localidad',
            'localidad' => 'localidad',
            'direccion sede', 'direccion' => 'direccion',
            default => $plain,
        };
    }
}
