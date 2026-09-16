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

    public static function helps(): array
    {
        return [
            'department_id' => 'Entidad territorial donde se ubica el sujeto. Debe ser coherente con municipio, matrícula y soporte registral.',
            'city_id' => 'Municipio o distrito del sujeto. En inmuebles urbanos es base para cruces de mercado, catastro, POT y comparables.',
            'neighborhood_id' => 'Unidad territorial inmediata del sujeto. Es clave para leer centralidad, entorno, norma, comparables y percepción de mercado.',
            'locality_name' => 'Se autocompleta desde el barrio cuando aplica. Sirve para estructurar lectura territorial y cruces internos.',
            'commune_ucg' => 'Subdivisión administrativa o urbanística asociada al barrio. Facilita cruces con planeación y caracterización sectorial.',
            'zone_sector' => 'Categoría amplia del emplazamiento: residencial, comercial, mixto, industrial, turístico o periférico.',
            'point_reference' => 'Ayuda a ubicar el inmueble en campo: esquina, frente a parque, sobre vía principal, cerca a equipamientos o hitos urbanos.',
            'address' => 'Dirección real del sujeto. Si no tiene nomenclatura formal, describe la ubicación útil para visita y cotejo catastral.',
            'alternate_nomenclature' => 'Registra nomenclatura secundaria, antigua o comercial si ayuda a rastrear el inmueble en soportes o visita.',
            'centrality' => 'Mide inserción urbana, cercanía a equipamientos, servicios y nodos de actividad.',
            'immediate_environment' => 'Describe el contexto dominante que rodea el sujeto: residencial, mixto, comercial, institucional o industrial.',
            'stratum' => 'Dato de contexto urbano-comercial. No reemplaza el análisis de mercado, pero ayuda a segmentar comparables.',
            'property_registry' => 'Identificador registral del sujeto. Debe permitir cotejo con certificado de tradición y soporte jurídico.',
            'cadastral_reference' => 'Identificador catastral del predio o unidad. Sirve para contrastar ubicación, área y trazabilidad física.',
            'registry_office' => 'Oficina competente donde se lleva el folio. Es clave para validar procedencia registral del sujeto.',
            'urban_license' => 'Registra licencia o ausencia según soporte disponible. Ayuda a leer legalidad urbanística y estado de desarrollo.',
            'permitted_use' => 'Lectura sintética de compatibilidad entre uso observado o potencial y norma urbana aplicable.',
            'urban_treatment' => 'Tratamiento POT o categoría urbanística de referencia: desarrollo, consolidación, renovación o conservación.',
            'restrictions' => 'Anota servidumbres, aislamientos, restricciones de uso o limitaciones que afecten valor, uso o comercialización.',
            'legal_urban_affectations' => 'Resume afectaciones viales, rondas, zonas de reserva, protecciones u otras cargas externas relevantes.',
            'road_condition' => 'Clasifica el soporte vial inmediato: vía principal, secundaria, local, peatonal o acceso interno.',
            'access_facility' => 'Valora facilidad real de acceso, maniobra, visibilidad, seguridad y calidad del ingreso.',
            'transport_connectivity' => 'Mide cobertura de movilidad y conexión con corredores viales, transporte público y nodos de actividad.',
            'loading_unloading' => 'Indica condiciones funcionales para cargue y descargue, especialmente en usos comerciales o logísticos.',
            'current_use' => 'Uso efectivamente observado al momento del análisis. Debe diferenciarse del uso permitido y potencial.',
            'main_potential_use' => 'Uso que mejor capitaliza localización, norma y condiciones del sujeto dentro del mercado relevante.',
            'complementary_potential_uses' => 'Usos adicionales viables y coherentes con el principal. Se pueden registrar varios separados por coma.',
            'main_complementary_activity' => 'Actividad secundaria dominante observada: administrativa, comercial, servicios, logística u otra.',
            'secondary_complementary_activities' => 'Otras actividades observadas que complementan el uso principal.',
            'current_occupation' => 'Situación de uso del sujeto: propio uso, arrendado, desocupado, en adecuación, venta o mixta.',
            'water_service' => 'Condición del servicio de agua del sujeto o proyecto. Registra percepción técnica general.',
            'energy_service' => 'Condición del servicio eléctrico: continuidad, suficiencia o limitaciones observadas.',
            'gas_service' => 'Disponibilidad real del servicio de gas cuando aplique al uso del sujeto.',
            'sewer_service' => 'Condición del alcantarillado o disposición sanitaria, relevante para funcionalidad y legalidad.',
            'internet_service' => 'Disponibilidad de conectividad de datos, relevante para oficinas, comercio, logística y servicios.',
            'service_continuity' => 'Juicio integrado sobre estabilidad operativa de los servicios, más allá de su existencia formal.',
            'subject_reference_date' => 'Fecha de la información del sujeto usada para la lectura técnica y de mercado.',
            'latitude' => 'Coordenada geográfica del sujeto. Facilita georreferenciación, mapas y trazabilidad espacial.',
            'longitude' => 'Coordenada geográfica complementaria para ubicar el bien en mapa.',
            'notes' => 'Espacio libre para criterio técnico, precisiones de campo, salvedades o notas útiles para las siguientes pestañas.',
        ];
    }
}
