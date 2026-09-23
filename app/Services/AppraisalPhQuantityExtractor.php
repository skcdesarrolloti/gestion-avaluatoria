<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhQuantityExtractor
{
    private const WORDS = ['un'=>1,'uno'=>1,'una'=>1,'dos'=>2,'tres'=>3,'cuatro'=>4,'cinco'=>5,'seis'=>6,'siete'=>7,
        'ocho'=>8,'nueve'=>9,'diez'=>10,'once'=>11,'doce'=>12,'trece'=>13,'catorce'=>14,'quince'=>15,'dieciseis'=>16,
        'diecisiete'=>17,'dieciocho'=>18,'diecinueve'=>19,'veinte'=>20,'treinta'=>30,'cuarenta'=>40,'cincuenta'=>50,
        'sesenta'=>60,'setenta'=>70,'ochenta'=>80,'noventa'=>90,'cien'=>100,'ciento'=>100];
    public function extract(string $text): array
    {
        $plain = AppraisalPhEvidence::fold(preg_replace('/\s+/u', ' ', $text) ?? '');
        $out = [];
        if (preg_match('/\bpiso\s+(\d{1,2})\s*(?:al|a|-)\s*(\d{1,2})\b/u', $plain, $m)) {
            $out['numero_pisos'] = max((int) $m[1], (int) $m[2]) . ' pisos';
        }
        foreach ([
            'numero_oficinas' => ['oficinas?', 'oficinas'],
            'numero_locales' => ['locales?', 'locales'],
            'numero_parqueaderos' => ['parqueaderos?|garajes|estacionamientos', 'parqueaderos'],
            'numero_depositos' => ['depositos|cuartos utiles', 'depósitos'],
            'numero_pisos' => ['pisos?|niveles', 'pisos'],
            'numero_sotanos' => ['sotanos?|semisotanos?', 'sótanos'],
            'numero_ascensores' => ['ascensores?', 'ascensores'],
        ] as $key => [$terms, $label]) {
            if (isset($out[$key])) continue;
            $value = $this->near($plain, $terms, $label, (string) $key);
            if ($value !== '') $out[$key] = $value;
        }
        if (!isset($out['numero_sotanos']) && preg_match('/\bsotano\b/u', $plain)) $out['numero_sotanos'] = 'Sótano mencionado';
        return $out;
    }
    private function near(string $text, string $terms, string $label, string $key): string
    {
        $number = '(\d{1,4}|un|uno|una|dos|tres|cuatro|cinco|seis|siete|ocho|nueve|diez|once|doce|trece|catorce|quince|dieciseis|diecisiete|dieciocho|diecinueve|veinte|treinta|cuarenta|cincuenta|sesenta|setenta|ochenta|noventa|cien|ciento)';
        $found = [];
        foreach (['/\((\d{1,4})\)\s+(?:' . $terms . ')\b/u', '/\b(?:' . $terms . ')\s*[:\-]\s*(\d{1,4})\b/u'] as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) foreach ($matches[1] as $match) {
                if ($this->badContext($text, (int) $match[1])) continue;
                $found[(string) (int) $match[0]] = $this->format((string) $match[0], $label);
            }
        }
        if (!$found && !in_array($key, ['numero_oficinas', 'numero_locales', 'numero_depositos'], true)
            && preg_match_all('/\b' . $number . '(?![\.,]\d)\s+(?:unidades\s+)?(?:' . $terms . ')\b/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $match) {
                if ($this->badContext($text, (int) $match[1])) continue;
                $raw = (string) $match[0];
                $found[(string) $this->numeric($raw)] = $this->format($raw, $label);
            }
        }
        if (!$found) return '';
        $values = array_values($found);
        return count($values) === 1 ? $values[0] : implode('; ', $values) . ' (verificar vigencia)';
    }
    private function numeric(string $raw): int|string
    {
        $raw = trim($raw);
        return ctype_digit($raw) ? (int) $raw : (self::WORDS[$raw] ?? $raw);
    }
    private function format(string $raw, string $label): string
    {
        $num = $this->numeric($raw);
        return is_int($num) ? $num . ' ' . $label : trim($raw) . ' ' . $label;
    }
    private function badContext(string $text, int $offset): bool
    {
        $before = mb_substr($text, max(0, $offset - 70), 70);
        if (preg_match('/total de|comprende|incluye|cuenta con|numero de|número de/u', $before) === 1) return false;
        return preg_match('/articulo|capitulo|paragrafo|notaria|escritura|matricula|cedula|metro[s]? cuadrado|m2|m 2/u', $before) === 1;
    }
}
