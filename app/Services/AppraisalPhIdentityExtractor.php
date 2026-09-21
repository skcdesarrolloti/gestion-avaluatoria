<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhIdentityExtractor
{
    public function extract(string $text, array $context): array
    {
        $core = [];
        $text = preg_replace('/\[(?:Documento: [^\]]+|Cobertura: [^\]]+|Página \d+)\]/u', '', $text) ?? $text;
        $joined = preg_replace('/\s+/u', ' ', $text) ?? $text;
        preg_match_all('/\b(?:EDIFICIO|CONJUNTO|COPROPIEDAD|CENTRO COMERCIAL)\s+["“]?([A-ZÁÉÍÓÚÑ0-9][A-ZÁÉÍÓÚÑ0-9 -]{3,90})/u', $joined, $matches);
        $names = [];
        foreach ($matches[0] as $name) {
            $name = trim(preg_split('/\s+(?:PROPIEDAD HORIZONTAL|P\.?H\.?|ARTICULO|ARTÍCULO|EL|LA|DE LA|NIT)\b/u', $name)[0], ' .-');
            if (mb_strlen($name) > 8) $names[] = $name;
        }
        if ($names) {
            $counts = array_count_values($names); arsort($counts);
            $core['ph_name'] = (string) array_key_first($counts);
            $core['ph_key'] = $core['ph_name'];
        }
        $core['matrix_registration'] = $this->unique('/matr[ií]cula\s+(?:inmobiliaria\s+)?(?:matriz|base)\s*[:nNoO.º°-]*\s*(\d{2,4}-\d{3,9})/iu', $text);
        // A regulation contains many units. Never adopt its first unit or percentage.
        $unit = trim((string) ($context['private_unit'] ?? ''));
        if ($unit !== '') {
            $pattern = '/(?<![\pL\d])' . preg_quote($unit, '/') . '(?![\pL\d])[^\r\n]{0,100}?coeficiente\s*(?:de copropiedad)?\s*[:=]?\s*(\d+(?:[.,]\d+)?\s*%)/iu';
            $core['coefficient'] = $this->unique($pattern, $text);
        }
        foreach ([
            'administration_name' => '(?:raz[oó]n social de la administraci[oó]n|empresa administradora)',
            'administration_contact' => 'contacto de administraci[oó]n',
            'administration_phone' => 'tel[eé]fono (?:de la |de )?administraci[oó]n',
            'administration_email' => 'correo (?:de la |de )?administraci[oó]n',
        ] as $field => $label) {
            $core[$field] = $this->unique('/(?:^|\n)\s*' . $label . '\s*:\s*([^\r\n]{3,120})/iu', $text);
        }
        return array_filter($core, static fn ($v) => $v !== '');
    }

    private function unique(string $pattern, string $text): string
    {
        preg_match_all($pattern, $text, $matches);
        $values = array_values(array_unique(array_map('trim', $matches[1] ?? [])));
        return count($values) === 1 ? $values[0] : '';
    }
}
