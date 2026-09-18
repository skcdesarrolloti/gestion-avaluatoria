<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalLegalCatalog;

final class LegalCertificateParser
{
    public function parse(string $text, string $filename): array
    {
        $data = $this->fields($text, $filename);
        $annotations = $this->classifyAnnotations($this->annotations($text));
        $alerts = $this->alerts($data, $annotations, $text);
        $data = array_replace(AppraisalLegalCatalog::defaults(), $data, $this->reportFields($data, $annotations, $alerts));
        return ['data' => $data, 'annotations' => $annotations, 'alerts' => $alerts,
            'status' => trim($text) === '' ? 'Requiere lectura manual' : 'Lectura preliminar',
            'message' => trim($text) === ''
                ? 'No se obtuvo texto del certificado. Puede ser escaneado o protegido; carga OCR o transcribe los datos relevantes.'
                : 'Lectura preliminar generada. Revisa cada campo antes del informe.'];
    }

    private function fields(string $text, string $filename): array
    {
        $area = $this->area($text, '/[áa]rea\s+(?:de\s+)?([0-9\.,]{1,30})\s*(?:m2|mts2|metros?\s*cuadrados?)/iu');
        $areaPrivada = $this->area($text, '/[áa]rea\s+privada\b[^0-9]{0,50}([0-9\.,]{1,30})/iu');
        $areaConstruida = $this->area($text, '/[áa]rea\s+construida\b[^0-9]{0,50}([0-9\.,]{1,30})/iu');
        $matriculas = $this->matriculas($text);
        return [
            'archivo_origen' => $filename,
            'matricula_inmobiliaria' => $this->code($this->match($text, [
                '/(?:nro|no|n[uú]mero|numero)\s+matr(?:i|í)cula\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/matr(?:i|í)cula\s+inmobiliaria\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/\b([0-9]{2,4}\s*-\s*[0-9]{3,})\b/u',
            ])),
            'circulo_registral' => $this->clean($this->match($text, [
                '/c[ií]rculo\s+registral\s*[:#]?\s*([^\n\r]{3,120})/iu',
                '/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu',
            ])),
            'orip' => $this->clean($this->match($text, ['/oficina\s+de\s+registro\s+de\s+instrumentos\s+p[uú]blicos\s+de\s*([^\n\r]{3,120})/iu'])),
            'municipio' => $this->clean($this->match($text, [
                '/municipio\s*[:#]?\s*([A-ZÁÉÍÓÚÜÑa-záéíóúüñ ]{3,100})(?=\s+(?:vereda|departamento|direcci[oó]n|referencia|nro|estado|fecha)|[\n\r]|$)/iu',
                '/municipio\s*[:#]?\s*([^\n\r]{3,80})/iu',
            ])),
            'departamento' => $this->clean($this->match($text, [
                '/departamento\s*[:#]?\s*([A-ZÁÉÍÓÚÜÑa-záéíóúüñ ]{3,80})(?=\s+(?:municipio|vereda|direcci[oó]n|referencia|nro|estado|fecha)|[\n\r]|$)/iu',
                '/departamento\s*[:#]?\s*([^\n\r]{3,80})/iu',
            ])),
            'vereda' => $this->clean($this->match($text, [
                '/vereda\s*[:#]?\s*([^\n\r]{3,120})(?=\s+(?:direcci[oó]n|referencia|nro|estado|fecha)|[\n\r]|$)/iu',
            ])),
            'estado_folio' => $this->clean($this->match($text, ['/estado\s+del\s+folio\s*[:#]?\s*([^\n\r]{3,80})/iu', '/folio\s+(abierto|cerrado|cancelado)\b/iu'])),
            'fecha_apertura' => $this->clean($this->match($text, ['/fecha\s+de\s+apertura\s*[:#]?\s*([0-9\/\-]{8,20})/iu'])),
            'fecha_expedicion' => $this->clean($this->match($text, [
                '/fecha\s+de\s+expedici(?:o|ó)n\s*[:#]?\s*([0-9\/\-\s:amp\.]{8,40})/iu',
                '/fecha\s+de\s+impresi(?:o|ó)n\s*[:#]?\s*([0-9\/\-\s:amp\.]{8,40})/iu',
                '/impreso\s+el\s+([0-9\/\-\s:amp\.]{8,40})/iu',
            ])),
            'turno' => $this->clean($this->match($text, ['/turno\s*[:#]?\s*([A-Za-z0-9\-\/\.]{3,50})/iu'])),
            'pin' => $this->clean($this->match($text, [
                '/pin\s*(?:no\.?|n[uú]mero|numero)?\s*[:#]?\s*([A-Za-z0-9\-]{4,40})/iu',
                '/certificado\s+generado\s+con\s+el\s+pin\s+(?:no\.?)?\s*([A-Za-z0-9\-]{4,40})/iu',
            ])),
            'codigo_catastral_actual' => $this->code($this->match($text, [
                '/referencia\s+catastral(?:\s+actual)?\s*[:#]?\s*([0-9A-Za-z\.\- ]{8,80})/iu',
                '/c[eé]dula\s+catastral(?:\s+actual)?\s*[:#]?\s*([0-9A-Za-z\.\- ]{8,80})/iu',
                '/c[oó]digo\s+catastral(?:\s+actual)?\s*[:#]?\s*([0-9A-Za-z\.\- ]{8,80})/iu',
            ])),
            'codigo_catastral_anterior' => $this->code($this->match($text, [
                '/referencia\s+catastral\s+anterior\s*[:#]?\s*([0-9A-Za-z\.\- ]{8,80})/iu',
                '/c[oó]digo\s+catastral\s+anterior\s*[:#]?\s*([0-9A-Za-z\.\- ]{8,80})/iu',
            ])),
            'nupre' => $this->code($this->match($text, ['/nupre\s*[:#]?\s*([0-9A-Za-z\-]{6,40})/iu'])),
            'direccion' => $this->clean($this->match($text, [
                '/direcci(?:o|ó)n\s+actual\s+del\s+inmueble\s*[:#]?\s*([^\n\r]{6,220})/iu',
                '/direcci(?:o|ó)n\s+del\s+inmueble\s*[:#]?\s*([^\n\r]{6,220})/iu',
                '/direcci(?:o|ó)n(?:\s+actual)?\s*[:#]?\s*([^\n\r]{6,220})/iu',
                '/ubicaci(?:o|ó)n\s+del\s+predio\s*[:#]?\s*([^\n\r]{6,220})/iu',
            ])),
            'tipo_predio' => $this->clean($this->match($text, ['/tipo\s+de\s+predio\s*[:#]?\s*([^\n\r]{3,80})/iu', '/destinaci(?:o|ó)n\s+econ[oó]mica\s*[:#]?\s*([^\n\r]{3,120})/iu'])),
            'area' => $area, 'area_privada' => $areaPrivada, 'area_construida' => $areaConstruida,
            'coeficiente' => $this->clean($this->match($text, ['/coeficiente\s*[:#]?\s*([0-9\.,%]{1,30})/iu'])),
            'cabida_linderos' => $this->block($text, ['cabida y linderos', 'cabida/linderos', 'linderos'], 1200),
            'reglamento_ph' => $this->contains($text, ['propiedad horizontal', 'reglamento de propiedad horizontal', 'ley 675']) ? 'Sí' : '',
            'matricula_matriz' => $this->code($this->match($text, ['/matr(?:i|í)cula\s+matriz\s*[:#]?\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu'])),
            'matriculas_derivadas' => implode('; ', array_slice($matriculas, 0, 12)),
            'titular_actual' => $this->clean($this->match($text, ['/titular(?:es)?\s+del\s+derecho\s+real\s+de\s+dominio\s*[:#]?\s*([^\n\r]{4,220})/iu', '/propietario(?:\(s\))?\s*[:#]?\s*([^\n\r]{4,220})/iu'])),
            'documento_soporte_actual' => $this->clean($this->match($text, ['/escritura\s+p[uú]blica\s*(?:no\.?|n[oº])?\s*([A-Za-z0-9\-\/\.]{2,80})/iu'])),
            'valor_ultimo_acto' => $this->clean($this->match($text, ['/valor\s+(?:acto|negocio|compraventa)\s*[:#]?\s*\$?\s*([0-9\.\,]{4,40})/iu'])),
        ];
    }

