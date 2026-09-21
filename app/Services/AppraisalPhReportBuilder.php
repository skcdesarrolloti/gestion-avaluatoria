<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhReportBuilder
{
    public function build(array $core, array $technical, array $common, array $documents,
        array $risks, array $photos, string $typology, string $sourceSummary, array $findings): array
    {
        $typologyLabel = AppraisalPhCatalog::typologies()[$typology] ?? 'tipología PH pendiente';
        $name = $this->value($core['ph_name'] ?? '') ?: 'la copropiedad analizada';
        $missingId = $this->missing(['matrícula matriz' => $core['matrix_registration'] ?? '',
            'unidad privada' => $core['private_unit'] ?? '', 'coeficiente' => $core['coefficient'] ?? '']);
        $priority = $this->priorityText($common, $typology);
        $detected = $this->detectedCommonText($common);
        $rules = $this->presentLabels([
            'usos permitidos' => $technical['usos_permitidos'] ?? '',
            'restricciones' => $technical['usos_restringidos'] ?? '',
            'reglas constructivas' => $technical['reglas_constructivas'] ?? '',
            'operación' => $technical['condiciones_normativas_operativas'] ?? '',
        ]);
        $adminMissing = $this->missing(['administración vigente' => $core['administration_name'] ?? '',
            'cuota' => $core['monthly_fee'] ?? '', 'paz y salvo' => $core['fee_status'] ?? '',
            'seguros vigentes' => $core['insurance_status'] ?? '']);
        $tab = [];
        $tab['resumen_base_ph'] = "Base comparativa: {$name}, {$typologyLabel}. {$priority} "
            . "La clasificación de dotación es preliminar y debe compararse solo con PH de la misma tipología, escala y localización.";
        $tab['resumen_trazabilidad_ph'] = 'Trazabilidad: ' . ($sourceSummary ?: 'sin soporte procesado.')
            . ' El OCR y los extractos son evidencia de trabajo; el informe debe citar el documento fuente y conservar salvedad de lectura.';
        $tab['resumen_identificacion_ph'] = "Identificación: se reconoce {$name}. "
            . ($missingId ? 'Falta confirmar ' . $missingId . ' para vincularlo plenamente con el bien sujeto.' : 'La identificación principal tiene soporte diligenciado.');
        $tab['resumen_tipologia_ph'] = "Tipología y régimen: lectura preliminar como {$typologyLabel}. "
            . ($this->value($technical['uso_dominante'] ?? '') !== '' ? 'El uso dominante cuenta con referencia documental que debe resumirse sin copiar OCR.' : 'Falta depurar uso dominante y complementario.');
        $tab['resumen_configuracion_ph'] = 'Configuración predial: ' . $this->presentLabels([
            'etapas/sectores' => $technical['etapas_copropiedad'] ?? '',
            'unidades privadas' => $technical['numero_unidades'] ?? '',
            'áreas' => $technical['resumen_areas_conjunto'] ?? '',
            'desenglobes' => $technical['desarrollos_relevantes'] ?? '',
        ], 'hay referencia a ') . '. Usar solo datos claros; linderos y recortes notariales quedan como soporte.';
        $tab['resumen_comunes_ph'] = "Bienes comunes y soporte: {$detected} "
            . 'Las menciones documentales no prueban estado actual; se deben cruzar con visita y fotografías.';
        $tab['resumen_reglas_ph'] = 'Reglas de uso y operación: ' . ($rules ?: 'sin reglas depuradas suficientes')
            . '. Llevar al informe solo restricciones claras y aplicables al bien sujeto.';
        $tab['resumen_administracion_ph'] = 'Administración y cargas: ' . ($adminMissing
            ? 'no hay soporte vigente de ' . $adminMissing . '.'
            : 'hay datos administrativos diligenciados.')
            . ' Las referencias a coeficientes, expensas o fondo son reglamentarias hasta confirmar soporte actual.';
        $tab['resumen_incidencia_ph'] = "Incidencia valuatoria: {$name} presenta una dotación común {$this->level($technical)} para {$typologyLabel}; "
            . 'el impacto final en funcionalidad, comercialización y valor queda sujeto al criterio del analista.';
        $tab['resumen_notas_ph'] = 'Notas y salvedades: aplicar Ley 675 para bienes comunes, coeficientes y expensas; '
            . 'usar Decreto 1420, Resolución IGAC 941 e IVS como soporte metodológico, sin convertir el OCR en conclusión jurídica.';
        $report = $this->report($name, $typologyLabel, $detected, $priority);
        return ['diagnosis_text' => $tab['resumen_incidencia_ph'], 'report_text' => $report, 'technical' => $tab];
    }

    private function report(string $name, string $typology, string $detected, string $priority): string
    {
        return "La copropiedad corresponde preliminarmente a {$name}, clasificada para efectos de análisis como {$typology}. "
            . "{$detected} {$priority} Estos hallazgos son de carácter documental y deben contrastarse con visita, registro fotográfico, "
            . 'certificado de tradición, reglamento completo, paz y salvo y certificación actual de administración. '
            . 'Esta lectura técnica no reemplaza estudio de títulos ni verificación jurídica independiente.';
    }

    private function detectedCommonText(array $common): string
    {
        $labels = AppraisalPhCatalog::commonAreas();
        $names = [];
        foreach ($common as $key => $row) {
            if (($row['status'] ?? '') !== '') $names[] = $labels[$key] ?? $key;
            if (count($names) >= 7) break;
        }
        return $names ? 'se identifican menciones de ' . implode(', ', $names) . '.'
            : 'no hay evidencia suficiente de bienes comunes específicos.';
    }

    private function priorityText(array $common, string $typology): string
    {
        $keys = AppraisalPhCatalog::typologyPriorities()[$typology] ?? [];
        if (!$keys) return 'Seleccione tipología para medir dotación prioritaria.';
        $found = 0;
        foreach ($keys as $key) if (($common[$key]['status'] ?? '') !== '') $found++;
        return "Se detectan {$found} de " . count($keys) . ' factores prioritarios de dotación.';
    }

    private function presentLabels(array $values, string $prefix = ''): string
    {
        $names = [];
        foreach ($values as $label => $value) if ($this->value($value) !== '') $names[] = $label;
        return $names ? $prefix . implode(', ', $names) : '';
    }

    private function missing(array $values): string
    {
        $missing = [];
        foreach ($values as $label => $value) if ($this->value($value) === '') $missing[] = $label;
        return implode(', ', $missing);
    }

    private function level(array $technical): string
    {
        $text = $this->value($technical['nivel_dotacion_comparativa'] ?? '');
        return $text !== '' ? mb_strtolower(strtok($text, ':') ?: $text) : 'por confirmar';
    }

    private function value(mixed $value): string
    {
        return trim((string) $value);
    }
}
