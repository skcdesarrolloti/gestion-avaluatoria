<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalFunctionalVariableCatalog
{
    public static function fieldsFor(string $type): array
    {
        return array_intersect_key(self::definitions(), array_flip(self::profile($type)));
    }

    public static function factorLabelsFor(string $type): array
    {
        return array_map(static fn (array $field): string => $field['label'], self::fieldsFor($type));
    }

    public static function profile(string $type): array
    {
        return [
            'casa' => ['functional_bedrooms_count', 'functional_bathrooms_count', 'functional_service_room_bathroom',
                'functional_parking_spaces_count', 'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'apartamento' => ['functional_bedrooms_count', 'functional_bathrooms_count', 'functional_service_room_bathroom',
                'functional_parking_spaces_count', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'finca' => ['functional_bedrooms_count', 'functional_bathrooms_count', 'functional_service_room_bathroom',
                'functional_parking_spaces_count', 'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'hotel' => ['functional_bedrooms_count', 'functional_bathrooms_count', 'functional_parking_spaces_count',
                'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'local' => ['functional_bathrooms_count', 'functional_parking_spaces_count', 'functional_loading_bays_count',
                'functional_clear_height_m', 'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'oficina' => ['functional_bathrooms_count', 'functional_parking_spaces_count',
                'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'consultorio' => ['functional_bathrooms_count', 'functional_parking_spaces_count',
                'functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'],
            'bodega' => ['functional_loading_bays_count', 'functional_clear_height_m', 'functional_office_area_m2',
                'functional_parking_spaces_count', 'functional_access_type', 'functional_finish_quality', 'functional_notes'],
            'edificio' => ['functional_parking_spaces_count', 'functional_loading_bays_count', 'functional_clear_height_m',
                'functional_office_area_m2', 'functional_access_type', 'functional_finish_quality', 'functional_notes'],
            'parqueadero' => ['functional_parking_spaces_count', 'functional_access_type', 'functional_finish_quality', 'functional_notes'],
            'lote' => ['functional_access_type', 'functional_notes'],
        ][$type] ?? ['functional_access_type', 'functional_view', 'functional_finish_quality', 'functional_notes'];
    }

    public static function definitions(): array
    {
        return [
            'functional_bedrooms_count' => ['label' => 'Habitaciones', 'kind' => 'number', 'placeholder' => 'Ej. 3',
                'help' => 'Aplica a vivienda, hospedaje o unidades habitacionales.'],
            'functional_bathrooms_count' => ['label' => 'Baños', 'kind' => 'decimal', 'placeholder' => 'Ej. 2 o 2,5', 'help' => ''],
            'functional_service_room_bathroom' => ['label' => 'Alcoba / baño de servicio', 'kind' => 'select', 'options' => self::serviceOptions(), 'help' => ''],
            'functional_parking_spaces_count' => ['label' => 'Celdas de parqueo', 'kind' => 'number', 'placeholder' => 'Ej. 1', 'help' => ''],
            'functional_loading_bays_count' => ['label' => 'Muelles / puntos de cargue', 'kind' => 'number', 'placeholder' => 'Ej. 2',
                'help' => 'Útil en bodegas, industria, locales grandes o logística.'],
            'functional_clear_height_m' => ['label' => 'Altura libre (m)', 'kind' => 'decimal', 'placeholder' => 'Ej. 4,50', 'help' => ''],
            'functional_office_area_m2' => ['label' => 'Área de oficina / apoyo (m²)', 'kind' => 'decimal', 'placeholder' => 'Ej. 25', 'help' => ''],
            'functional_access_type' => ['label' => 'Tipo de acceso', 'kind' => 'select', 'options' => self::accessOptions(), 'help' => ''],
            'functional_view' => ['label' => 'Vista del inmueble', 'kind' => 'select', 'options' => self::viewOptions(), 'help' => ''],
            'functional_finish_quality' => ['label' => 'Acabados del inmueble', 'kind' => 'select', 'options' => self::finishOptions(), 'help' => ''],
            'functional_notes' => ['label' => 'Notas de variables funcionales', 'kind' => 'textarea',
                'placeholder' => 'Aclara variables no comparables, datos pendientes o equivalencias por tipología.', 'help' => ''],
        ];
    }

    private static function serviceOptions(): array
    {
        return ['' => 'No verificado', 'no_aplica' => 'No aplica', 'alcoba' => 'Alcoba de servicio',
            'bano' => 'Baño de servicio', 'alcoba_bano' => 'Alcoba y baño de servicio'];
    }

    private static function accessOptions(): array
    {
        return ['' => 'No verificado', 'peatonal' => 'Peatonal', 'vehicular' => 'Vehicular',
            'mixto' => 'Mixto', 'cargue' => 'Cargue y descargue', 'restringido' => 'Restringido'];
    }

    private static function viewOptions(): array
    {
        return ['' => 'No verificado', 'interior' => 'Interior', 'exterior' => 'Exterior',
            'panoramica' => 'Panorámica', 'esquinera' => 'Esquinera', 'sin_vista' => 'Sin vista relevante'];
    }

    private static function finishOptions(): array
    {
        return ['' => 'No verificado', 'basico' => 'Básico / económico', 'medio' => 'Medio',
            'bueno' => 'Bueno', 'alto' => 'Alto', 'lujo' => 'Superior / lujo', 'obra_gris' => 'Obra gris'];
    }
}
