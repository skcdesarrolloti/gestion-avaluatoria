<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateFieldExtractor
{
    private const END_LABELS = ['departamento', 'municipio', 'vereda', 'circulo registral', 'círculo registral',
        'matricula inmobiliaria', 'matrícula inmobiliaria', 'estado del folio', 'fecha de apertura',
        'fecha de expedicion', 'fecha de expedición', 'turno', 'pin', 'referencia catastral',
        'cedula catastral', 'cédula catastral', 'codigo catastral', 'código catastral', 'nupre',
        'direccion', 'dirección', 'tipo de predio', 'cabida y linderos', 'anotacion', 'anotación'];

    public static function extract(string $text, string $filename): array
    {
        $self = new self();
        $flat = $self->flat($text);
        $compact = $self->compact($text);
        $matriculas = $self->matriculas($text);
        $actualCadastral = $self->cadastral($flat, false) ?: $self->match($compact, [
            '/(?:referenciacatastral|c[eé]dulacatastral|c[oó]digocatastral)(?:actual)?[:#]?([0-9A-Za-z\.\-]{8,80}?)(?=(?:referenciacatastralanterior|c[oó]digocatastralanterior|nupre|direcci|estado|fecha|$))/iu',
        ]);
        $previousCadastral = $self->cadastral($flat, true) ?: $self->match($compact, [
            '/(?:referenciacatastralanterior|c[oó]digocatastralanterior)[:#]?([0-9A-Za-z\.\-]{8,80}?)(?=(?:nupre|direcci|estado|fecha|$))/iu',
        ]);
        $data = [
            'archivo_origen' => $filename,
            'matricula_inmobiliaria' => $self->code($self->between($flat, ['nro matricula', 'nro matrícula',
                'matricula inmobiliaria', 'matrícula inmobiliaria']) ?: $self->match($text, [
                '/(?:nro|no|n[uú]mero|numero)\s+matr(?:i|í)cula\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/matr(?:i|í)cula\s+inmobiliaria(?:\s*(?:no\.?|nro\.?))?\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/\b([0-9]{2,4}\s*-\s*[0-9]{3,})\b/u',
            ])),
            'circulo_registral' => $self->clean($self->between($flat, ['circulo registral', 'círculo registral'])
                ?: $self->match($text, ['/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu'])),
            'orip' => $self->clean($self->match($text, ['/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu'])),
            'municipio' => $self->clean($self->between($flat, ['municipio'])),
            'departamento' => $self->clean($self->between($flat, ['departamento'])),
            'vereda' => $self->clean($self->between($flat, ['vereda'])),
            'estado_folio' => $self->clean($self->between($flat, ['estado del folio'])
                ?: $self->match($text, ['/folio\s+(abierto|cerrado|cancelado|activo)\b/iu'])),
            'fecha_apertura' => $self->clean($self->between($flat, ['fecha de apertura'])),
            'fecha_expedicion' => $self->clean($self->between($flat, ['fecha de expedicion', 'fecha de expedición',
                'fecha de impresion', 'fecha de impresión']) ?: $self->match($text, ['/impreso\s+el\s+([0-9\/\-\s:amp\.]{8,40})/iu'])),
            'turno' => $self->clean($self->between($flat, ['turno'])),
            'pin' => $self->clean($self->between($flat, ['pin']) ?: $self->match($text, [
                '/certificado\s+generado\s+con\s+el\s+pin\s+(?:no\.?)?\s*([A-Za-z0-9\-]{4,40})/iu',
            ])),
            'codigo_catastral_actual' => $self->code($actualCadastral),
            'codigo_catastral_anterior' => $self->code($previousCadastral),
            'nupre' => $self->code($self->between($flat, ['nupre'])),
            'observacion_catastral' => $self->clean($self->between($flat, ['observacion catastral',
                'observación catastral', 'observaciones catastrales'])),
            'direccion' => $self->address($self->between($flat, ['direccion actual del inmueble',
                'dirección actual del inmueble', 'direccion del inmueble', 'dirección del inmueble',
                'direccion', 'dirección']) ?: $self->match($text, ['/ubicaci(?:o|ó)n\s+del\s+predio\s*[:#]?\s*([^\n\r]{6,220})/iu'])),
            'tipo_predio' => $self->clean($self->between($flat, ['tipo de predio', 'destinacion economica',
                'destinación económica'])),
            'area' => $self->area($text, '/[áa]rea\s+(?:de\s+)?([0-9\.,]{1,30})\s*(?:m2|mts2|metros?\s*cuadrados?)/iu'),
            'area_privada' => $self->area($text, '/[áa]rea\s+privada\b[^0-9]{0,50}([0-9\.,]{1,30})/iu'),
            'area_construida' => $self->area($text, '/[áa]rea\s+construida\b[^0-9]{0,50}([0-9\.,]{1,30})/iu'),
            'coeficiente' => $self->clean($self->between($flat, ['coeficiente'])),
            'cabida_linderos' => $self->block($text, ['cabida y linderos', 'cabida/linderos', 'linderos'], 1600),
            'reglamento_ph' => $self->contains($text, ['propiedad horizontal', 'reglamento de propiedad horizontal', 'ley 675']) ? 'Sí' : '',
            'matricula_matriz' => $self->code($self->match($text, ['/matr(?:i|í)cula\s+matriz\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu'])),
            'matriculas_derivadas' => implode('; ', array_slice($matriculas, 0, 12)),
            'titular_actual' => $self->clean($self->titular($text)),
            'documento_soporte_actual' => $self->clean($self->match($text, ['/escritura\s+p[uú]blica\s*(?:no\.?|n[oº])?\s*([A-Za-z0-9\-\/\.]{2,80})/iu'])),
            'valor_ultimo_acto' => $self->clean($self->match($text, ['/valor\s+(?:acto|negocio|compraventa)\s*[:#]?\s*\$?\s*([0-9\.\,]{4,40})/iu'])),
        ];
        if ($data['orip'] === '') $data['orip'] = $data['circulo_registral'];
        return $data;
    }

    private function between(string $flat, array $labels): string
    {
        foreach ($labels as $label) {
            $pattern = '/\b' . preg_quote($label, '/') . '\b\s*[:#-]?\s*(.+?)(?=\s+(?:'
                . implode('|', array_map(static fn (string $end): string => preg_quote($end, '/'), self::END_LABELS))
                . ')\b|$)/iu';
            if (preg_match($pattern, $flat, $m)) return $this->clean((string) $m[1]);
        }
        return '';
    }

    private function cadastral(string $flat, bool $previous): string
    {
        $labels = $previous ? ['referencia catastral anterior', 'codigo catastral anterior', 'código catastral anterior']
            : ['referencia catastral actual', 'referencia catastral', 'cedula catastral', 'cédula catastral',
                'codigo catastral actual', 'código catastral actual', 'codigo catastral', 'código catastral'];
        return $this->match($this->between($flat, $labels), ['/([0-9A-Za-z\.\- ]{8,80})/u']);
    }

    private function titular(string $text): string
    {
        return $this->match($text, [
            '/titular(?:es)?\s+del\s+derecho\s+real\s+de\s+dominio\s*[:#]?\s*([^\n\r]{4,220})/iu',
            '/personas\s+que\s+intervienen\s+en\s+el\s+acto.*?de:\s*([^\n\r]{4,220})/isu',
            '/propietario(?:\(s\))?\s*[:#]?\s*([^\n\r]{4,220})/iu',
        ]);
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }

    private function clean(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        $value = preg_replace('/^(no\.?|nro\.?|numero|n[uú]mero)\s*/iu', '', trim($value)) ?? trim($value);
        return mb_substr(trim($value, " \t\n\r\0\x0B:-"), 0, 600);
    }

    private function address(string $value): string
    {
        $value = preg_replace('/\b(matricula|matr[ií]cula|referencia|c[eé]dula|estado|fecha)\b.*$/iu', '', $value) ?? $value;
        return $this->clean($value);
    }

    private function code(string $value): string { return preg_replace('/\s+/', '', $this->clean($value)) ?? ''; }
    private function flat(string $text): string { return preg_replace('/\s+/u', ' ', $text) ?? $text; }
    private function compact(string $text): string { return preg_replace('/\s+/u', '', $text) ?? $text; }
    private function area(string $text, string $pattern): string { return preg_match($pattern, $text, $m) ? str_replace(',', '.', (string) $m[1]) : ''; }
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
    private function matriculas(string $text): array { preg_match_all('/\b[0-9]{2,4}\s*-\s*[0-9]{3,}\b/u', $text, $m); return array_values(array_unique(array_map(fn ($v) => $this->code((string) $v), $m[0] ?? []))); }
    private function block(string $text, array $labels, int $limit): string { foreach ($labels as $label) if (($pos = mb_stripos($text, $label)) !== false) return $this->clean(mb_substr($text, (int) $pos, $limit)); return ''; }
}
