<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateCancellationMatcher
{
    public function apply(array $rows): array
    {
        $index = [];
        foreach ($rows as $key => $row) {
            $order = $this->norm((string) ($row['orden'] ?? ''));
            if ($order !== '') $index[$order] = $key;
        }
        foreach ($rows as $key => $row) {
            $refs = $this->references($row);
            if (!$refs) $refs = $this->refsBySharedId($rows, $key, $row);
            if (!$refs) $refs = $this->refsBySharedParties($rows, $key, $row);
            if (!$refs) continue;
            $closed = [];
            foreach ($refs as $ref) {
                $targetKey = $index[$this->norm($ref)] ?? null;
                if ($targetKey === null) continue;
                $targetOrder = (string) ($rows[$targetKey]['orden'] ?? $ref);
                $closingOrder = (string) ($rows[$key]['orden'] ?? '');
                $rows[$targetKey]['estado_juridico'] = 'solucionada';
                $rows[$targetKey]['requiere_revision'] = 'No';
                $rows[$targetKey]['cancelada_por'] = $closingOrder;
                $rows[$targetKey]['impacto_resumen'] = 'Afectación cerrada: la anotación '
                    . $targetOrder . ' se cancela con la anotación ' . $closingOrder . ' y queda cerrada.';
                $closed[] = $targetOrder;
            }
            if ($closed) {
                $rows[$key]['estado_juridico'] = 'solucionada';
                $rows[$key]['requiere_revision'] = 'No';
                $rows[$key]['cancelacion_de'] = implode(', ', array_values(array_unique($closed)));
                $rows[$key]['impacto_resumen'] = 'Cierre registral: esta anotación cancela la anotación '
                    . $rows[$key]['cancelacion_de'] . ' y deja cerrada esa afectación.';
            }
        }
        return $rows;
    }

    private function references(array $row): array
    {
        $refs = array_filter(array_map('strval', (array) ($row['cancela_anotaciones'] ?? [])));
        return array_values(array_unique(array_merge($refs, $this->fromText((string) ($row['texto'] ?? '')))));
    }

    private function fromText(string $text): array
    {
        $patterns = [
            '/(?:se\s+)?cancela(?:ci(?:o|ó|\?|Ã³)n)?(?:\s+(?:total|parcial))?(?:\s+(?:de|a|la|las|el|los))?\s+anotaci(?:o|ó|\?|Ã³)n(?:es)?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*:?\s*([0-9][0-9,\s\-y]*)/iu',
            '/(?:levantamiento|desembargo|liberaci(?:o|ó|\?|Ã³)n)[^.]{0,160}?anotaci(?:o|ó|\?|Ã³)n(?:es)?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*:?\s*([0-9][0-9,\s\-y]*)/iu',
        ];
        $refs = [];
        foreach ($patterns as $pattern) {
            if (!preg_match_all($pattern, $text, $matches)) continue;
            foreach ($matches[1] as $chunk) {
                if (preg_match_all('/\d+/', (string) $chunk, $nums)) {
                    foreach ($nums[0] as $num) $refs[] = ltrim($num, '0') ?: '0';
                }
            }
        }
        return array_values(array_unique($refs));
    }

    private function refsBySharedId(array $rows, int|string $key, array $row): array
    {
        $text = (string) ($row['texto'] ?? '');
        if (!preg_match('/cancelaci|cancela|levantamiento|desembargo|liberaci(?:o|ó|\?|Ã³)n/iu', $text)) return [];
        $ids = $this->identifiers($text);
        if (!$ids) return [];
        $refs = [];
        foreach ($rows as $targetKey => $target) {
            if ($targetKey === $key || (int) $targetKey >= (int) $key) continue;
            if (($target['estado_juridico'] ?? '') === 'solucionada') continue;
            if (!in_array(($target['categoria'] ?? ''), ['gravamen', 'limitacion_dominio', 'medida_cautelar'], true)) continue;
            if (array_intersect($ids, $this->identifiers((string) ($target['texto'] ?? '')))) {
                $refs[] = (string) ($target['orden'] ?? '');
            }
        }
        return array_values(array_filter(array_unique($refs)));
    }

    private function refsBySharedParties(array $rows, int|string $key, array $row): array
    {
        $text = (string) ($row['texto'] ?? '');
        if (!preg_match('/cancelaci|cancela|levantamiento|desembargo|liberaci(?:o|ó|\?|Ã³)n/iu', $text)) return [];
        $closingTokens = $this->partyTokens($row);
        if (count($closingTokens) < 2) return [];
        $matches = [];
        foreach ($rows as $targetKey => $target) {
            if ($targetKey === $key || (int) $targetKey >= (int) $key) continue;
            if (($target['estado_juridico'] ?? '') === 'solucionada') continue;
            if (($target['categoria'] ?? '') !== 'medida_cautelar') continue;
            $score = count(array_intersect($closingTokens, $this->partyTokens($target)));
            if ($score >= 3) $matches[] = ['score' => $score, 'order' => (string) ($target['orden'] ?? '')];
        }
        if (!$matches) return [];
        $best = max(array_column($matches, 'score'));
        return array_values(array_filter(array_unique(array_column(array_filter($matches,
            static fn (array $match): bool => $match['score'] === $best), 'order'))));
    }

    private function partyTokens(array $row): array
    {
        $text = implode(' ', [(string) ($row['personaDe'] ?? ''), (string) ($row['personaA'] ?? ''),
            (string) ($row['texto'] ?? '')]);
        $text = preg_replace('/\b(?:cc|nit|n\.?i\.?t|cedula|c[eé]dula)\b[^A-ZÁÉÍÓÚÑ]*/iu', ' ', $text) ?? $text;
        preg_match_all('/[A-ZÁÉÍÓÚÑ]{4,}/iu', mb_strtoupper($text), $matches);
        $stop = ['BANCO', 'BANCOLOMBIA', 'CONDOMINIO', 'DISTRITAL', 'JUZGADO', 'OFICIO', 'CARTAGENA',
            'INDIAS', 'NIT', 'SAS', 'SA', 'MARIA', 'ELENA', 'MEDIDA', 'CAUTELAR', 'CANCELACION',
            'PROCESO', 'RADICADO', 'EMBARGO', 'DEMANDA', 'OFICINA', 'APOYO', 'JUDICIAL', 'CIVIL',
            'CIRCUITO', 'OCTAVO', 'CUARTO', 'ORALIDAD', 'EJECUTIVO', 'ACCION', 'REAL', 'ORDEN',
            'PROVIDENCIA'];
        return array_values(array_diff(array_unique($matches[0] ?? []), $stop));
    }

    private function identifiers(string $text): array
    {
        if (!preg_match_all('/\b\d[\d\s\-.]{10,}\d\b/', $text, $matches)) return [];
        $ids = [];
        foreach ($matches[0] as $raw) {
            $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';
            if (strlen($digits) >= 12 && !preg_match('/^0+$/', $digits)) $ids[] = $digits;
        }
        return array_values(array_unique($ids));
    }

    private function norm(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        return ltrim($digits, '0') ?: ($digits === '' ? '' : '0');
    }
}