    private function annotations(string $text): array
    {
        $pattern = '/anotaci(?:o|ó)n\s*:?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*\d+/iu';
        if (!preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) return [];
        $rows = [];
        foreach ($matches[0] as $index => $match) {
            $start = (int) $match[1];
            $end = isset($matches[0][$index + 1][1]) ? (int) $matches[0][$index + 1][1] : strlen($text);
            $block = preg_replace('/\s+/', ' ', substr($text, $start, $end - $start)) ?? '';
            $rows[] = ['orden' => $this->match($block, ['/anotaci(?:o|ó)n\s*:?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*(\d+)/iu']) ?: (string) ($index + 1),
                'fecha' => $this->match($block, ['/fecha\s*[:#]?\s*([0-9\/\-]{8,20})/iu']),
                'documento' => $this->clean($this->match($block, ['/doc\s*\.?\s*:\s*(.+?)(?=\s+valor\s+acto|\s+especificaci|\s+personas\s+que\s+intervienen|$)/iu'])),
                'valor' => $this->clean($this->match($block, ['/valor\s+acto\s*:\s*\$?\s*([0-9][0-9\.,\s]{1,60})/iu'])),
                'texto' => mb_substr(trim($block), 0, 1600)];
        }
        return $rows;
    }

    private function classifyAnnotations(array $rows): array
    {
        foreach ($rows as &$row) {
            $txt = mb_strtolower((string) $row['texto']);
            $category = 'informativa'; $state = 'informativa'; $review = false; $impact = 'No se aprecia afectación material inmediata.';
            if ($this->contains($txt, ['cancelacion', 'cancela', 'levantamiento', 'liberacion', 'desembargo'])) {
                $state = 'solucionada'; $impact = 'Anotación de cancelación, levantamiento o superación de una afectación previa.';
            }
            if ($this->contains($txt, ['compraventa', 'adjudicacion', 'adjudicación', 'sucesion', 'sucesión', 'donacion', 'donación', 'permuta', 'remate', 'transferencia'])) {
                $category = 'tradicion'; $impact = 'Integra la cadena de tradición o el soporte de titularidad.';
            }
            if ($this->contains($txt, ['propiedad horizontal', 'reglamento', 'coeficiente', 'copropiedad'])) {
                $category = 'propiedad_horizontal'; $state = $state === 'solucionada' ? $state : 'vigente'; $review = true;
                $impact = 'Delimita régimen de propiedad horizontal, coeficientes o unidad privada.';
            }
            if ($this->contains($txt, ['hipoteca', 'gravamen', 'prenda'])) {
                $category = 'gravamen'; $state = $state === 'solucionada' ? $state : 'vigente'; $review = $state !== 'solucionada';
                $impact = 'Puede afectar la libre disposición mientras permanezca vigente.';
            }
            if ($this->contains($txt, ['embargo', 'demanda', 'medida cautelar', 'secuestro', 'prohibicion', 'prohibición'])) {
                $category = 'medida_cautelar'; $state = $state === 'solucionada' ? $state : 'vigente'; $review = $state !== 'solucionada';
                $impact = 'Riesgo jurídico relevante; exige revisión especializada.';
            }
            if ($this->contains($txt, ['usufructo', 'patrimonio de familia', 'afectacion a vivienda familiar', 'afectación a vivienda familiar', 'servidumbre'])) {
                $category = 'limitacion_dominio'; $state = $state === 'solucionada' ? $state : 'vigente'; $review = $state !== 'solucionada';
                $impact = 'Impone limitación o carga al ejercicio del dominio.';
            }
            $row += ['categoria' => $category, 'estado_juridico' => $state,
                'requiere_revision' => $review ? 'Sí' : 'No', 'impacto_resumen' => $impact];
        }
        return $rows;
    }

