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

    public static function guideFor(string $type): array
    {
        return self::guides()[$type] ?? self::guides()[''];
    }

    public static function factorGroupsFor(string $type): array
    {
        $guide = self::guideFor($type);
        return array_filter([
            'Funcionales directos (3.3)' => self::factorLabelsFor($type),
            'Superficie, norma y localización' => $guide['surface'] ?? [],
            'Atributos diferenciales' => $guide['special'] ?? [],
            'PH, copropiedad o soporte común' => $guide['ph'] ?? [],
        ]);
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

    public static function guides(): array
    {
        return [
            'casa' => self::guide('Casa',
                'Vivienda con peso combinado de terreno, construcción, anexos y estado de conservación.',
                ['Área de lote', 'Área construida', 'Frente y fondo equivalente', 'Forma/topografía', 'Norma urbana y servicios'],
                ['Patios, terrazas o anexos', 'Ubicación especial', 'Estado de fachada', 'Dependencias y acabados'],
                ['Si pertenece a conjunto: amenidades, seguridad, administración, parqueadero asignado o privado']),
            'apartamento' => self::guide('Apartamento',
                'Unidad privada en PH: comparar área privada, piso, edificio, servicios comunes y atributos propios.',
                ['Área privada/adoptada', 'Piso o nivel', 'Vetustez del edificio', 'Estrato y localización vertical'],
                ['Vista', 'Iluminación', 'Parqueaderos/deposito', 'Estado y acabados de la unidad'],
                ['Ascensor', 'Amenidades', 'Seguridad', 'Administración', 'Planta eléctrica y alcance', 'Relación jurídica del parqueadero']),
            'lote' => self::guide('Lote',
                'Predomina el suelo: no se activan variables de vivienda; la comparación sale de cabida, norma y potencial.',
                ['Área de terreno', 'Frente', 'Fondo equivalente', 'Relación frente-fondo', 'Forma', 'Topografía', 'Tratamiento urbanístico'],
                ['Servicios públicos', 'Cerramiento', 'Riesgos físicos', 'Afectaciones', 'Visibilidad comercial', 'Vía de acceso'],
                []),
            'local' => self::guide('Local',
                'Comercio o servicios: pesan exposición, frente, vitrina, acceso, flujo y soporte del edificio o centro comercial.',
                ['Área útil/adoptada', 'Frente comercial', 'Piso/nivel', 'Ubicación dentro del corredor o centro comercial'],
                ['Vitrina o exposición', 'Esquina', 'Flujo peatonal/vehicular', 'Baño privado o común', 'Cargue liviano'],
                ['Administración', 'Seguridad', 'Zonas comunes comerciales', 'Parqueaderos de visitantes']),
            'oficina' => self::guide('Oficina',
                'Unidad corporativa o profesional: no usa habitaciones; se compara por área, piso, edificio, acceso y soporte común.',
                ['Área privada o construida', 'Piso/nivel', 'Eficiencia del área', 'Vetustez y estado del edificio'],
                ['Imagen corporativa', 'Iluminación/vista', 'Recepción o lobby', 'Parqueaderos', 'Acabados de oficina'],
                ['Ascensor', 'Seguridad', 'Administración', 'Planta eléctrica', 'Baños comunes o privados', 'Salas o servicios comunes']),
            'consultorio' => self::guide('Consultorio',
                'Servicio profesional o salud: se parece a oficina, pero con mayor atención a acceso de usuarios y soporte operativo.',
                ['Área privada o construida', 'Piso/nivel', 'Localización en edificio médico o corporativo'],
                ['Sala de espera', 'Baño privado o común', 'Accesibilidad', 'Iluminación', 'Acabados sanitarios'],
                ['Ascensor', 'Seguridad', 'Recepción', 'Parqueaderos de visitantes', 'Normas internas del edificio']),
            'bodega' => self::guide('Bodega',
                'Activo logístico o industrial: altura, maniobra, muelles, piso, redes y acceso pesado dominan la comparabilidad.',
                ['Área de nave', 'Área de patio', 'Área de oficinas', 'Norma industrial/logística', 'Vía de acceso pesado'],
                ['Altura libre', 'Muelles', 'Patio de maniobra', 'Capacidad eléctrica', 'Sistema contra incendio', 'Resistencia de piso'],
                ['Control de acceso', 'Seguridad', 'Administración de parque industrial', 'Servicios comunes operativos']),
            'edificio' => self::guide('Edificio',
                'Activo integral: separar usos, áreas rentables, áreas comunes, niveles y componentes que el mercado negocia unidos.',
                ['Área total construida', 'Área rentable', 'Área de lote', 'Número de niveles', 'Unidades funcionales'],
                ['Uso predominante', 'Mezcla de usos', 'Estado general', 'Flexibilidad funcional', 'Parqueaderos'],
                ['Ascensores', 'Administración', 'Seguridad', 'Servicios comunes', 'Planta eléctrica', 'Equipos especiales']),
            'finca' => self::guide('Finca',
                'Predio rural, suburbano o recreativo: combinar suelo, acceso, aguas, productividad, vivienda y anexos.',
                ['Área de terreno', 'Topografía', 'Acceso', 'Disponibilidad de agua', 'Uso normativo rural/suburbano'],
                ['Casa principal', 'Anexos productivos', 'Cultivos o mejoras', 'Cerramientos', 'Vista/entorno'],
                ['Servicios o infraestructura común cuando exista parcelación, condominio o conjunto campestre']),
            'hotel' => self::guide('Hotel / hospedaje',
                'Unidad económica de hospedaje: comparar escala, habitaciones, operación, zonas comunes y servicios.',
                ['Área construida', 'Número de niveles', 'Localización turística/comercial', 'Área útil operativa'],
                ['Habitaciones', 'Baños', 'Recepción', 'Cocina/restaurante', 'Zonas comunes', 'Parqueaderos'],
                ['Ascensor', 'Planta eléctrica', 'Lavandería/equipos', 'Seguridad', 'Servicios operativos']),
            'parqueadero' => self::guide('Parqueadero',
                'Cupo o área de estacionamiento: precisar derecho, cubierta, maniobra, seguridad y relación con el inmueble principal.',
                ['Número de celdas', 'Área/cabida si aplica', 'Ubicación interna', 'Cubierto o descubierto'],
                ['Facilidad de maniobra', 'Seguridad', 'Acceso vehicular', 'Demanda del sector'],
                ['Matrícula independiente', 'Uso exclusivo', 'Asignado a unidad privada', 'Comunal o visitantes']),
            '' => self::guide('Tipología pendiente',
                'Primero define el tipo de inmueble para evitar pedir variables que no pertenecen al activo.',
                ['Localización', 'Área adoptada', 'Uso o destinación'], ['Atributos que expliquen diferencia real de mercado'], []),
        ];
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

    private static function guide(string $title, string $summary, array $surface, array $special, array $ph): array
    {
        return compact('title', 'summary', 'surface', 'special', 'ph');
    }
}
