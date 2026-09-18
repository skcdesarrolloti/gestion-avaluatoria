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

    private function norm(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        return ltrim($digits, '0') ?: ($digits === '' ? '' : '0');
    }
}
