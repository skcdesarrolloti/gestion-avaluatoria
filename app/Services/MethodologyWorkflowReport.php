<?php
declare(strict_types=1);
namespace App\Services;
final class MethodologyWorkflowReport
{
    public static function text(array $record, array $units): string
    {
        $saved = MethodologyWorkflow::saved($record);
        $lines = [];
        foreach (MethodologyValuationPlan::working(MethodologyWorkflow::components($record, $units)) as $key => $component) {
            $item = $saved[$key] ?? [];
            if (($item['method'] ?? '') === '') continue;
            if ($item['method'] !== 'mercado') unset($item['analysis'], $item['conclusion']);
            $lines[] = $component['label'] . (isset($component['comparison_key']) ? ' (estimación alternativa; no sumable)' : '') . ': ' . MethodologyWorkflow::METHODS[$item['method']] . '. '
                . ($item['reason'] ?? '') . "\nAlcance: " . (($item['coverage'] ?? '') ?: 'Pendiente de documentar.')
                . "\nAnálisis: " . (($item['analysis'] ?? '') ?: 'Pendiente.')
                . "\nConclusión: " . (($item['conclusion'] ?? '') ?: 'Pendiente; no se ha adoptado un valor.');
        }
        return $lines === [] ? 'La selección metodológica por componente está pendiente de documentar por el perito.'
            : implode("\n\n", $lines);
    }
}
