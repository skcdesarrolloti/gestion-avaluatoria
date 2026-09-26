<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalConservationCatalog;

final class AppraisalConservationNarrator
{
    public static function normalize(array $postedItems, array $summary, ?int $actorId = null): array
    {
        $items = []; $flat = [];
        foreach (AppraisalConservationCatalog::subcomponentIndex() as $id => [$group, $sub]) {
            $raw = is_array($postedItems[$id] ?? null) ? $postedItems[$id] : [];
            $item = [
                'group_id' => (string) ($group['id'] ?? ''), 'group_label' => (string) ($group['label'] ?? ''),
                'subcomponent_id' => $id, 'subcomponent_label' => (string) ($sub['label'] ?? ''),
                'applicability' => self::select($raw['applicability'] ?? 'aplica'),
                'material' => self::text($raw['material'] ?? '', 160),
                'finding' => self::text($raw['finding'] ?? '', 220),
                'functionality' => self::select($raw['functionality'] ?? ''),
                'intervention' => self::select($raw['intervention'] ?? ''),
                'evidence' => self::text($raw['evidence'] ?? '', 180),
                'state_adopted' => self::state($raw['state_adopted'] ?? ''),
                'notes' => self::text($raw['notes'] ?? '', 600),
            ];
            $evidence = self::evidenceFor($id, $item);
            $item += $evidence;
            if (self::hasContent($item)) $items[$id] = $item;
            if ($item['state_adopted'] !== '') $flat[$id] = $item['state_adopted'];
        }
        $result = self::result($items, $summary, $actorId);
        return [
            'legacy_json' => json_encode($flat, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'result_json' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'generated_text' => $result['generated_text'],
            'approved_text' => self::text($summary['approved_text'] ?? '', 8000),
        ];
    }

    public static function textForReport(array $unit): string
    {
        $approved = self::text($unit['conservation_approved_text'] ?? '', 8000);
        if ($approved !== '') return $approved;
        return self::text($unit['conservation_generated_text'] ?? '', 8000);
    }

    private static function result(array $items, array $summary, ?int $actorId): array
    {
        $groups = []; $critical = []; $verified = 0; $applicable = 0; $states = [];
        foreach ($items as $item) {
            if ($item['applicability'] === 'no_aplica') continue;
            $applicable++;
            if ($item['applicability'] !== 'no_verificable') $verified++;
            $state = self::proposedState($item);
            $states[] = $state;
            $gid = $item['group_id'];
            $groups[$gid] ??= ['label' => $item['group_label'], 'items' => [], 'state' => ''];
            $item['state_proposed'] = $state;
            $groups[$gid]['items'][] = $item;
            if ($item['critical']) $critical[] = $item['subcomponent_label'] . ': ' . $item['finding'];
        }
        foreach ($groups as &$group) {
            $groupStates = array_map(static fn (array $item): string => $item['state_adopted'] ?: $item['state_proposed'], $group['items']);
            $group['state'] = self::worstState($groupStates);
            $group['conclusion'] = self::groupConclusion($group);
        }
        unset($group);
        $proposed = self::worstState($states) ?: '';
        $adopted = self::state($summary['global_adopted'] ?? '') ?: $proposed;
        $justification = self::text($summary['change_justification'] ?? '', 1200);
        $confidence = $applicable === 0 ? 'Baja' : (($verified / $applicable) >= 0.8 ? 'Alta' : (($verified / $applicable) >= 0.5 ? 'Media' : 'Baja'));
        $generated = self::generatedText($groups, $proposed, $adopted, $critical, $confidence, $justification);
        return [
            'items' => $items, 'groups' => array_values($groups), 'critical_findings' => $critical,
            'state_global_proposed' => $proposed, 'state_global_adopted' => $adopted,
            'change_justification' => $justification, 'confidence' => $confidence,
            'verified_count' => $verified, 'applicable_count' => $applicable,
            'generated_text' => $generated, 'generated_at' => gmdate('Y-m-d H:i:s'),
            'actor_id' => $actorId, 'algorithm_version' => AppraisalConservationCatalog::algorithmVersion(),
            'catalog_version' => AppraisalConservationCatalog::version(),
            'sources' => AppraisalConservationCatalog::sources(),
        ];
    }

    private static function evidenceFor(string $id, array $item): array
    {
        $finding = null;
        foreach (AppraisalConservationCatalog::findingsFor($id) as $row) {
            if ((string) ($row['finding'] ?? '') === $item['finding']) { $finding = $row; break; }
        }
        $severity = (int) ($finding['severity'] ?? 0);
        $critical = (bool) ($finding['critical'] ?? false);
        $guide = (string) ($finding['state_guide'] ?? '');
        $interventionSeverity = 0;
        foreach (AppraisalConservationCatalog::interventions() as $row) {
            if ((string) ($row['level'] ?? '') === $item['intervention']) $interventionSeverity = (int) ($row['severity'] ?? 0);
        }
        return compact('severity', 'critical', 'guide', 'interventionSeverity');
    }

    private static function proposedState(array $item): string
    {
        if ($item['state_adopted'] !== '') return $item['state_adopted'];
        if ($item['applicability'] === 'no_aplica' || $item['applicability'] === 'no_verificable') return '';
        $severity = max((int) $item['severity'], (int) $item['interventionSeverity']);
        if ($item['critical'] && $severity >= 4) return '4.5';
        if ($item['critical']) return '3.5';
        return ['2', '2.5', '3', '3.5', '4', '4.5'][$severity] ?? '2';
    }

    private static function generatedText(array $groups, string $proposed, string $adopted, array $critical, string $confidence, string $justification): string
    {
        if (!$groups) return 'El estado de conservación queda pendiente de inspección por componentes.';
        $lines = [];
        foreach ($groups as $group) $lines[] = $group['conclusion'];
        $lines[] = 'Resumen del estado de conservación por grupo:';
        foreach ($groups as $group) $lines[] = $group['label'] . ' | ' . self::stateText($group['state']) . ' | ' . self::shortConclusion($group);
        $global = 'Conclusión global: el sistema propone ' . self::stateText($proposed ?: $adopted)
            . ' y se adopta ' . self::stateText($adopted ?: $proposed) . '. Nivel de verificación: ' . $confidence . '.';
        if ($critical) $global .= ' Hallazgos críticos: ' . implode('; ', array_slice($critical, 0, 4)) . '.';
        if ($justification !== '') $global .= ' Justificación del avaluador: ' . rtrim($justification, '.') . '.';
        $lines[] = $global;
        $lines[] = 'Base técnica: ' . AppraisalConservationCatalog::technicalReference() . ' Los catálogos de hallazgos, intervención y reglas de consolidación son una herramienta interna de trazabilidad y no sustituyen diagnóstico especializado.';
        return implode("\n", $lines);
    }

    private static function groupConclusion(array $group): string
    {
        $items = $group['items']; $names = array_column($items, 'subcomponent_label');
        $findings = array_values(array_filter(array_map(static fn (array $i): string => $i['finding'], $items)));
        $interventions = array_values(array_filter(array_map(static fn (array $i): string => $i['intervention'], $items)));
        $text = $group['label'] . ': se evaluaron ' . implode(', ', array_slice($names, 0, 5)) . '.';
        if ($findings) $text .= ' Hallazgos relevantes: ' . implode('; ', array_slice($findings, 0, 4)) . '.';
        if ($interventions) $text .= ' Intervención predominante registrada: nivel ' . self::dominant($interventions) . '.';
        return $text . ' Estado del grupo: ' . self::stateText($group['state']) . '.';
    }

    private static function shortConclusion(array $group): string
    {
        foreach ($group['items'] as $item) if ($item['finding'] !== '') return $item['finding'];
        return 'Sin hallazgo relevante registrado';
    }

    private static function worstState(array $states): string
    {
        $values = array_values(array_filter(array_map(static fn ($v): float => (float) $v, $states), static fn (float $v): bool => $v > 0));
        return $values ? rtrim(rtrim(number_format(max($values), 1, '.', ''), '0'), '.') : '';
    }

    private static function dominant(array $values): string
    {
        $counts = array_count_values($values); arsort($counts);
        return (string) array_key_first($counts);
    }

    private static function stateText(string $state): string { return $state !== '' ? AppraisalConservationCatalog::stateLabel($state) : 'pendiente'; }
    private static function hasContent(array $item): bool
    {
        foreach (['material','finding','functionality','intervention','evidence','state_adopted','notes'] as $key) {
            if ($item[$key] !== '') return true;
        }
        return $item['applicability'] !== 'aplica';
    }
    private static function select(mixed $value): string { return mb_substr(trim((string) $value), 0, 60); }
    private static function state(mixed $value): string
    {
        $value = trim((string) $value);
        return in_array($value, array_column(AppraisalConservationCatalog::states(), 'value'), true) ? $value : '';
    }
    private static function text(mixed $value, int $limit): string { return mb_substr(trim((string) $value), 0, $limit); }
}
