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

    private function annotations(string $text): array { return (new LegalCertificateAnnotationExtractor())->extract($text); }

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
            $row += ['categoria' => $category, 'categoria_final' => $category,
                'estado_juridico' => $state, 'descripcion_acto' => $this->describeAct($row, $category),
                'requiere_revision' => $review ? 'Sí' : 'No', 'impacto_resumen' => $impact];
        }
        unset($row);
        return (new LegalCertificateCancellationMatcher())->apply($rows);
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
        $opinion = (new LegalCertificateOpinionBuilder())->build($data, $tradition, $debts, $limits, $measures, $ph, $alerts);
        $salvedad = 'Lectura automática preliminar basada en el certificado cargado; debe validarse contra el folio completo y los soportes del encargo.';
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
            'reporte_matricula' => trim((string) ($data['matricula_inmobiliaria'] ?? '')),
            'reporte_escritura_propiedad' => $this->lastActSummary($tradition),
            'reporte_cedula_catastral' => trim((string) ($data['codigo_catastral_actual'] ?? '')),
            'reporte_licencia_construccion' => 'No se identifica licencia de construcción dentro del certificado de tradición y libertad. Su verificación debe realizarse con expediente urbanístico o soporte documental aportado.',
            'reporte_constitucion_ph' => $this->phSummary($data, $ph),
            'reporte_coeficiente_propiedad' => trim((string) (($data['coeficiente_ph'] ?? '') ?: ($data['coeficiente'] ?? ''))),
            'reporte_titular_actual' => $this->titleSummary($data, $tradition),
            'reporte_afectaciones' => $this->annotationSummary($affects, 'No se identifican afectaciones o medidas vigentes concluyentes en la lectura automática.'),
            'reporte_gravamenes' => $this->annotationSummary($debts, 'No se identifican gravámenes vigentes concluyentes en la lectura automática.'),
            'semaforo_manual' => $opinion['level'], 'clasificacion_manual' => $opinion['classification'],
            'revision_analista' => $alerts ? implode("\n", $alerts) : 'Sin alertas automáticas; conservar revisión humana del certificado.',
            'salvedad_final' => $salvedad,
            'reporte_conclusion_entregable' => $opinion['conclusion'],
            'reporte_profesional_entregable' => $opinion['integrated'] . "\n\n" . $salvedad,
        ];
    }

    private function annotationSummary(array $rows, string $empty): string
    {
        if (!$rows) return $empty;
        return implode("\n", array_map(static fn (array $row): string => 'Anotación ' . ($row['orden'] ?? '')
            . ': ' . ($row['impacto_resumen'] ?? 'Revisión preliminar pendiente.')
            . (($row['cancelada_por'] ?? '') !== '' ? ' Relación registral: se cancela con la anotación ' . $row['cancelada_por'] . '.' : '')
            . (($row['cancelacion_de'] ?? '') !== '' ? ' Relación registral: cancela la anotación ' . $row['cancelacion_de'] . '.' : '')
            . (($row['documento'] ?? '') !== '' ? ' Soporte: ' . $row['documento'] . '.' : ''), $rows));
    }

    private function describeAct(array $row, string $category): string
    {
        $base = mb_strtolower((string) (($row['especificacion'] ?? '') . ' ' . ($row['texto'] ?? '')));
        if ($category === 'propiedad_horizontal') {
            if ($this->contains($base, ['reglamento propiedad horizontal', 'constitucion de propiedad horizontal'])) return 'Acto constitutivo de propiedad horizontal';
            if ($this->contains($base, ['coeficiente', 'aclaratoria'])) return 'Acto aclaratorio o modificatorio de coeficientes';
            if ($this->contains($base, ['reforma', 'modificacion', 'modifica'])) return 'Acto reformatorio o aclaratorio del régimen PH';
            return 'Acto relacionado con propiedad horizontal';
        }
        if ($category === 'tradicion') {
            foreach (['compraventa', 'adjudicación', 'adjudicacion', 'dación en pago', 'dacion en pago',
                'donación', 'donacion', 'sucesión', 'sucesion', 'permuta', 'remate'] as $mode) {
                if (mb_stripos($base, $mode) !== false) return ucfirst(str_replace('cion', 'ción', $mode));
            }
            return 'Acto de tradición';
        }
        if ($category === 'gravamen') return $this->contains($base, ['cancelacion', 'cancela'])
            ? 'Cancelación de hipoteca' : 'Constitución de hipoteca';
        if ($category === 'limitacion_dominio') return 'Limitación al dominio';
        if ($category === 'medida_cautelar') {
            if ($this->contains($base, ['embargo'])) return 'Embargo';
            if ($this->contains($base, ['demanda'])) return 'Demanda registrada';
            return 'Medida cautelar o judicial';
        }
        return 'Otra anotación registral';
    }

    private function lastActSummary(array $tradition): string
    {
        if (!$tradition) return '';
        $row = end($tradition);
        $parts = [];
        if (($row['orden'] ?? '') !== '') $parts[] = 'Anotación ' . $row['orden'];
        if (($row['documento'] ?? '') !== '') $parts[] = $row['documento'];
        if (($row['fecha'] ?? '') !== '') $parts[] = 'Fecha: ' . $row['fecha'];
        if (($row['descripcion_acto'] ?? '') !== '') $parts[] = 'Acto: ' . $row['descripcion_acto'];
        return implode(' | ', $parts);
    }

    private function phSummary(array $data, array $ph): string
    {
        if (($data['reglamento_ph'] ?? '') === '' && !$ph) return '';
        $row = $ph[0] ?? [];
        $parts = ['Sí, el inmueble presenta régimen de propiedad horizontal.'];
        if (($row['documento'] ?? '') !== '') $parts[] = 'Soporte: ' . $row['documento'] . '.';
        if (($row['fecha'] ?? '') !== '') $parts[] = 'Fecha: ' . $row['fecha'] . '.';
        if (($data['matricula_matriz'] ?? '') !== '') $parts[] = 'Matrícula matriz: ' . $data['matricula_matriz'] . '.';
        return implode(' ', $parts);
    }

    private function titleSummary(array $data, array $tradition): string
    {
        $row = $tradition ? end($tradition) : [];
        $parts = [];
        if (($data['titular_actual'] ?? '') !== '') $parts[] = 'Titular inscrito: ' . $data['titular_actual'] . '.';
        if (($row['descripcion_acto'] ?? '') !== '') $parts[] = 'Modo de adquisición: ' . $row['descripcion_acto'] . '.';
        if (($row['documento'] ?? '') !== '') $parts[] = 'Soporte: ' . $row['documento'] . '.';
        if (($row['fecha'] ?? '') !== '') $parts[] = 'Fecha del acto: ' . $row['fecha'] . '.';
        if (($row['valor'] ?? '') !== '') $parts[] = 'Valor del acto: ' . $row['valor'] . '.';
        return implode(' ', $parts);
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }
    private function clean(string $value): string { return mb_substr(trim(preg_replace('/\s+/', ' ', $value) ?? $value), 0, 500); }
    private function contains(string $text, array $needles): bool { foreach ($needles as $n) if (mb_stripos($text, $n) !== false) return true; return false; }
}
