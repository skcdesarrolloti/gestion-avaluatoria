<?php
declare(strict_types=1);
namespace App\Support;
use App\Services\LegalCertificateCancellationMatcher;

final class AppraisalLegalView
{
    public static function sections(): array
    {
        return [
            'registral' => ['Identificación registral', 'Valida la trazabilidad base del folio y los datos de apertura, expedición y estado registral.', [
                ['Identificación registral', ['matricula_inmobiliaria', 'circulo_registral', 'orip', 'municipio',
                    'departamento', 'vereda', 'fecha_apertura', 'fecha_expedicion', 'turno', 'pin', 'estado_folio']],
            ]],
            'catastro' => ['Catastro y físico', 'Agrupa la identificación catastral y la descripción física principal del inmueble.', [
                ['Identificación catastral', ['codigo_catastral_actual', 'codigo_catastral_anterior', 'nupre', 'observacion_catastral']],
                ['Identificación física', ['direccion', 'tipo_predio', 'area', 'area_privada', 'area_construida', 'coeficiente', 'cabida_linderos']],
            ]],
            'ph' => ['PH y titularidad', 'Permite revisar si el inmueble pertenece a propiedad horizontal y quién figura como titular actual.', [
                ['Propiedad horizontal', ['reglamento_ph', 'reformas_ph', 'unidad_privada', 'coeficiente_ph',
                    'matricula_matriz', 'matriculas_derivadas']],
                ['Titularidad actual', ['titular_actual', 'documento_soporte_actual', 'valor_ultimo_acto']],
            ]],
            'tradicion' => ['Tradición y cargas', 'Concentra anotaciones históricas, gravámenes, limitaciones y medidas judiciales para una validación detallada.', []],
            'informe' => ['Informe Final', 'Consolida la matriz que pasa al entregable y el control interno final del analista.', [
                ['Variables para el entregable', ['reporte_matricula', 'reporte_escritura_propiedad',
                    'reporte_cedula_catastral', 'reporte_licencia_construccion', 'reporte_constitucion_ph',
                    'reporte_coeficiente_propiedad', 'reporte_titular_actual', 'reporte_afectaciones',
                    'reporte_gravamenes', 'semaforo_manual', 'clasificacion_manual', 'revision_analista',
                    'salvedad_final', 'reporte_conclusion_entregable']],
            ]],
            'impresion' => ['Impresión del entregable profesional', 'Presenta la matriz profesional integrada para el documento final.', []],
        ];
    }

    public static function matrixRows(): array
    {
        return [
            ['1', 'Matrícula inmobiliaria', 'Corresponde al número de matrícula inmobiliaria asignada al bien inmueble.', 'reporte_matricula', 'input'],
            ['2', 'Escritura de propiedad', 'Corresponde a la identificación del último documento de transferencia de dominio del bien inmueble objeto de valuación, registrado en el certificado de tradición y libertad. Se debe identificar el número y la fecha de expedición de la escritura, tipo de acto, número de la notaría y círculo registral al cual pertenece.', 'reporte_escritura_propiedad', 'textarea'],
            ['3', 'Cédula catastral', 'Corresponde a la identificación del número de cédula o código catastral otorgado al bien inmueble.', 'reporte_cedula_catastral', 'input'],
            ['4', 'Licencia de construcción', 'Corresponde a la identificación del número y fecha de expedición del acto por medio del cual la autoridad competente autoriza o autorizó la construcción o reforma e identificación del inmueble. En caso de que el solicitante de la valuación no suministre esta información podrá ser omitida del informe y deberá dejarse constancia del hecho.', 'reporte_licencia_construccion', 'textarea'],
            ['5', 'Constitución de la PH (solo para bienes inmuebles sometidos a este régimen PH)', 'Corresponde a la identificación de los datos de la escritura pública a la cual se somete al régimen de propiedad horizontal el edificio, conjunto o agrupación donde se localiza el bien inmueble objeto de valuación: número de la escritura, fecha, número de la notaría y círculo registral al cual pertenece.', 'reporte_constitucion_ph', 'textarea'],
            ['6', 'Coeficiente de propiedad', 'Corresponde a la identificación del coeficiente de copropiedad para cada uno de los bienes inmuebles objetos de valuación en el régimen de PH, cuando aplique.', 'reporte_coeficiente_propiedad', 'input'],
            ['7', 'Afectaciones', 'Indica las afectaciones que presente el inmueble desde el punto de vista jurídico, tales como afectación a patrimonio de familia, embargo, medida cautelar, vivienda familiar, propiedad horizontal u otras limitaciones que deban destacarse en el informe.', 'reporte_afectaciones', 'textarea'],
        ];
    }

    public static function groupedAnnotations(array $annotations): array
    {
        $groups = ['ph' => [], 'tradicion' => [], 'gravamen' => [], 'limitacion_dominio' => [],
            'medida_cautelar' => [], 'otras' => []];
        foreach (self::resolvedAnnotations($annotations) as $row) {
            $category = (string) ($row['categoria_final'] ?? $row['categoria'] ?? 'otras');
            $key = in_array($category, ['tradicion', 'gravamen', 'limitacion_dominio', 'medida_cautelar'], true)
                ? $category : 'otras';
            if ($category === 'propiedad_horizontal') $groups['ph'][] = $row;
            else $groups[$key][] = $row;
        }
        return $groups;
    }

    public static function resolvedAnnotations(array $annotations): array
    {
        $enriched = array_map(static fn (array $row): array => self::enrich($row), $annotations);
        return (new LegalCertificateCancellationMatcher())->apply($enriched);
    }

