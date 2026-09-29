<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalNarrativeMidasSupport
{
    public function forChapter(string $chapter, array $documents): array
    {
        return match ($chapter) {
            '6' => $this->economic($documents),
            '7' => $this->restrictive($documents),
            default => ['title' => '', 'intro' => '', 'items' => [], 'findings' => []],
        };
    }

    private function economic(array $documents): array
    {
        $education = $this->group($documents, 'Educación');
        $findings = [];
        if ($education !== []) {
            $findings[] = 'Educación MIDAS debe revisarse como soporte de equipamientos institucionales: colegios oficiales y privados pueden justificar actividad institucional o educativa secundaria/complementaria.';
            if ($summary = $this->educationSummary($education)) $findings[] = $summary;
        }
        return [
            'title' => 'Soportes MIDAS para actividad económica',
            'intro' => 'Estos archivos no reemplazan la visita ni el criterio del analista; ayudan a sustentar actividades secundarias, complementarias y equipamientos del sector.',
            'items' => $this->items($education),
            'findings' => $findings,
        ];
    }

    private function restrictive(array $documents): array
    {
        $climate = $this->group($documents, 'Cambio climático');
        $findings = $climate === [] ? [] : [
            'Cambio climático MIDAS debe revisarse en el numeral 7 para amenazas, vulnerabilidad, adaptación, inundación, salvedades ambientales o ausencia soportada de incidencia.',
            'Si el soporte muestra una amenaza o condición ambiental relevante, ajusta los textos base de estabilidad, ambiente y restricciones antes de emitir el entregable.',
        ];
        return [
            'title' => 'Soportes MIDAS para condiciones restrictivas',
            'intro' => 'Usa estos documentos para confirmar si las salvedades del capítulo 7 deben mantenerse, ampliarse o cambiarse por una condición soportada.',
            'items' => $this->items($climate),
            'findings' => $findings,
        ];
    }

    private function group(array $documents, string $group): array
    {
        return array_values(array_filter($documents, static fn (array $doc): bool =>
            (string) ($doc['layer_group'] ?? '') === $group));
    }

    private function items(array $documents): array
    {
        return array_map(static fn (array $doc): array => [
            'id' => (string) ($doc['id'] ?? ''),
            'group' => (string) ($doc['layer_group'] ?? ''),
            'title' => (string) ($doc['title'] ?? 'Documento MIDAS'),
            'filename' => (string) ($doc['source_filename'] ?? ''),
            'use' => (string) ($doc['practical_use'] ?? ''),
            'applies_to' => (string) ($doc['applies_to'] ?? ''),
        ], $documents);
    }

    private function educationSummary(array $documents): string
    {
        $sectorCounts = []; $localityCounts = []; $total = 0;
        foreach ($documents as $document) {
            $rows = $this->readXlsxRows((string) ($document['file_path'] ?? ''));
            foreach ($rows as $row) {
                $sector = $this->first($row, ['sector', 'tipo sector', 'oficial privado']);
                $locality = $this->first($row, ['localidad', 'nombre localidad']);
                if ($sector !== '') $sectorCounts[$sector] = ($sectorCounts[$sector] ?? 0) + 1;
                if ($locality !== '') $localityCounts[$locality] = ($localityCounts[$locality] ?? 0) + 1;
                if ($sector !== '' || $locality !== '') $total++;
            }
        }
        if ($total === 0) return '';
        arsort($sectorCounts); arsort($localityCounts);
        $sectors = $this->countsText($sectorCounts);
        $localities = $this->countsText(array_slice($localityCounts, 0, 3, true));
        return trim('Lectura preliminar del Excel de Educación: ' . $total . ' registro(s)'
            . ($sectors !== '' ? '; sector: ' . $sectors : '')
            . ($localities !== '' ? '; localidades principales: ' . $localities : '') . '.');
    }

    private function readXlsxRows(string $path): array
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
            foreach ($header['columns'] as $column => $label) {
                $mapped[$label] = trim((string) ($row[$column] ?? ''));
            }
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

    private function first(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (($row[$key] ?? '') !== '') return $this->label((string) $row[$key]);
        }
        return '';
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

    private function label(string $value): string
    {
        $value = mb_strtolower(trim($value));
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    private function countsText(array $counts): string
    {
        $parts = [];
        foreach ($counts as $label => $count) $parts[] = $label . ' ' . $count;
        return implode(', ', $parts);
    }
}