    private function alerts(array $data, array $annotations, string $text): array
    {
        $alerts = [];
        if (trim((string) $data['matricula_inmobiliaria']) === '') $alerts[] = 'No se identificó matrícula inmobiliaria.';
        if (preg_match('/folio\s+(cerrado|cancelado)/iu', (string) $data['estado_folio'] . ' ' . $text)) $alerts[] = 'El certificado sugiere folio cerrado o cancelado.';
        foreach ($annotations as $row) {
            if (($row['requiere_revision'] ?? '') === 'Sí' && ($row['estado_juridico'] ?? '') !== 'solucionada') {
                $alerts[] = 'Revisar anotación ' . ($row['orden'] ?? '') . ': ' . ($row['categoria'] ?? 'hallazgo jurídico') . '.';
            }
        }
        return array_values(array_unique(array_filter($alerts)));
    }

    private function reportFields(array $data, array $annotations, array $alerts): array
    {
        $tradition = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'tradicion'));
        $debts = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'gravamen'));
        $affects = array_values(array_filter($annotations, fn (array $a): bool => in_array(($a['categoria'] ?? ''), ['limitacion_dominio', 'medida_cautelar'], true)));
        return [
            'reporte_matricula' => trim('Matrícula inmobiliaria ' . ($data['matricula_inmobiliaria'] ?? '') . ', ORIP ' . (($data['orip'] ?? '') ?: ($data['circulo_registral'] ?? '')) . '.'),
            'reporte_escritura_propiedad' => $tradition ? 'Se identifican actos de tradición que deben confrontarse con las anotaciones del certificado.' : '',
            'reporte_cedula_catastral' => trim((string) ($data['codigo_catastral_actual'] ?? '')),
            'reporte_constitucion_ph' => ($data['reglamento_ph'] ?? '') !== '' ? 'El certificado reporta referencia a régimen de propiedad horizontal; validar reglamento, coeficiente y unidad privada.' : '',
            'reporte_titular_actual' => (string) ($data['titular_actual'] ?? ''),
            'reporte_afectaciones' => $affects ? 'Existen anotaciones que pueden constituir limitaciones o medidas cautelares. Validar vigencia y cancelaciones.' : '',
            'reporte_gravamenes' => $debts ? 'Se identifican gravámenes o hipotecas en la lectura preliminar; confirmar si están vigentes o cancelados.' : '',
            'reporte_conclusion_entregable' => $alerts ? 'Lectura jurídica preliminar con alertas pendientes de revisión por el analista.' : 'Lectura jurídica preliminar sin alertas automáticas relevantes; validar contra el certificado completo.',
        ];
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }
    private function clean(string $value): string { return mb_substr(trim(preg_replace('/\s+/', ' ', $value) ?? $value), 0, 500); }
    private function code(string $value): string { return preg_replace('/\s+/', '', $this->clean($value)) ?? ''; }
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
    private function area(string $text, string $pattern): string { return preg_match($pattern, $text, $m) ? str_replace(',', '.', (string) $m[1]) : ''; }
    private function matriculas(string $text): array { preg_match_all('/\b[0-9]{2,4}\s*-\s*[0-9]{3,}\b/u', $text, $m); return array_values(array_unique(array_map(fn ($v) => $this->code((string) $v), $m[0] ?? []))); }
    private function block(string $text, array $labels, int $limit): string { foreach ($labels as $label) if (($pos = mb_stripos($text, $label)) !== false) return $this->clean(mb_substr($text, (int) $pos, $limit)); return ''; }
}
