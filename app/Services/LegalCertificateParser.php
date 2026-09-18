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
                ? 'No se obtuvo texto del certificado. Puede ser imagen sin OCR disponible, PDF escaneado o archivo protegido; transcribe los datos relevantes.'
                : 'Lectura preliminar generada con ' . $found . ' campos sugeridos. Revisa cada campo antes del informe.'];
    }

    private function fields(string $text, string $filename): array
    {
        $data = LegalCertificateFieldExtractor::extract($text, $filename);
        $trusted = ['matricula_inmobiliaria', 'circulo_registral', 'orip', 'municipio', 'departamento',
            'vereda', 'estado_folio', 'fecha_apertura', 'fecha_expedicion', 'turno', 'pin', 'tipo_predio',
            'direccion', 'area', 'coeficiente', 'matricula_matriz'];
        foreach ((new LegalCertificateSnrHeaderExtractor())->extract($text) as $key => $value) {
            if (in_array($key, $trusted, true) || trim((string) ($data[$key] ?? '')) === '') $data[$key] = $value;
        }
        return $data;
    }

    private function annotations(string $text): array
    {
        return (new LegalCertificateAnnotationExtractor())->extract($text);
    }

    private function classifyAnnotations(array $rows): array
    {
        foreach ($rows as &$row) {
            $txt = mb_strtolower((string) $row['texto']);
            $category = 'informativa'; $state = 'informativa'; $review = false; $impact = 'No se aprecia afectación material inmediata.';
            if ($this->contains($txt, ['cancelacion', 'cancela', 'levantamiento', 'liberacion', 'desembargo'])) {
                $state = 'solucionada'; $impact = 'Anotación de cancelación, levantamiento o superación de una afectación previa.';
            }
            if ($this->contains($txt, ['compraventa', 'adjudicacion', 'adjudicación', 'adjudicaci?n',
                'sucesion', 'sucesión', 'sucesi?n', 'donacion', 'donación', 'donaci?n', 'permuta',
                'remate', 'transferencia', 'dacion', 'daci?n'])) {
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
        $limits = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'limitacion_dominio'));
        $measures = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'medida_cautelar'));
        $ph = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'propiedad_horizontal'));
        $others = array_values(array_filter($annotations, fn (array $a): bool => ($a['categoria'] ?? '') === 'informativa'));
        $affects = array_merge($limits, $measures);
        $level = ($measures || $limits) ? 'Crítico' : ($debts || $alerts ? 'Atención' : 'Normal');
        $classification = $level === 'Crítico' ? 'Requiere estudio jurídico especializado'
            : ($level === 'Atención' ? 'Con alertas para revisión jurídica' : 'Sin alertas automáticas relevantes');
        $salvedad = 'Lectura automática preliminar basada en el certificado cargado; debe validarse contra el folio completo y los soportes del encargo.';
        $integrated = $this->integratedReport($data, $tradition, $debts, $affects, $ph, $alerts);
        return [
            'check_tradicion' => $tradition ? 'Sí' : '', 'check_gravamenes' => $debts ? 'Sí' : '',
            'check_limitaciones_dominio' => $limits ? 'Sí' : '', 'check_medidas_cautelares' => $measures ? 'Sí' : '',
            'check_propiedad_horizontal' => $ph ? 'Sí' : '', 'check_otras' => $others ? 'Sí' : '',
            'revision_tradicion' => $this->annotationSummary($tradition, 'No se identificaron actos de tradición en la lectura automática.'),
            'revision_gravamenes' => $this->annotationSummary($debts, 'No se identificaron gravámenes en la lectura automática.'),
            'revision_limitaciones_dominio' => $this->annotationSummary($limits, 'No se identificaron limitaciones al dominio en la lectura automática.'),
            'revision_medidas_cautelares' => $this->annotationSummary($measures, 'No se identificaron medidas cautelares en la lectura automática.'),
            'revision_propiedad_horizontal' => $this->annotationSummary($ph, 'No se identificó anotación específica de propiedad horizontal.'),
            'revision_otras_cargas' => $this->annotationSummary($others, 'Sin otras notas clasificadas automáticamente.'),
            'reporte_matricula' => trim('Matrícula inmobiliaria ' . ($data['matricula_inmobiliaria'] ?? '') . ', ORIP ' . (($data['orip'] ?? '') ?: ($data['circulo_registral'] ?? '')) . '.'),
            'reporte_escritura_propiedad' => $tradition ? 'Se identifican actos de tradición que deben confrontarse con las anotaciones del certificado.' : '',
            'reporte_cedula_catastral' => trim((string) ($data['codigo_catastral_actual'] ?? '')),
            'reporte_licencia_construccion' => trim((string) ($data['observacion_catastral'] ?? '')),
            'reporte_constitucion_ph' => ($data['reglamento_ph'] ?? '') !== '' ? 'El certificado reporta referencia a régimen de propiedad horizontal; validar reglamento, coeficiente y unidad privada.' : '',
            'reporte_coeficiente_propiedad' => trim((string) (($data['coeficiente_ph'] ?? '') ?: ($data['coeficiente'] ?? ''))),
            'reporte_titular_actual' => (string) ($data['titular_actual'] ?? ''),
            'reporte_afectaciones' => $affects ? 'Existen anotaciones que pueden constituir limitaciones o medidas cautelares. Validar vigencia y cancelaciones.' : '',
            'reporte_gravamenes' => $debts ? 'Se identifican gravámenes o hipotecas en la lectura preliminar; confirmar si están vigentes o cancelados.' : '',
            'semaforo_manual' => $level, 'clasificacion_manual' => $classification,
            'revision_analista' => $alerts ? implode("\n", $alerts) : 'Sin alertas automáticas; conservar revisión humana del certificado.',
            'salvedad_final' => $salvedad,
            'reporte_conclusion_entregable' => $alerts ? 'Lectura jurídica preliminar con alertas pendientes de revisión por el analista.' : 'Lectura jurídica preliminar sin alertas automáticas relevantes; validar contra el certificado completo.',
            'reporte_profesional_entregable' => $integrated . "\n\n" . $salvedad,
        ];
    }

    private function annotationSummary(array $rows, string $empty): string
    {
        if (!$rows) return $empty;
        return implode("\n", array_map(static fn (array $row): string => 'Anotación ' . ($row['orden'] ?? '')
            . ': ' . ($row['impacto_resumen'] ?? 'Revisión preliminar pendiente.')
            . (($row['documento'] ?? '') !== '' ? ' Soporte: ' . $row['documento'] . '.' : ''), $rows));
    }

    private function integratedReport(array $data, array $tradition, array $debts, array $affects, array $ph, array $alerts): string
    {
        $parts = [];
        if (($data['matricula_inmobiliaria'] ?? '') !== '') $parts[] = 'El inmueble se revisó con matrícula inmobiliaria '
            . $data['matricula_inmobiliaria'] . ', asociada a la ORIP ' . (($data['orip'] ?? '') ?: ($data['circulo_registral'] ?? 'pendiente')) . '.';
        if (($data['titular_actual'] ?? '') !== '') $parts[] = 'La titularidad preliminar leída corresponde a ' . $data['titular_actual'] . '.';
        if ($tradition) $parts[] = 'La cadena de tradición presenta actos que deben cotejarse con el certificado completo.';
        if ($debts) $parts[] = 'Se detectaron gravámenes o hipotecas para confirmar vigencia y cancelaciones.';
        if ($affects) $parts[] = 'Se detectaron posibles limitaciones o medidas cautelares que requieren revisión jurídica.';
        if ($ph) $parts[] = 'Hay referencias a propiedad horizontal o copropiedad que deben validarse con reglamento y coeficientes.';
        if ($alerts) $parts[] = 'Alertas: ' . implode(' ', $alerts);
        return $parts ? implode(' ', $parts) : 'Lectura registral preliminar sin hallazgos automáticos concluyentes.';
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }
    private function clean(string $value): string { return mb_substr(trim(preg_replace('/\s+/', ' ', $value) ?? $value), 0, 500); }
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
}