    public static function activeAlerts(array $alerts, array $annotations): array
    {
        $closed = array_values(array_filter(array_map(static fn (array $row): string => ($row['estado_juridico'] ?? '') === 'solucionada'
            ? (string) ($row['orden'] ?? '') : '', $annotations)));
        if (!$closed) return $alerts;
        return array_values(array_filter($alerts, static fn ($alert): bool => !preg_match('/anotaci(?:o|ó)n\s+('
            . implode('|', array_map('preg_quote', $closed)) . ')\b/iu', (string) $alert)));
    }

    public static function enrich(array $row, string $category = ''): array
    {
        $text = (string) ($row['texto'] ?? '');
        $category = $category !== '' ? $category : (string) ($row['categoria'] ?? '');
        $row['especificacion'] = self::value($row, 'especificacion') ?: self::match($text,
            '/especificaci(?:o|ó|\?)n\s*:\s*(.+?)(?=\s+personas\s+que\s+intervienen|\s+\bde\s*:|\s+\ba\s*:|$)/isu');
        $row['personaDe'] = self::compactParty(self::value($row, 'personaDe') ?: self::match($text,
            '/\bde\s*:\s*(.+?)(?=\s+\ba\s*:|\s+\bI\b|\s+\bX\b|$)/isu'));
        $row['personaA'] = self::compactParty(self::value($row, 'personaA') ?: self::match($text,
            '/\ba\s*:\s*(.+?)(?=\s+\bI\b|\s+\bX\b|$)/isu'));
        $row['descripcion_acto'] = self::value($row, 'descripcion_acto') ?: self::describe($row, $category);
        return $row;
    }

    public static function trafficLight(array $row): array
    {
        $state = (string) ($row['estado_juridico'] ?? '');
        $review = (string) ($row['requiere_revision'] ?? '');
        $category = (string) ($row['categoria_final'] ?? $row['categoria'] ?? '');
        if ($state === 'solucionada' || ($row['cancelada_por'] ?? '') !== '' || ($row['cancelacion_de'] ?? '') !== '') {
            return ['Verde', 'Cerrada / saneada', 'bg-emerald-50', 'bg-emerald-100 text-emerald-800 border-emerald-200'];
        }
        if ($review === 'Sí' && in_array($category, ['gravamen', 'limitacion_dominio', 'medida_cautelar'], true)) {
            return ['Rojo', 'Afectación sin cierre', 'bg-red-50', 'bg-red-100 text-red-800 border-red-200'];
        }
        if ($review === 'Sí' || $state === 'vigente') {
            return ['Amarillo', 'Por validar', 'bg-amber-50', 'bg-amber-100 text-amber-900 border-amber-200'];
        }
        return ['Verde', 'Sin alerta activa', 'bg-emerald-50', 'bg-emerald-100 text-emerald-800 border-emerald-200'];
    }

    public static function trafficCounts(array $annotations): array
    {
        $counts = ['Rojo' => 0, 'Amarillo' => 0, 'Verde' => 0];
        foreach (self::resolvedAnnotations($annotations) as $row) $counts[self::trafficLight($row)[0]]++;
        return $counts;
    }

    private static function describe(array $row, string $category): string
    {
        $base = mb_strtolower((string) (($row['especificacion'] ?? '') . ' ' . ($row['texto'] ?? '')));
        if ($category === 'propiedad_horizontal') {
            if (str_contains($base, 'reglamento propiedad horizontal')) return 'Acto constitutivo de propiedad horizontal';
            if (str_contains($base, 'coeficiente')) return 'Acto aclaratorio o modificatorio de coeficientes';
            if (preg_match('/reforma|modifica|aclara/u', $base)) return 'Acto reformatorio o aclaratorio del régimen PH';
            return 'Acto relacionado con propiedad horizontal';
        }
        if ($category === 'tradicion') {
            if (str_contains($base, 'compraventa')) return 'Compraventa';
            if (str_contains($base, 'dacion') || str_contains($base, 'dación')) return 'Dación en pago';
            if (str_contains($base, 'adjudic')) return 'Adjudicación';
            return 'Acto de tradición';
        }
        if ($category === 'gravamen') return preg_match('/cancelaci|cancela/u', $base) ? 'Cancelación de hipoteca' : 'Constitución de hipoteca';
        if ($category === 'limitacion_dominio') return 'Limitación al dominio';
        if ($category === 'medida_cautelar') return str_contains($base, 'embargo') ? 'Embargo' : 'Medida cautelar o judicial';
        return 'Otra anotación registral';
    }

    private static function value(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    private static function compactParty(string $value): string
    {
        $value = preg_replace('/OFICINA DE REGISTRO DE INSTRUMENTOS PUBLICOS DE [A-ZÁÉÍÓÚÑ\s]+ ORIP/iu', ' ', $value) ?? $value;
        $value = preg_replace('/\b(?:I|X)\b\s*/u', ' ', $value) ?? $value;
        $parts = array_values(array_unique(array_filter(array_map('trim', preg_split('/\s{2,}|;/', $value) ?: []))));
        $clean = trim(preg_replace('/\s+/', ' ', implode('; ', $parts)) ?? $value);
        return mb_strlen($clean) > 180 ? mb_substr($clean, 0, 177) . '...' : $clean;
    }

    private static function match(string $text, string $pattern): string
    {
        return preg_match($pattern, $text, $m) ? trim(preg_replace('/\s+/', ' ', (string) ($m[1] ?? '')) ?? '') : '';
    }
}
