<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhAgeExtractor
{
    private const MONTHS = ['enero'=>'01','febrero'=>'02','marzo'=>'03','abril'=>'04','mayo'=>'05','junio'=>'06',
        'julio'=>'07','agosto'=>'08','septiembre'=>'09','setiembre'=>'09','octubre'=>'10','noviembre'=>'11','diciembre'=>'12'];

    public function extract(string $text, ?int $asOfYear = null): array
    {
        $candidate = $this->candidate($text);
        if ($candidate === null) return [];
        $year = (int) $candidate['year'];
        $asOfYear ??= (int) date('Y');
        if ($year < 1900 || $year > $asOfYear) return [];
        $age = max(0, $asOfYear - $year);
        return [
            'fecha_reglamento_ph' => $candidate['date'] !== '' ? $candidate['date'] : 'Año ' . $year,
            'edad_aproximada_ph' => $age . ' años aprox. (base ' . $year . '; corte ' . $asOfYear . ')',
        ];
    }

    private function candidate(string $text): ?array
    {
        $plain = preg_replace('/\s+/u', ' ', AppraisalPhEvidence::fold($text)) ?? '';
        $patterns = [
            '/anotaci[oó?]n.{0,80}?(?:de fecha|fecha)\s*(' . $this->datePattern() . ').{0,220}?(?:propiedad horizontal|reglamento|acto constitutivo)/iu',
            '/(?:acto constitutivo|constituci[oó]n|reglamento)\s+(?:de\s+)?propiedad horizontal.{0,160}?(?:de fecha|fecha|del)\s*(' . $this->datePattern() . ')/iu',
            '/(?:escritura\s+p[uú]blica|escritura)\s*(?:no\.?|nro\.?|n[uú]mero)?\s*\d{1,6}.{0,90}?(?:de fecha|fecha|del)\s*(' . $this->datePattern() . ').{0,240}?(?:propiedad horizontal|reglamento)/iu',
            '/(?:propiedad horizontal|reglamento).{0,220}?(?:escritura\s+p[uú]blica|escritura).{0,90}?(?:de fecha|fecha|del)\s*(' . $this->datePattern() . ')/iu',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $plain, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as [$raw, $offset]) {
                    $date = $this->normalizeDate($raw);
                    if ($date !== null) return $date;
                }
            }
        }
        return null;
    }

    private function datePattern(): string
    {
        return '(?:\d{1,2}[\/-]\d{1,2}[\/-]\d{4}|\d{1,2}\s+de\s+(?:' . implode('|', array_keys(self::MONTHS)) . ')\s+de\s+\d{4}|\d{4})';
    }

    private function normalizeDate(string $raw): ?array
    {
        $raw = trim(AppraisalPhEvidence::fold($raw));
        if (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/u', $raw, $m)) {
            return ['date' => sprintf('%02d-%02d-%04d', (int) $m[1], (int) $m[2], (int) $m[3]), 'year' => $m[3]];
        }
        if (preg_match('/^(\d{1,2})\s+de\s+([a-z]+)\s+de\s+(\d{4})$/u', $raw, $m)) {
            $month = self::MONTHS[$m[2]] ?? null;
            return $month ? ['date' => sprintf('%02d-%s-%04d', (int) $m[1], $month, (int) $m[3]), 'year' => $m[3]] : null;
        }
        return preg_match('/^(\d{4})$/u', $raw, $m) ? ['date' => '', 'year' => $m[1]] : null;
    }

}

