<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhCommonNarrative
{
    public function support(array $common, string $typology): string
    {
        $parts = []; $labels = AppraisalPhCatalog::commonAreas();
        $groups = AppraisalPhCatalog::commonAreaGroups(); $priorityList = AppraisalPhCatalog::typologyPriorities()[$typology] ?? [];
        $priority = array_values(array_filter($priorityList, fn (string $key): bool => $this->hasEvidence($common, $key)));
        if ($priority) $parts[] = 'Para la tipología ' . (AppraisalPhCatalog::typologies()[$typology] ?? 'seleccionada') . ', los elementos prioritarios identificados son ' . $this->names($priority, $labels) . '.';
        foreach ($groups as [$title, $items]) $this->appendGroup($parts, $title, $items, $common, $labels);
        return $parts ? implode(' ', $parts) : 'No se han identificado bienes comunes con soporte documental o verificación del analista para incorporar al Entregable.';
    }

    private function appendGroup(array &$parts, string $title, array $items, array $common, array $labels): void
    {
        $bucket = ['documento'=>[], 'sitio'=>[], 'analista'=>[], 'pendiente'=>[], 'riesgo'=>[]];
        foreach ($items as $key => $label) {
            if (!$this->hasEvidence($common, (string) $key)) continue;
            $status = (string) ($common[$key]['status'] ?? ''); $source = $this->source((string) ($common[$key]['notes'] ?? ''));
            if ($status === 'risk') $bucket['riesgo'][] = (string) $key;
            elseif ($status === 'ok' && ($source === 'sitio' || $source === 'ambos')) $bucket['sitio'][] = (string) $key;
            elseif ($source === 'documento') $bucket['documento'][] = (string) $key;
            elseif ($status === 'ok') $bucket['analista'][] = (string) $key;
            else $bucket['pendiente'][] = (string) $key;
        }
        $lines = [];
        if ($bucket['documento']) $lines[] = 'el reglamento o soporte documental menciona ' . $this->names($bucket['documento'], $labels);
        if ($bucket['sitio']) $lines[] = 'en sitio o fotografías se verifica ' . $this->names($bucket['sitio'], $labels);
        if ($bucket['analista']) $lines[] = 'el analista verifica ' . $this->names($bucket['analista'], $labels);
        if ($bucket['pendiente']) $lines[] = 'pendiente de verificar en sitio ' . $this->names($bucket['pendiente'], $labels);
        if ($bucket['riesgo']) $lines[] = 'con alerta por depurar ' . $this->names($bucket['riesgo'], $labels);
        if ($lines) $parts[] = $title . ': ' . implode('; ', $lines) . '.';
    }

    private function hasEvidence(array $common, string $key): bool
    { $status = (string) ($common[$key]['status'] ?? ''); return $status !== '' && $status !== 'na' && !str_starts_with(mb_strtolower((string) ($common[$key]['notes'] ?? '')), 'no identificado'); }
    private function source(string $text): string
    { $text = mb_strtolower($text); $site = preg_match('/\bvisita\b|foto|fotograf|inspecci[oó]n|\bsitio\b|\bcampo\b/u', $text) === 1; $doc = preg_match('/menci[oó]n documental|reglamento|escritura|pdf|p\.|p[aá]gina|cl[aá]usula/u', $text) === 1; return $site && $doc ? 'ambos' : ($site ? 'sitio' : ($doc ? 'documento' : 'analista')); }
    private function names(array $keys, array $labels): string
    { return implode(', ', array_slice(array_map(fn (string $key): string => mb_strtolower((string) ($labels[$key] ?? str_replace('_', ' ', $key))), $keys), 0, 9)); }
}
