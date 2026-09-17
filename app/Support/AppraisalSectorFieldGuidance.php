<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSectorFieldGuidance
{
    public static function legend(): array
    {
        return [
            'oficial' => ['Fuente oficial', 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'Dato que puede venir de MIDAS, POT, DANE, Geoportal u otra entidad.'],
            'sugerido' => ['Sugerido', 'bg-blue-50 text-blue-800 border-blue-200',
                'Texto que el sistema propone y el analista debe revisar.'],
            'validar' => ['Validar', 'bg-amber-50 text-amber-800 border-amber-200',
                'Dato precargado o consultable que requiere campo, mapa o soporte.'],
            'manual' => ['Manual', 'bg-rose-50 text-rose-800 border-rose-200',
                'Campo que depende del criterio técnico del analista.'],
        ];
    }

    public static function field(string $name): array
    {
        $mode = self::fieldModes()[$name] ?? 'manual';
        [$label, $class, $hint] = self::legend()[$mode];
        return ['mode' => $mode, 'label' => $label, 'class' => $class, 'hint' => $hint];
    }

    public static function emptyClass(string $mode): string
    {
        return in_array($mode, ['manual', 'validar'], true)
            ? 'border-rose-200 bg-rose-50 text-rose-800'
            : 'border-slate-200 bg-slate-50 text-slate-600';
    }

    private static function fieldModes(): array
    {
        return [
            'fuente_base_delimitacion' => 'oficial', 'medicion_source' => 'oficial',
            'fuente_servicios' => 'oficial', 'norma_base' => 'oficial',
            'fuente_normativa' => 'oficial', 'midas_lectura_manual' => 'oficial',
            'cartografia_status' => 'oficial', 'fuente_estratificacion' => 'oficial',
            'detalle_paraderos_transporte' => 'oficial', 'anexos_normativos' => 'oficial',
            'observacion_localizacion' => 'sugerido', 'aguas_lluvias_detalle' => 'sugerido',
            'comentario_vias_senalizacion' => 'sugerido', 'comentario_amoblamiento' => 'sugerido',
            'comentario_estratificacion' => 'sugerido', 'comentario_legalidad' => 'sugerido',
            'comentario_topografia' => 'sugerido', 'comentario_transporte' => 'sugerido',
            'comentario_edificaciones' => 'sugerido', 'comentario_externalidades' => 'sugerido',
            'comentario_conclusion_sectorial' => 'sugerido', 'literal_a_localizacion' => 'sugerido',
            'literal_b_vecindario' => 'sugerido', 'literal_c_accesibilidad' => 'sugerido',
            'literal_d_actividad_constructora' => 'sugerido', 'literal_e_amenazas' => 'sugerido',
            'literal_g_servicios' => 'sugerido', 'literal_h_uso_suelo' => 'sugerido',
            'microsector' => 'validar', 'fuente_base_satelital' => 'validar',
            'mapa_delimitacion_url' => 'validar', 'imagen_satelital_url' => 'validar',
            'acueducto' => 'validar', 'alcantarillado' => 'validar', 'energia' => 'validar',
            'gas' => 'validar', 'internet_operadores' => 'validar', 'aseo_prestadores' => 'validar',
            'clasificacion_suelo' => 'validar', 'acto_complementario' => 'validar',
            'via_principal' => 'validar', 'corredor_actividad' => 'validar',
            'tipo_corredor_actividad' => 'validar', 'vias_detalle' => 'validar',
            'amoblamiento_seleccionado' => 'validar', 'equipamientos_seleccionados' => 'validar',
            'estrato_predominante' => 'validar', 'homogeneidad_estrato' => 'validar',
            'porcentajes_estrato' => 'validar', 'fuente_legalidad' => 'validar',
            'estado_legalidad_sector' => 'validar', 'servicio_transporte_predominante' => 'validar',
            'tipos_transporte_identificados' => 'validar', 'detalle_rutas_transporte' => 'validar',
            'categorias_edificaciones' => 'validar', 'externalidades_positivas' => 'validar',
            'externalidades_negativas' => 'validar', 'soportes_fotograficos_plan' => 'validar',
        ];
    }
}
