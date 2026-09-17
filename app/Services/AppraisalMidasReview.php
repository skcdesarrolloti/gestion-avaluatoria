<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMidasReview
{
    public static function suggestions(array $subject): array
    {
        $profile = MidasNeighborhoodProfile::forSubject($subject);
        if ($profile) return $profile;
        $live = MidasLiveSearch::suggestions($subject);
        return $live !== [] ? array_replace_recursive(self::generic($subject), $live) : self::generic($subject);
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
        return [
            '01' => ['localidad' => 'MIDAS no devolvió localidad para este barrio.',
                'comuna' => 'MIDAS no devolvió UCG/comuna para este barrio.',
                'fuente_base_delimitacion' => 'Falta fuente territorial MIDAS del barrio.',
                'area_hectareas' => 'MIDAS no devolvió área del polígono barrial.',
                'perimetro_metros' => 'MIDAS no devolvió perímetro del polígono barrial.'],
            '03' => ['fuente_servicios' => 'Falta lectura de capas MIDAS de servicios públicos.',
                'acueducto_detalle' => 'Pendiente validar prestador/cobertura de acueducto.',
                'alcantarillado_detalle' => 'Pendiente validar prestador/cobertura de alcantarillado.',
                'energia_detalle' => 'Pendiente validar prestador/cobertura de energía.',
                'gas_detalle' => 'Pendiente validar cobertura de gas.',
                'aseo_detalle' => 'Pendiente empresa, frecuencia o microrruta de aseo.'],
            '06' => ['vias_detalle' => 'Pendiente lectura de capas de vías y señalización.'],
            '07' => ['equipamientos_seleccionados' => 'Pendiente activar capas de equipamiento urbano.',
                'comentario_amoblamiento' => 'Pendiente lectura de amoblamiento o equipamientos visibles.'],
            '11' => ['detalle_rutas_transporte' => 'Pendiente lectura de rutas de transporte.',
                'detalle_paraderos_transporte' => 'Pendiente lectura de paraderos asociados al barrio.'],
            '12' => ['edificaciones_ancla' => 'Pendiente identificar hitos o edificaciones relevantes.'],
            '13' => ['observacion_externalidades' => 'Pendiente lectura de externalidades, riesgos o ambiente.'],
        ];
    }
}
