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
                'group_number' => (string) ($group['number'] ?? ''),
                'subcomponent_id' => $id, 'subcomponent_label' => (string) ($sub['label'] ?? ''),
                'criticality' => (string) ($sub['criticality'] ?? ''),
                'weight' => self::weightFor((string) ($sub['criticality'] ?? '')),
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
        $approved = self::text($summary['approved_text'] ?? '', 8000);
        return [
            'legacy_json' => json_encode($flat, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'result_json' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'generated_text' => $result['generated_text'],
            'approved_text' => $approved !== '' ? $approved : $result['generated_text'],
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
        $groups = []; $critical = []; $verified = 0; $applicable = 0;
        foreach ($items as $item) {
            if ($item['applicability'] === 'no_aplica') continue;
            $applicable++;
            if ($item['applicability'] !== 'no_verificable') $verified++;
            $item += self::factorMetrics($item);
            $state = self::stateFromScore((float) $item['factor_score']);
            $gid = $item['group_id'];
            $groups[$gid] ??= [
                'id' => $gid, 'number' => $item['group_number'], 'label' => $item['group_label'],
                'weight' => self::groupWeightFor($gid), 'items' => [], 'state' => ''
            ];
            $item['state_proposed'] = $state;
            $groups[$gid]['items'][] = $item;
            if ($item['critical']) $critical[] = $item['subcomponent_label'] . ': ' . $item['finding'];
        }
        foreach ($groups as &$group) {
            $score = self::weightedScore($group['items']);
            $group['score'] = $score['score'];
            $group['weight_total'] = $score['weight_total'];
            $group['weighted_sum'] = $score['weighted_sum'];
            $group['state'] = self::stateFromScore($score['score']);
            $group['conclusion'] = self::groupConclusion($group);
        }
        unset($group);
        $globalScore = self::globalScore($groups);
        $proposed = self::stateFromScore($globalScore['score']);
        $adopted = self::state($summary['global_adopted'] ?? '') ?: $proposed;
        $justification = self::text($summary['change_justification'] ?? '', 1200);
        $confidence = $applicable === 0 ? 'Baja' : (($verified / $applicable) >= 0.8 ? 'Alta' : (($verified / $applicable) >= 0.5 ? 'Media' : 'Baja'));
        $generated = self::generatedText($groups, $proposed, $adopted, $critical, $confidence, $justification);
        return [
            'items' => $items, 'groups' => array_values($groups), 'critical_findings' => $critical,
            'state_global_proposed' => $proposed, 'state_global_adopted' => $adopted,
            'score_global' => $globalScore['score'], 'weight_total' => $globalScore['weight_total'],
            'weighted_sum' => $globalScore['weighted_sum'],
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

    private static function factorMetrics(array $item): array
    {
        if ($item['applicability'] === 'no_aplica' || $item['applicability'] === 'no_verificable') {
            return ['finding_state' => '', 'intervention_state' => '', 'functionality_floor' => '', 'factor_score' => 0.0];
        }
        $finding = self::severityScore((int) $item['severity']);
        $intervention = self::severityScore((int) $item['interventionSeverity']);
        $floor = self::functionalityFloor($item['functionality']);
        $score = max($finding, $intervention, $floor);
        if ($item['critical'] && $score < 3.5) $score = 3.5;
        if ($item['critical'] && (int) $item['severity'] >= 4) $score = max($score, 4.5);
        return [
            'finding_state' => self::stateFromScore($finding),
            'intervention_state' => self::stateFromScore($intervention),
            'functionality_floor' => $floor > 0 ? self::stateFromScore($floor) : '',
            'factor_score' => (float) self::stateFromScore($score),
        ];
    }

    private static function generatedText(array $groups, string $proposed, string $adopted, array $critical, string $confidence, string $justification): string
    {
        return AppraisalConservationReportWriter::build($groups, $proposed, $adopted, $critical, $confidence, $justification);
    }

    private static function groupConclusion(array $group): string
    {
        return AppraisalConservationReportWriter::groupConclusion($group);
    }

    private static function weightedScore(array $items): array
    {
        $sum = 0.0; $weights = 0.0;
        foreach ($items as $item) {
            $state = (float) ($item['state_adopted'] ?: $item['state_proposed'] ?: 0);
            if ($state <= 0) continue;
            $weight = (float) ($item['weight'] ?? 1);
            $sum += $state * $weight;
            $weights += $weight;
        }
        return ['score' => $weights > 0 ? $sum / $weights : 0.0, 'weighted_sum' => $sum, 'weight_total' => $weights];
    }

    private static function globalScore(array $groups): array
    {
        $sum = 0.0; $weights = 0.0;
        foreach ($groups as $group) {
            $score = (float) ($group['score'] ?? 0);
            if ($score <= 0) continue;
            $weight = (float) ($group['weight'] ?? 1);
            $sum += $score * $weight;
            $weights += $weight;
        }
        return ['score' => $weights > 0 ? $sum / $weights : 0.0, 'weighted_sum' => $sum, 'weight_total' => $weights];
    }

    private static function severityScore(int $severity): float
    {
        return [0 => 2.0, 1 => 2.5, 2 => 3.0, 3 => 3.5, 4 => 4.0, 5 => 4.5][$severity] ?? 2.0;
    }

    private static function functionalityFloor(string $functionality): float
    {
        return ['normal' => 2.0, 'observaciones' => 2.5, 'limitada' => 3.0, 'no_funcional' => 4.0][$functionality] ?? 0.0;
    }

    private static function stateFromScore(float $score): string
    {
        if ($score <= 0) return '';
        $rounded = max(1.0, min(5.0, round($score * 2) / 2));
        return rtrim(rtrim(number_format($rounded, 1, '.', ''), '0'), '.');
    }

    private static function weightFor(string $criticality): float
    {
        return ['Crítica' => 1.5, 'Alta' => 1.25, 'Media' => 1.0, 'Baja' => 0.75][$criticality] ?? 1.0;
    }

    private static function groupWeightFor(string $groupId): float
    {
        return [
            'estructura' => 2.0, 'instalaciones' => 1.5, 'envolvente' => 1.25,
            'acabados' => 1.0, 'espacios_funcionales' => 1.0, 'condiciones_ambientales' => 0.75,
        ][$groupId] ?? 1.0;
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
