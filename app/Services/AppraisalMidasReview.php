<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMidasReview
{
    public static function suggestions(array $subject): array
    {
        $profile = MidasNeighborhoodProfile::forSubject($subject);
        $wfs = MidasWfsSearch::suggestions($subject);
        if ($profile) return $wfs !== [] ? array_replace_recursive($profile, $wfs) : $profile;
        $live = MidasLiveSearch::suggestions($subject);
        $suggestions = $live !== [] ? array_replace_recursive(self::generic($subject), $live) : self::generic($subject);
        return $wfs !== [] ? array_replace_recursive($suggestions, $wfs) : $suggestions;
    }

    public static function mergeEmpty(array $current, array $suggestions): array
    {
        foreach ($suggestions as $code => $fields) {
            foreach ($fields as $field => $value) {
                $actual = $current[$code][$field] ?? '';
                if ((is_array($actual) && $actual !== []) || (!is_array($actual) && trim((string) $actual) !== '')) {
                    continue;
                }
                $current[$code][$field] = $value;
            }
        }
        return $current;
    }

    public static function rows(array $current, array $suggestions): array
    {
        $rows = [];
        foreach ($suggestions as $code => $fields) {
            foreach ($fields as $field => $value) {
                $actual = $current[$code][$field] ?? '';
                $rows[] = ['code' => $code, 'field' => $field, 'value' => $value,
                    'mode' => trim(is_array($actual) ? implode(', ', $actual) : (string) $actual) === ''
                        ? 'Aplicable' : 'Conserva manual'];
            }
        }
        foreach (self::pending($suggestions) as $code => $fields) {
            foreach ($fields as $field => $reason) {
                $actual = $current[$code][$field] ?? '';
                if ((is_array($actual) && $actual !== []) || (!is_array($actual) && trim((string) $actual) !== '')) {
                    continue;
                }
                $rows[] = ['code' => $code, 'field' => $field, 'value' => $reason, 'mode' => 'Pendiente'];
            }
        }
        return $rows;
    }

    public static function stats(array $suggestions): array
    {
        return ['sections' => count($suggestions),
            'fields' => array_sum(array_map('count', $suggestions)),
            'pending' => array_sum(array_map('count', self::pending($suggestions)))];
    }

    public static function pending(array $suggestions): array
    {
        $pending = [];
        foreach (self::automaticPlan() as $code => $fields) {
            foreach ($fields as $field => $reason) {
                if (!array_key_exists($field, $suggestions[$code] ?? [])) {
                    $pending[$code][$field] = $reason;
                }
            }
        }
        return $pending;
    }

    private static function generic(array $subject): array
    {
        $barrio = trim((string) ($subject['neighborhood_name'] ?? 'el barrio'));
        return ['05' => ['fuente_normativa' => 'MIDAS Cartagena - consulta pendiente de detalle.',
            'midas_lectura_manual' => 'Abrir MIDAS, buscar ' . ($barrio ?: 'el barrio')
                . ' y registrar tratamiento, usos, restricciones, áreas e indicadores disponibles.'],
            '14' => ['anexos_normativos' => 'Adjuntar captura o enlace de MIDAS usado como soporte.',
                'observacion_soporte_grafico' => 'La consulta automática queda preparada; requiere lectura puntual en MIDAS.']];
    }

    private static function automaticPlan(): array
    {
        return MidasLayerPlan::pendingReasons();
    }
}
