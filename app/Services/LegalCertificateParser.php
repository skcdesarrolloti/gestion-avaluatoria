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
        $found = count(array_filter($data, static fn ($value): bool => trim((string) $value) !== ''));
        $data = array_replace(AppraisalLegalCatalog::defaults(), $data, $this->reportFields($data, $annotations, $alerts));
        return ['data' => $data, 'annotations' => $annotations, 'alerts' => $alerts,
            'status' => trim($text) === '' ? 'Requiere lectura manual' : 'Lectura preliminar',
            'message' => trim($text) === ''
                ? 'No se obtuvo texto del certificado. Puede ser escaneado o protegido; carga OCR o transcribe los datos relevantes.'
                : 'Lectura preliminar generada con ' . $found . ' campos sugeridos. Revisa cada campo antes del informe.'];
    }

    private function fields(string $text, string $filename): array
    {
        return LegalCertificateFieldExtractor::extract($text, $filename);
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
            'reporte_licencia_construccion' => trim((string) ($data['observacion_catastral'] ?? '')),
            'reporte_constitucion_ph' => ($data['reglamento_ph'] ?? '') !== '' ? 'El certificado reporta referencia a régimen de propiedad horizontal; validar reglamento, coeficiente y unidad privada.' : '',
            'reporte_coeficiente_propiedad' => trim((string) (($data['coeficiente_ph'] ?? '') ?: ($data['coeficiente'] ?? ''))),
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
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
}
