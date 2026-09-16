<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSubjectCatalog
{
    public static function defaults(): array
    {
        return array_fill_keys(self::keys(), '');
    }

    public static function keys(): array
    {
        return array_merge(self::textKeys(), array_keys(self::selects()), ['subject_reference_date', 'notes']);
    }

    public static function textKeys(): array
    {
        return ['point_reference', 'address', 'alternate_nomenclature', 'property_registry',
            'cadastral_reference', 'registry_office', 'restrictions', 'legal_urban_affectations',
            'complementary_potential_uses', 'secondary_complementary_activities', 'latitude', 'longitude'];
    }

    public static function selects(): array
    {
        $verified = ['no_verificado' => 'No verificado', 'si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica'];
        return [
            'horizontal_property' => ['Propiedad horizontal', ['no' => 'NO', 'si' => 'Sí', 'no_aplica' => 'N/A']],
            'centrality' => ['Centralidad', ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja', 'no_verificada' => 'No verificada']],
            'immediate_environment' => ['Entorno inmediato', ['residencial_consolidado' => 'Residencial consolidado',
                'comercial' => 'Comercial', 'mixto' => 'Mixto', 'industrial' => 'Industrial', 'institucional' => 'Institucional']],
            'stratum' => ['Estrato', ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', 'no_aplica' => 'N/A']],
            'urban_license' => ['Licencia urbanística', ['no_reporta' => 'No reporta', 'si_reporta' => 'Sí reporta', 'no_aplica' => 'N/A']],
            'permitted_use' => ['Uso permitido / compatibilidad normativa', ['altamente_compatible' => 'Altamente compatible',
                'compatible' => 'Compatible', 'condicionado' => 'Condicionado', 'no_verificado' => 'No verificado']],
            'urban_treatment' => ['Tratamiento urbanístico base', ['mejoramiento_integral' => 'Mejoramiento integral',
                'consolidacion' => 'Consolidación', 'renovacion' => 'Renovación', 'desarrollo' => 'Desarrollo',
                'conservacion' => 'Conservación', 'no_verificado' => 'No verificado']],
            'road_condition' => ['Condición de la vía', ['via_principal' => 'Sobre vía principal',
                'via_secundaria' => 'Sobre vía secundaria', 'via_local' => 'Vía local', 'sin_acceso_directo' => 'Sin acceso directo']],
            'access_facility' => ['Facilidad de ingreso', ['buena' => 'Buena', 'media' => 'Media', 'limitada' => 'Limitada']],
            'transport_connectivity' => ['Transporte / conectividad', ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja']],
            'loading_unloading' => ['Cargue / descargue', ['no_aplica' => 'No aplica', 'posible' => 'Posible', 'restringido' => 'Restringido']],
            'current_occupation' => ['Ocupación actual', ['ocupado' => 'Ocupado', 'desocupado' => 'Desocupado', 'parcial' => 'Parcial']],
            'water_service' => ['Agua', $verified], 'energy_service' => ['Energía', $verified],
            'gas_service' => ['Gas', $verified], 'sewer_service' => ['Alcantarillado', $verified],
            'internet_service' => ['Internet / datos', $verified],
            'service_continuity' => ['Continuidad real de servicios', ['estable' => 'Estable',
                'intermitente' => 'Intermitente', 'no_verificada' => 'No verificada']],
        ];
    }
}
