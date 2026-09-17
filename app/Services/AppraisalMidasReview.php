<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMidasReview
{
    public static function suggestions(array $subject): array
    {
        return MidasNeighborhoodProfile::forSubject($subject) ?? self::generic($subject);
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
        return $rows;
    }

    public static function stats(array $suggestions): array
    {
        return ['sections' => count($suggestions),
            'fields' => array_sum(array_map('count', $suggestions))];
    }

    private static function generic(array $subject): array
    {
        $barrio = trim((string) ($subject['neighborhood_name'] ?? 'el barrio'));
        return ['03' => ['fuente_servicios' => 'MIDAS Cartagena y empresas prestadoras: validar cobertura puntual.',
            'acueducto_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar con recibo, empresa o visita.',
            'alcantarillado_detalle' => 'Aguas de Cartagena S.A. E.S.P. - Acuacar. Confirmar cobertura puntual.',
            'energia_detalle' => 'Afinia - Grupo EPM. Confirmar disponibilidad y continuidad en campo.',
            'gas_detalle' => 'Surtigas S.A. E.S.P. Confirmar acometida o cobertura en el inmueble.',
            'aseo_detalle' => 'Consultar empresa, frecuencia y microrruta de aseo por barrio; validar con prestador y visita.'],
            '05' => ['fuente_normativa' => 'MIDAS Cartagena - consulta pendiente de detalle.',
            'midas_lectura_manual' => 'Abrir MIDAS, buscar ' . ($barrio ?: 'el barrio')
                . ' y registrar tratamiento, usos, restricciones, áreas e indicadores disponibles.'],
            '14' => ['anexos_normativos' => 'Adjuntar captura o enlace de MIDAS usado como soporte.',
                'observacion_soporte_grafico' => 'La consulta automática queda preparada; requiere lectura puntual en MIDAS.']];
    }

}
