<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateFieldExtractor
{
    private const END_LABELS = ['departamento', 'municipio', 'vereda', 'circulo registral', 'círculo registral',
        'depto', 'matricula inmobiliaria', 'matrícula inmobiliaria', 'matr?cula inmobiliaria', 'estado del folio',
        'pagina', 'página', 'p?gina', 'descripcion', 'descripción', 'fecha apertura', 'fecha de apertura',
        'radicacion', 'radicación', 'radicaci?n', 'impreso el',
        'fecha de expedicion', 'fecha de expedición', 'turno', 'pin', 'referencia catastral',
        'cedula catastral', 'cédula catastral', 'codigo catastral', 'código catastral', 'nupre',
        'direccion', 'dirección', 'tipo predio', 'tipo de predio', 'cabida y linderos', 'anotacion', 'anotación'];

    public static function extract(string $text, string $filename): array
    {
        $self = new self();
        $flat = $self->flat($text);
        $compact = $self->compact($text);
        $matriculas = $self->matriculas($text);
        $lines = $self->lines($text);
        $lastTradition = $self->lastTraditionBlock($text);
        $actualCadastral = $self->cadastral($flat, false) ?: $self->match($compact, [
            '/(?:referenciacatastral|c[eé]dulacatastral|c[oó]digocatastral)(?:actual)?[:#]?([0-9A-Za-z\.\-]{8,80}?)(?=(?:referenciacatastralanterior|c[oó]digocatastralanterior|nupre|direcci|estado|fecha|$))/iu',
        ]);
        $previousCadastral = $self->cadastral($flat, true) ?: $self->match($compact, [
            '/(?:referenciacatastralanterior|c[oó]digocatastralanterior)[:#]?([0-9A-Za-z\.\-]{8,80}?)(?=(?:nupre|direcci|estado|fecha|$))/iu',
        ]);
        $data = [
            'archivo_origen' => $filename,
            'matricula_inmobiliaria' => $self->code($self->match($text, [
                '/(?:nro|no|n[uú]mero|numero)\s+matr.{0,3}cula\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/matr.{0,3}cula\s+inmobiliaria(?:\s*(?:no\.?|nro\.?))?\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/\b([0-9]{2,4}\s*-\s*[0-9]{3,})\b/u',
            ]) ?: $self->between($flat, ['nro matricula', 'nro matrícula',
                'nro matr?cula', 'matricula inmobiliaria', 'matrícula inmobiliaria', 'matr?cula inmobiliaria',
                'numero de matricula', 'número de matrícula']) ?: $self->lineValue($lines, ['nro matricula',
                'nro matrícula', 'nro matr?cula', 'matricula inmobiliaria', 'matrícula inmobiliaria',
                'matr?cula inmobiliaria', 'numero de matricula', 'número de matrícula'])
            ),
            'circulo_registral' => $self->clean($self->between($flat, ['circulo registral', 'círculo registral'])
                ?: $self->lineValue($lines, ['circulo registral', 'círculo registral'])
                ?: $self->match($text, ['/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu'])),
            'orip' => $self->clean($self->match($text, ['/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu'])),
            'municipio' => $self->clean($self->between($flat, ['municipio']) ?: $self->lineValue($lines, ['municipio'])),
            'departamento' => $self->clean($self->between($flat, ['departamento', 'depto']) ?: $self->lineValue($lines, ['departamento', 'depto'])),
            'vereda' => $self->clean($self->between($flat, ['vereda']) ?: $self->lineValue($lines, ['vereda'])),
            'estado_folio' => $self->clean($self->match($text, ['/estado\s+del\s+folio\s*[:#]?\s*(activo|cerrado|cancelado|abierto)\b/iu',
                    '/folio\s+(abierto|cerrado|cancelado|activo)\b/iu'])
                ?: $self->between($flat, ['estado del folio'])
                ?: $self->lineValue($lines, ['estado del folio'])
            ),
            'fecha_apertura' => $self->clean($self->between($flat, ['fecha de apertura', 'fecha apertura'])
                ?: $self->lineValue($lines, ['fecha de apertura', 'fecha apertura'])),
            'fecha_expedicion' => $self->clean($self->between($flat, ['fecha de expedicion', 'fecha de expedición',
                'fecha de impresion', 'fecha de impresión']) ?: $self->lineValue($lines, ['fecha de expedicion',
                'fecha de expedición', 'fecha de impresion', 'fecha de impresión'])
                ?: $self->match($text, ['/impreso\s+el\s+([0-9\/\-\s:amp\.]{8,40})/iu'])),
            'turno' => $self->clean($self->between($flat, ['turno']) ?: $self->lineValue($lines, ['turno'])),
            'pin' => $self->clean($self->match($text, [
                '/certificado\s+generado\s+con\s+el\s+pin\s+(?:no\.?)?\s*([A-Za-z0-9\-]{4,40})/iu',
            ]) ?: $self->between($flat, ['pin']) ?: $self->lineValue($lines, ['pin'])),
            'codigo_catastral_actual' => $self->code($actualCadastral),
            'codigo_catastral_anterior' => $self->code($previousCadastral),
            'nupre' => $self->code($self->between($flat, ['nupre']) ?: $self->lineValue($lines, ['nupre'])),
            'observacion_catastral' => $self->clean($self->between($flat, ['observacion catastral',
                'observación catastral', 'observaciones catastrales'])),
            'direccion' => $self->address($self->between($flat, ['direccion actual del inmueble',
                'dirección actual del inmueble', 'direccion del inmueble', 'dirección del inmueble',
                'direccion', 'dirección']) ?: $self->lineValue($lines, ['direccion actual del inmueble',
                'dirección actual del inmueble', 'direccion del inmueble', 'dirección del inmueble',
                'direccion', 'dirección']) ?: $self->addressFromText($text)
                ?: $self->match($text, ['/ubicaci(?:o|ó)n\s+del\s+predio\s*[:#]?\s*([^\n\r]{6,220})/iu'])),
            'tipo_predio' => $self->clean($self->match($text, ['/tipo\s+predio\s*[:#]?\s*([^\n\r]{3,80})/iu'])
                ?: $self->between($flat, ['tipo predio', 'tipo de predio', 'destinacion economica',
                'destinación económica']) ?: $self->lineValue($lines, ['tipo predio', 'tipo de predio', 'destinacion economica',
                'destinación económica'])),
            'area' => $self->area($text, '/(?:[áa]rea|cabida)\s+(?:de\s+)?([0-9\.,]{1,30})\s*(?:m2|mts2|metros?\s*cuadrados?)/iu'),
            'area_privada' => $self->area($text, '/[áa]rea\s+privada\b[^0-9]{0,50}([0-9\.,]{1,30})/iu'),
            'area_construida' => $self->area($text, '/[áa]rea\s+construida\b[^0-9]{0,50}([0-9\.,]{1,30})/iu'),
            'coeficiente' => $self->clean($self->match($text, ['/coeficiente\s*(?:de\s+copropiedad)?\s*(?:[:#]|de)?\s*([0-9\.,]+%?)/iu'])
                ?: $self->between($flat, ['coeficiente']) ?: $self->lineValue($lines, ['coeficiente'])),
            'cabida_linderos' => $self->block($text, ['descripcion cabida y linderos', 'descripción cabida y linderos',
                'cabida y linderos', 'cabida/linderos', 'linderos'], 2200),
            'reglamento_ph' => $self->contains($text, ['propiedad horizontal', 'reglamento de propiedad horizontal', 'ley 675']) ? 'Sí' : '',
            'matricula_matriz' => $self->code($self->match($text, ['/matr.{0,3}cula\s+matriz\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/matr.{0,3}cula\s+abierta\s+con\s+base\s+en\s+la\s+([0-9]{2,4}\s*-\s*[0-9]{3,})/iu'])),
            'matriculas_derivadas' => implode('; ', array_slice($matriculas, 0, 12)),
            'unidad_privada' => $self->clean($self->match($text, ['/unidad\s+privada\s*[:#]?\s*([^\n\r]{3,160})/iu'])),
            'coeficiente_ph' => $self->clean($self->match($text, ['/coeficiente(?:\s+de\s+copropiedad)?\s*[:#]?\s*([0-9\.,%]{1,30})/iu'])),
            'titular_actual' => $self->clean($self->titular($text, $lastTradition)),
            'documento_soporte_actual' => $self->clean($self->documento($lastTradition ?: $text)),
            'valor_ultimo_acto' => $self->clean($self->valor($lastTradition ?: $text)),
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

    private function titular(string $text, string $lastTradition = ''): string
    {
        $source = $lastTradition !== '' ? $lastTradition : $text;
        $candidate = $this->match($text, [
            '/titular(?:es)?\s+del\s+derecho\s+real\s+de\s+dominio\s*[:#]?\s*([^\n\r]{4,220})/iu',
            '/propietario(?:\(s\))?\s*[:#]?\s*([^\n\r]{4,220})/iu',
        ]) ?: $this->match($source, [
            '/personas\s+que\s+intervienen\s+en\s+el\s+acto.*?\ba\s*:\s*([^\n\r]{4,220})/isu',
            '/\ba\s*:\s*([^\n\r]{4,220})/iu',
        ]) ?: $this->match($source, ['/comprador(?:\(es\))?\s*[:#]?\s*([^\n\r]{4,220})/iu']);
        return (new LegalCertificateTitleSanitizer())->valid($candidate);
    }

    private function documento(string $text): string
    {
        return $this->match($text, [
            '/doc\s*\.?\s*:\s*(.+?)(?=\s+valor\s+acto|\s+especificaci|\s+personas\s+que\s+intervienen|$)/isu',
            '/escritura\s+p[uú]blica\s*(?:no\.?|n[oº])?\s*([A-Za-z0-9\-\/\.]{2,80})/iu',
        ]);
    }

    private function valor(string $text): string
    {
        return $this->match($text, ['/valor\s+(?:acto|negocio|compraventa)\s*[:#]?\s*\$?\s*([0-9\.\,\s]{4,60})/iu']);
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
        $value = preg_replace('/\b(matricula|matr[ií]cula|matr.{0,3}cula|referencia|c[eé]dula|estado|fecha)\b.*$/iu', '', $value) ?? $value;
        return $this->clean($value);
    }

    private function code(string $value): string
    {
        $value = preg_replace('/\s+/', '', $this->clean($value)) ?? '';
        return preg_match('/^(sininformacion|sininformaci.n|noaplica|ninguno)$/iu', $value) ? '' : $value;
    }
    private function flat(string $text): string { return preg_replace('/\s+/u', ' ', $text) ?? $text; }
    private function compact(string $text): string { return preg_replace('/\s+/u', '', $text) ?? $text; }
    private function lines(string $text): array { return array_values(array_filter(array_map('trim', preg_split('/\R/u', $text) ?: []))); }
    private function area(string $text, string $pattern): string { return preg_match($pattern, $text, $m) ? str_replace(',', '.', (string) $m[1]) : ''; }
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
    private function matriculas(string $text): array { preg_match_all('/\b[0-9]{2,4}\s*-\s*[0-9]{3,}\b/u', $text, $m); return array_values(array_unique(array_map(fn ($v) => $this->code((string) $v), $m[0] ?? []))); }
    private function block(string $text, array $labels, int $limit): string
    {
        foreach ($labels as $label) if (($pos = mb_stripos($text, $label)) !== false) {
            $chunk = mb_substr($text, (int) $pos, $limit);
            $chunk = preg_split('/(?:anotaci(?:o|ó|\?)n(?:es)?|salvedades|complementaciones|titular(?:es)?|direcci(?:o|ó|\?)n|referencia catastral)/iu', $chunk)[0] ?? $chunk;
            return $this->clean($chunk);
        }
        return '';
    }

    private function addressFromText(string $text): string
    {
        if (!preg_match('/direcci(?:o|ó|\?)n\s+(?:actual\s+)?del\s+inmueble\s*[:#]?\s*(.{6,360})/isu', $text, $m)) return '';
        $value = preg_split('/(?:determinaci(?:o|ó|\?)n|matr.{0,3}cula\s+abierta|anotaci(?:o|ó|\?)n|oficina\s+de\s+registro)/iu', (string) $m[1])[0] ?? '';
        $value = preg_replace('/tipo\s+predio\s*[:#]?\s*[^\n\r]+/iu', ' ', $value) ?? $value;
        $value = preg_replace('/^\s*\d+\s+/u', '', $value) ?? $value;
        return $this->address($value);
    }

    private function lineValue(array $lines, array $labels): string
    {
        $labelPattern = implode('|', array_map(static fn (string $label): string => preg_quote($label, '/'), $labels));
        foreach ($lines as $line) {
            if (preg_match('/\b(?:' . $labelPattern . ')\b\s*[:#-]?\s*(.+)$/iu', $line, $m)) return $this->clean((string) $m[1]);
        }
        return '';
    }

    private function lastTraditionBlock(string $text): string
    {
        preg_match_all('/anotaci(?:o|ó|\?)n\s*:?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*:?\s*\d+/iu', $text, $m, PREG_OFFSET_CAPTURE);
        $blocks = [];
        foreach ($m[0] ?? [] as $index => $match) {
            $start = (int) $match[1];
            $end = isset($m[0][$index + 1][1]) ? (int) $m[0][$index + 1][1] : strlen($text);
            $block = substr($text, $start, $end - $start);
            if ($this->contains($block, ['compraventa', 'adjudicacion', 'adjudicación', 'adjudicaci?n',
                'sucesion', 'sucesión', 'sucesi?n', 'donacion', 'donación', 'donaci?n', 'permuta',
                'remate', 'transferencia', 'dacion', 'daci?n'])) $blocks[] = $block;
        }
        return $blocks ? (string) end($blocks) : '';
    }
}
