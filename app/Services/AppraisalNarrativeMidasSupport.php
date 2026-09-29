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

    public function reportSections(string $chapter, array $documents): array
    {
        $support = $this->forChapter($chapter, $documents);
        if (($support['items'] ?? []) === []) return [];
        $findings = array_filter(array_map('strval', $support['findings'] ?? []));
        $titles = array_map(static fn (array $item): string => (string) ($item['title'] ?? ''), $support['items']);
        $titles = array_values(array_filter($titles));
        $text = trim(implode(' ', $findings));
        if ($titles !== []) $text .= "\n\nDocumentos MIDAS considerados: " . implode('; ', array_slice($titles, 0, 8)) . '.';
        return [[(string) ($support['title'] ?? 'Soporte MIDAS'), trim($text)]];
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
            'Cambio climático MIDAS no concluye por sí solo una afectación del predio; se usa como soporte documental para revisar amenazas, vulnerabilidad, adaptación, inundación y salvedades ambientales.',
            'El soporte alimenta 7.1 cuando exista relación con estabilidad, suelos, inundación o deslizamiento; alimenta 7.2 para ambiente, salubridad o vulnerabilidad climática; y alimenta 7.6 cuando deba dejarse una hipótesis o salvedad especial.',
            $this->climateSummary($climate),
        ];
        return [
            'title' => 'Soportes MIDAS para condiciones restrictivas',
            'intro' => 'Usa estos documentos para confirmar si las salvedades del capítulo 7 deben mantenerse, ampliarse o cambiarse por una condición soportada.',
            'items' => $this->items($climate),
            'findings' => $findings,
        ];
    }

    private function climateSummary(array $documents): string
    {
        $labels = [];
        foreach ($documents as $document) {
            $label = $this->climateLabel((string) (($document['title'] ?? '') . ' ' . ($document['source_filename'] ?? '')));
            if ($label !== '') $labels[$label] = true;
        }
        if ($labels === []) return 'Soportes de cambio climático cargados: revisa cada PDF antes de modificar las restricciones.';
        return 'Soportes de cambio climático cargados: ' . implode(', ', array_keys($labels))
            . '. Úsalos como respaldo de redacción, no como dictamen automático de riesgo predial.';
    }

    private function climateLabel(string $value): string
    {
        $plain = $this->normalize($value);
        return match (true) {
            str_contains($plain, 'invemar') => 'INVEMAR',
            str_contains($plain, 'plan adaptacion') || str_contains($plain, '4c') => 'Plan de adaptación 4C',
            str_contains($plain, 'lineamientos') => 'lineamientos de adaptación',
            str_contains($plain, 'integracion') => 'integración del cambio climático',
            default => trim((string) preg_replace('/\s+/', ' ', $value)),
        };
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
        $reader = new AppraisalEducationMidasReader();
        foreach ($documents as $document) {
            $rows = $reader->rows((string) ($document['file_path'] ?? ''));
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
