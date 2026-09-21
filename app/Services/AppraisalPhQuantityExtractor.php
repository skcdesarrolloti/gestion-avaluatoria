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
        foreach ([
            'numero_oficinas' => ['oficinas?', 'oficinas'],
            'numero_locales' => ['locales?', 'locales'],
            'numero_parqueaderos' => ['parqueaderos?|garajes|estacionamientos', 'parqueaderos'],
            'numero_depositos' => ['depositos|cuartos utiles', 'depósitos'],
            'numero_pisos' => ['pisos?|niveles', 'pisos'],
            'numero_sotanos' => ['sotanos?', 'sótanos'],
            'numero_ascensores' => ['ascensores?', 'ascensores'],
        ] as $key => [$terms, $label]) {
            $value = $this->near($plain, $terms, $label);
            if ($value !== '') $out[$key] = $value;
        }
        if (!isset($out['numero_sotanos']) && preg_match('/\bsotano\b/u', $plain)) $out['numero_sotanos'] = 'Sótano mencionado';
        return $out;
    }
    private function near(string $text, string $terms, string $label): string
    {
        $number = '(\d{1,4}|un|uno|una|dos|tres|cuatro|cinco|seis|siete|ocho|nueve|diez|once|doce|trece|catorce|quince|dieciseis|diecisiete|dieciocho|diecinueve|veinte|treinta|cuarenta|cincuenta|sesenta|setenta|ochenta|noventa|cien|ciento)';
        if (preg_match('/\b' . $number . '\s+(?:unidades\s+)?(?:' . $terms . ')\b/u', $text, $m)) return $this->format($m[1], $label);
        if (preg_match('/\b(?:' . $terms . ')\s*[:\-]\s*' . $number . '\b/u', $text, $m)) return $this->format($m[1], $label);
        return '';
    }
    private function format(string $raw, string $label): string
    {
        $raw = trim($raw);
        $num = ctype_digit($raw) ? (int) $raw : (self::WORDS[$raw] ?? null);
        return $num !== null ? $num . ' ' . $label : $raw . ' ' . $label;
    }
}
