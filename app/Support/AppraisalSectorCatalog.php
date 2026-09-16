<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSectorCatalog
{
    public static function sections(): array
    {
        return [
            'localizacion' => ['2.1', 'Localización y delimitación', [
                ['sector_name', 'Nombre sectorial de trabajo', 'text'],
                ['influence_area', 'Área de influencia', 'textarea'],
                ['sector_boundaries', 'Delimitación y referencias', 'textarea'],
                ['sector_source', 'Fuente de información sectorial', 'text'],
            ]],
            'infraestructura' => ['2.2', 'Servicios e infraestructura', [
                ['services_status', 'Disponibilidad de servicios', 'select'],
                ['infrastructure_notes', 'Infraestructura observada', 'textarea'],
                ['road_hierarchy', 'Jerarquía vial predominante', 'select'],
                ['public_space_state', 'Estado del espacio público', 'select'],
            ]],
            'uso_norma' => ['2.3', 'Uso y norma urbana', [
                ['predominant_use', 'Uso predominante del sector', 'select'],
                ['urban_norm', 'Norma urbana aplicable', 'textarea'],
                ['urban_treatment', 'Tratamiento urbanístico', 'text'],
                ['development_level', 'Nivel de consolidación urbana', 'select'],
            ]],
            'movilidad' => ['2.4', 'Movilidad y accesibilidad', [
                ['access_roads', 'Vías de acceso y salida', 'textarea'],
                ['public_transport', 'Transporte público', 'select'],
                ['connectivity', 'Conectividad urbana', 'select'],
                ['mobility_notes', 'Observaciones de movilidad', 'textarea'],
            ]],
            'equipamientos' => ['2.5', 'Equipamientos y actividad', [
                ['nearby_facilities', 'Equipamientos cercanos', 'textarea'],
                ['commercial_activity', 'Actividad comercial y servicios', 'select'],
                ['activity_anchors', 'Hitos o anclas de actividad', 'textarea'],
                ['daily_dynamics', 'Dinámica cotidiana del sector', 'textarea'],
            ]],
            'lectura' => ['2.6', 'Lectura sociofísica', [
                ['consolidation_level', 'Consolidación física', 'select'],
                ['socioeconomic_profile', 'Perfil socioeconómico observado', 'select'],
                ['security_perception', 'Percepción de seguridad', 'select'],
                ['environmental_quality', 'Calidad ambiental', 'select'],
            ]],
            'externalidades' => ['2.7', 'Externalidades y riesgos', [
                ['positive_externalities', 'Externalidades positivas', 'textarea'],
                ['negative_externalities', 'Externalidades negativas', 'textarea'],
                ['sector_risks', 'Riesgos o alertas sectoriales', 'textarea'],
                ['mitigation_notes', 'Consideraciones de mitigación', 'textarea'],
            ]],
            'conclusion' => ['2.8', 'Soporte y conclusión sectorial', [
                ['field_sources', 'Fuentes consultadas', 'textarea'],
                ['support_notes', 'Soporte gráfico o documental', 'textarea'],
                ['sector_conclusion', 'Conclusión técnica del sector', 'textarea'],
                ['sector_report_text', 'Texto editable para el entregable', 'textarea'],
            ]],
        ];
    }

    public static function options(): array
    {
        $levels = ['alto' => 'Alto', 'medio' => 'Medio', 'bajo' => 'Bajo', 'no_verificado' => 'No verificado'];
        return [
            'services_status' => ['completa' => 'Completa', 'parcial' => 'Parcial', 'deficiente' => 'Deficiente'] + ['no_verificado' => 'No verificado'],
            'road_hierarchy' => ['arterial' => 'Arterial', 'colectora' => 'Colectora', 'local' => 'Local', 'peatonal' => 'Peatonal'] + ['no_verificado' => 'No verificado'],
            'public_space_state' => ['bueno' => 'Bueno', 'regular' => 'Regular', 'deficiente' => 'Deficiente', 'no_verificado' => 'No verificado'],
            'predominant_use' => ['residencial' => 'Residencial', 'comercial' => 'Comercial', 'mixto' => 'Mixto', 'industrial' => 'Industrial', 'institucional' => 'Institucional', 'turistico' => 'Turístico'],
            'development_level' => ['consolidado' => 'Consolidado', 'en_consolidacion' => 'En consolidación', 'renovacion' => 'Renovación', 'expansion' => 'Expansión'],
            'public_transport' => ['amplio' => 'Amplio', 'moderado' => 'Moderado', 'limitado' => 'Limitado', 'no_verificado' => 'No verificado'],
            'connectivity' => ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja', 'no_verificado' => 'No verificado'],
            'commercial_activity' => ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja', 'incipiente' => 'Incipiente'],
            'consolidation_level' => ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja', 'heterogenea' => 'Heterogénea'],
            'socioeconomic_profile' => $levels,
            'security_perception' => ['favorable' => 'Favorable', 'media' => 'Media', 'alerta' => 'Con alertas', 'no_verificado' => 'No verificado'],
            'environmental_quality' => ['favorable' => 'Favorable', 'media' => 'Media', 'afectada' => 'Afectada', 'no_verificado' => 'No verificado'],
        ];
    }

    public static function helps(): array
    {
        return [
            'sector_name' => 'Nombre técnico del barrio, microsector o zona que se usará como unidad de lectura sectorial.',
            'influence_area' => 'Delimita el entorno que incide en el valor: manzanas, corredores, hitos, usos o barreras urbanas.',
            'services_status' => 'Lee disponibilidad real y continuidad de servicios públicos y redes de soporte urbano.',
            'predominant_use' => 'Uso que domina el comportamiento del sector y orienta el universo de comparables.',
            'access_roads' => 'Describe accesos principales, salidas, jerarquía vial y restricciones de movilidad.',
            'nearby_facilities' => 'Registra dotacionales, comercio, salud, educación, parques y servicios que inciden en mercado.',
            'consolidation_level' => 'Mide homogeneidad, permanencia de usos, densidad y madurez física del sector.',
            'sector_risks' => 'Incluye alertas físicas, urbanísticas, ambientales, sociales o de mercado observadas.',
            'sector_report_text' => 'Redacción depurada que podrá pasar al capítulo sectorial del entregable.',
        ];
    }

    public static function defaults(): array
    {
        return array_fill_keys(self::keys(), '');
    }

    public static function keys(): array
    {
        return array_values(array_unique(array_merge(...array_values(array_map(
            static fn (array $section): array => array_column($section[2], 0),
            self::sections()
        )))));
    }
}
