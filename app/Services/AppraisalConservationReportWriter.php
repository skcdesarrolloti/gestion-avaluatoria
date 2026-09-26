<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalConservationCatalog;

final class AppraisalConservationReportWriter
{
    public static function build(array $groups, string $proposed, string $adopted, array $critical, string $confidence, string $justification): string
    {
        if (!$groups) return 'El estado de conservación queda pendiente de inspección por componentes.';
        $lines = ['Cuadro resumen del estado de conservación', 'Grupo | Estado | Principal conclusión'];
        foreach (AppraisalConservationCatalog::groups() as $catalogGroup) {
            $gid = (string) ($catalogGroup['id'] ?? '');
            $group = $groups[$gid] ?? null;
            $lines[] = $group
                ? $group['label'] . ' | ' . self::stateText($group['state']) . ' | ' . self::shortConclusion($group)
                : (string) ($catalogGroup['label'] ?? '') . ' | No diligenciado | Sin conclusión automática por falta de selección.';
        }
        $lines[] = 'Estado global | ' . self::stateText($adopted ?: $proposed) . ' | ' . self::globalShortConclusion($groups, $critical);
        $lines[] = '';
        $lines[] = 'Lectura técnica por grupo';
        foreach (AppraisalConservationCatalog::groups() as $catalogGroup) {
            $gid = (string) ($catalogGroup['id'] ?? '');
            $heading = trim((string) ($catalogGroup['number'] ?? '') . ' ' . (string) ($catalogGroup['label'] ?? ''));
            $lines[] = $heading . ': ' . (isset($groups[$gid])
                ? self::groupConclusion($groups[$gid])
                : 'no se registraron selecciones para este grupo; por tanto, no se emite calificación automática.');
        }
        $lines[] = '';
        $lines[] = self::globalConclusion($groups, $proposed, $adopted, $critical, $confidence, $justification);
        $lines[] = 'Base técnica: ' . AppraisalConservationCatalog::technicalReference() . ' La calificación documenta condición observable y criterio valuatorio; no sustituye diagnóstico especializado.';
        return implode("\n", $lines);
    }

    public static function groupConclusion(array $group): string
    {
        $parts = ['se asigna ' . self::stateText($group['state']) . ' al grupo.'];
        foreach ($group['items'] as $item) {
            $line = $item['subcomponent_label'] . ': Hallazgo observado: ' . self::observedFinding($item) . '. ';
            $line .= 'Interpretación técnica: ' . self::interpretationFor($item) . '. ';
            $line .= 'Estado asignado: ' . self::stateText($item['state_adopted'] ?: $item['state_proposed']) . '.';
            if ($item['notes'] !== '') $line .= ' Observación del analista: ' . rtrim($item['notes'], '.') . '.';
            if ($item['evidence'] !== '') $line .= ' Evidencia: ' . rtrim($item['evidence'], '.') . '.';
            $parts[] = $line;
        }
        return implode(' ', $parts);
    }

    private static function globalConclusion(array $groups, string $proposed, string $adopted, array $critical, string $confidence, string $justification): string
    {
        $state = self::stateDefinition($adopted ?: $proposed);
        $global = 'Conclusión global: del análisis integral de los componentes constructivos diligenciados se concluye que la unidad presenta un Estado de Conservación ' . self::stateText($adopted ?: $proposed) . '.';
        if (($state['criterion'] ?? '') !== '') $global .= ' Esta clasificación corresponde a: ' . rtrim((string) $state['criterion'], '.') . '.';
        $global .= ' La decisión se fundamenta en los hallazgos observados, la interpretación técnica registrada para cada grupo y la escala de estados de conservación utilizada por el IGAC. Nivel de verificación: ' . $confidence . '.';
        if ($critical) $global .= ' Se identificaron condiciones críticas en: ' . implode('; ', array_slice($critical, 0, 4)) . '.';
        else $global .= ' No se registraron condiciones críticas en los componentes diligenciados.';
        if ($justification !== '') $global .= ' Justificación del avaluador: ' . rtrim($justification, '.') . '.';
        return $global;
    }

    private static function shortConclusion(array $group): string
    {
        $worst = null; $worstValue = -1.0;
        foreach ($group['items'] as $item) {
            $value = (float) ($item['state_adopted'] ?: $item['state_proposed'] ?: 0);
            if ($value > $worstValue) { $worst = $item; $worstValue = $value; }
        }
        if (!$worst) return 'Sin conclusión automática.';
        $finding = self::observedFinding($worst);
        if ($finding !== 'sin hallazgo específico registrado') return $worst['subcomponent_label'] . ': ' . $finding;
        return 'Componentes diligenciados sin hallazgo negativo específico.';
    }

    private static function observedFinding(array $item): string
    {
        if ($item['finding'] !== '') return $item['finding'];
        if ($item['notes'] !== '') return $item['notes'];
        return 'sin hallazgo específico registrado';
    }

    private static function interpretationFor(array $item): string
    {
        $chunks = [];
        if ($item['guide'] !== '' && $item['finding'] !== '') $chunks[] = 'el hallazgo orienta la lectura hacia el rango ' . $item['guide'];
        if ($item['functionality'] !== '') $chunks[] = 'funcionalidad ' . $item['functionality'];
        if ($item['intervention'] !== '') $chunks[] = 'intervención aparente ' . self::interventionText($item['intervention']);
        $state = self::stateDefinition($item['state_adopted'] ?: $item['state_proposed']);
        if (($state['intervention'] ?? '') !== '') $chunks[] = (string) $state['intervention'];
        return $chunks ? implode('; ', $chunks) : 'sin interpretación automática adicional por ausencia de hallazgo, funcionalidad o intervención seleccionada';
    }

    private static function interventionText(string $level): string
    {
        foreach (AppraisalConservationCatalog::interventions() as $row) {
            if ((string) ($row['level'] ?? '') === $level) return $level . ' - ' . (string) ($row['label'] ?? '');
        }
        return 'nivel ' . $level;
    }

    private static function stateDefinition(string $state): array
    {
        foreach (AppraisalConservationCatalog::states() as $row) {
            if ((string) ($row['value'] ?? '') === $state) return $row;
        }
        return [];
    }

    private static function globalShortConclusion(array $groups, array $critical): string
    {
        if ($critical) return 'Existen hallazgos críticos que condicionan la conclusión.';
        $labels = array_values(array_map(static fn (array $g): string => $g['label'], $groups));
        return $labels ? 'Conclusión derivada de ' . implode(', ', array_slice($labels, 0, 4)) . '.' : 'Pendiente de diligenciamiento.';
    }

    private static function stateText(string $state): string
    {
        return $state !== '' ? AppraisalConservationCatalog::stateLabel($state) : 'pendiente';
    }
}
