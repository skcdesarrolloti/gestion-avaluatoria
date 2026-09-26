<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeGroups
{
    public static function all(): array
    {
        return [
            'comun' => ['Base común', self::common()],
            'vivienda' => ['Vivienda, apartamento o casa', self::housing()],
            'local_comercial' => ['Local comercial', self::commercial()],
            'oficina_consultorio' => ['Oficina', self::office()],
            'consultorio_salud' => ['Consultorio', self::consultingRoom()],
            'bodega_industrial' => ['Bodega o industrial', self::warehouse()],
            'lote' => ['Lote', self::lot()],
            'edificio_integral' => ['Edificio', self::building()],
            'finca_rural' => ['Finca o predio rural', self::rural()],
            'hotel_hospedaje' => ['Hotel u hospedaje', self::hotel()],
            'parqueadero' => ['Parqueadero', self::parking()],
        ];
    }

    private static function common(): array
    {
        return [
            'ubicacion_especial' => ['Ubicación especial', 'Localización o exposición que diferencia la unidad frente al mercado.', AppraisalSpecialAttributeOptions::location()],
            'acceso' => ['Acceso', 'Facilidad real de ingreso peatonal, vehicular u operativo.', AppraisalSpecialAttributeOptions::level()],
            'estado_conservacion' => ['Estado de conservación', 'Condición física observable y mantenimiento general.', AppraisalSpecialAttributeOptions::condition()],
            'mejoras_relevantes' => ['Mejoras relevantes', 'Adecuaciones u obras que agregan funcionalidad o valor.', AppraisalSpecialAttributeOptions::relevance()],
            'riesgos_afectaciones_fisicas' => ['Riesgos o afectaciones físicas', 'Humedad, inundación, remoción, deterioros o restricciones físicas observables.', AppraisalSpecialAttributeOptions::risk()],
            'evidencia_fotografica' => ['Evidencia fotográfica', 'Atributo de soporte: indica si este diferencial debe documentarse con fotografía en 3.7.', AppraisalSpecialAttributeOptions::evidenceNeed()],
            'impacto_valuatorio' => ['Impacto valuatorio', 'Lectura técnica del efecto esperado en valor.', AppraisalSpecialAttributeOptions::marketImpact()],
            'otro_atributo_especial' => ['Otro diferencial', 'Campo de apoyo para un atributo o demérito no previsto en el catálogo.', AppraisalSpecialAttributeOptions::other()],
        ];
    }

    private static function housing(): array
    {
        return [
            'vista_vivienda' => ['Vista', 'Interior, calle, paisajística, mar, parque u obstruida.', AppraisalSpecialAttributeOptions::view()],
            'iluminacion_ventilacion' => ['Iluminación y ventilación', 'Entrada de luz y circulación de aire en espacios principales.', AppraisalSpecialAttributeOptions::quality()],
            'privacidad' => ['Privacidad', 'Nivel de exposición frente a vecinos, vías o zonas comunes.', AppraisalSpecialAttributeOptions::privacy()],
            'balcon_terraza_patio_jardin' => ['Balcón, terraza, patio o jardín', 'Área exterior privada que pueda incidir en deseabilidad.', AppraisalSpecialAttributeOptions::amenity()],
            'acabados_interiores' => ['Acabados interiores', 'Calidad observable de pisos, carpintería, pintura y detalles.', AppraisalSpecialAttributeOptions::finish()],
            'estado_fachada_vivienda' => ['Estado de fachada', 'Presentación exterior, mantenimiento e imagen de la vivienda.', AppraisalSpecialAttributeOptions::condition()],
            'dependencias_vivienda' => ['Dependencias', 'Alcobas, zonas de servicio, depósitos interiores u otros espacios funcionales.', AppraisalSpecialAttributeOptions::level()],
            'cocina_banos_closets' => ['Cocina, baños y closets', 'Dotación y estado de elementos interiores principales.', AppraisalSpecialAttributeOptions::finish()],
            'parqueadero_deposito' => ['Parqueadero o depósito', 'Disponibilidad y funcionalidad de anexos asociados.', AppraisalSpecialAttributeOptions::annex()],
            'relacion_juridica_parqueadero_vivienda' => ['Relación jurídica del parqueadero', 'Indica si el parqueadero es privado, asignado, de uso exclusivo o común.', ['' => 'No verificado', 'privado' => 'Privado con matrícula', 'uso_exclusivo' => 'Uso exclusivo', 'asignado' => 'Asignado', 'comunal' => 'Comunal']],
            'ruido_humedad_asoleamiento' => ['Ruido, humedad o asoleamiento', 'Condiciones de confort que pueden castigar o premiar el valor.', AppraisalSpecialAttributeOptions::comfortRisk()],
            'amenidades_conjunto' => ['Amenidades del conjunto', 'Piscina, salón social, gimnasio, zonas verdes u otros comunes que inciden en mercado.', AppraisalSpecialAttributeOptions::level()],
            'planta_electrica_vivienda' => ['Planta eléctrica del conjunto', 'Existencia y cobertura total o parcial para la unidad o zonas comunes.', AppraisalSpecialAttributeOptions::yesPartial()],
        ];
    }

    private static function commercial(): array
    {
        return [
            'frente_comercial' => ['Frente comercial', 'Longitud y calidad del frente útil para exhibición o acceso.', AppraisalSpecialAttributeOptions::front()],
            'vitrina' => ['Vitrina', 'Capacidad de exhibición hacia zona de clientes.', AppraisalSpecialAttributeOptions::level()],
            'visibilidad_peatonal' => ['Visibilidad peatonal', 'Exposición frente al flujo de peatones.', AppraisalSpecialAttributeOptions::level()],
            'visibilidad_vehicular' => ['Visibilidad vehicular', 'Exposición desde vía o circulación vehicular.', AppraisalSpecialAttributeOptions::level()],
            'flujo_personas' => ['Flujo de personas', 'Intensidad observable de potenciales clientes.', AppraisalSpecialAttributeOptions::level()],
            'esquinero_medianero' => ['Esquinero o medianero', 'Ubicación dentro de la manzana o corredor comercial.', AppraisalSpecialAttributeOptions::corner()],
            'altura_libre_comercial' => ['Altura libre', 'Altura funcional para operación, exhibición o adecuaciones.', AppraisalSpecialAttributeOptions::height()],
            'banos_local' => ['Baño privado o común', 'Disponibilidad y suficiencia de baño para clientes, empleados o zonas comunes.', AppraisalSpecialAttributeOptions::yesPartial()],
            'facilidad_parqueo' => ['Facilidad de parqueo', 'Disponibilidad cercana para clientes o usuarios.', AppraisalSpecialAttributeOptions::level()],
            'bahia_cargue_descargue' => ['Bahía de cargue/descargue', 'Facilidad operativa para abastecimiento.', AppraisalSpecialAttributeOptions::yesPartial()],
            'compatibilidad_uso' => ['Compatibilidad de uso', 'Coherencia entre uso actual, norma y dinámica comercial.', AppraisalSpecialAttributeOptions::compatibility()],
            'restricciones_aviso_horario_actividad' => ['Restricciones de aviso, horario o actividad', 'Limitaciones que puedan afectar explotación comercial.', AppraisalSpecialAttributeOptions::restriction()],
            'anclas_comerciales' => ['Cercanía a anclas comerciales', 'Proximidad a marcas, equipamientos o flujos que atraen demanda.', AppraisalSpecialAttributeOptions::level()],
        ];
    }

    private static function office(): array
    {
        return [
            'imagen_corporativa' => ['Imagen corporativa del edificio', 'Presentación y percepción profesional del inmueble.', AppraisalSpecialAttributeOptions::quality()],
            'piso_altura' => ['Piso o altura', 'Nivel dentro del edificio y efecto funcional o comercial.', AppraisalSpecialAttributeOptions::floor()],
            'vista_oficina' => ['Vista', 'Calidad visual desde áreas de trabajo o atención.', AppraisalSpecialAttributeOptions::view()],
            'iluminacion_natural_oficina' => ['Iluminación natural', 'Entrada de luz natural en áreas laborales.', AppraisalSpecialAttributeOptions::quality()],
            'modularidad' => ['Modularidad', 'Facilidad para adaptar puestos, salas o consultorios.', AppraisalSpecialAttributeOptions::flexibility()],
            'divisiones_internas' => ['Divisiones internas', 'Distribución construida y posibilidad de ajuste.', AppraisalSpecialAttributeOptions::division()],
            'cableado_redes' => ['Cableado / redes', 'Soporte para datos, energía regulada o comunicaciones.', AppraisalSpecialAttributeOptions::quality()],
            'aire_acondicionado' => ['Aire acondicionado', 'Disponibilidad y condición de climatización.', AppraisalSpecialAttributeOptions::yesPartial()],
            'ascensores' => ['Ascensores', 'Disponibilidad y suficiencia de transporte vertical.', AppraisalSpecialAttributeOptions::level()],
            'recepcion' => ['Recepción', 'Control o atención de ingreso al edificio o unidad.', AppraisalSpecialAttributeOptions::yesPartial()],
            'banos_oficina' => ['Baños comunes o privados', 'Disponibilidad y suficiencia de baños propios o comunes para usuarios.', AppraisalSpecialAttributeOptions::yesPartial()],
            'parqueaderos_oficina' => ['Parqueaderos', 'Disponibilidad para usuarios, visitantes o propietarios.', AppraisalSpecialAttributeOptions::level()],
            'seguridad_control_acceso' => ['Seguridad y control de acceso', 'Vigilancia, portería, tarjetas o filtros de ingreso.', AppraisalSpecialAttributeOptions::level()],
            'planta_electrica_oficina' => ['Planta eléctrica', 'Existencia y alcance para zonas comunes, ascensores o unidad privada.', AppraisalSpecialAttributeOptions::yesPartial()],
            'salas_servicios_comunes_oficina' => ['Salas o servicios comunes', 'Salas de juntas, baños, cafetería, coworking u otros servicios compartidos.', AppraisalSpecialAttributeOptions::level()],
            'servicios_empresariales' => ['Cercanía a servicios empresariales', 'Entorno de bancos, notarías, comercio, transporte o apoyo profesional.', AppraisalSpecialAttributeOptions::level()],
        ];
    }

    private static function consultingRoom(): array
    {
        return [
            'sala_espera' => ['Sala de espera', 'Espacio propio o común para usuarios, pacientes o visitantes.', AppraisalSpecialAttributeOptions::yesPartial()],
            'accesibilidad_consultorio' => ['Accesibilidad', 'Facilidad de ingreso para usuarios, camilla, adultos mayores o movilidad reducida.', AppraisalSpecialAttributeOptions::level()],
            'banos_consultorio' => ['Baño privado o común', 'Disponibilidad y calidad del baño para usuarios o personal.', AppraisalSpecialAttributeOptions::yesPartial()],
            'acabados_sanitarios' => ['Acabados sanitarios', 'Acabados lavables, limpios o adecuados para actividad de consulta.', AppraisalSpecialAttributeOptions::finish()],
            'edificio_medico_corporativo' => ['Edificio médico o corporativo', 'Coherencia del edificio con servicios profesionales o de salud.', AppraisalSpecialAttributeOptions::compatibility()],
            'normas_internas_consultorio' => ['Normas internas del edificio', 'Reglas que habilitan, limitan o condicionan el uso de consultorio.', AppraisalSpecialAttributeOptions::restriction()],
        ];
    }

    private static function warehouse(): array
    {
        return [
            'altura_libre_industrial' => ['Altura libre', 'Altura útil para almacenamiento, estantería u operación.', AppraisalSpecialAttributeOptions::height()],
            'resistencia_piso' => ['Resistencia de piso', 'Capacidad aparente del piso para carga o uso industrial.', AppraisalSpecialAttributeOptions::floorStrength()],
            'muelles' => ['Muelles', 'Disponibilidad de muelles para cargue o descargue.', AppraisalSpecialAttributeOptions::level()],
            'bahias' => ['Bahías', 'Áreas de cargue, espera o operación vehicular.', AppraisalSpecialAttributeOptions::level()],
            'patio_maniobra' => ['Patio de maniobra', 'Espacio funcional para circulación interna.', AppraisalSpecialAttributeOptions::level()],
            'acceso_tractomulas' => ['Acceso tractomulas', 'Capacidad de ingreso de vehículos pesados.', AppraisalSpecialAttributeOptions::yesPartial()],
            'ancho_via' => ['Ancho de vía', 'Condición vial para logística y maniobra.', AppraisalSpecialAttributeOptions::level()],
            'puertas_cargue' => ['Puertas de cargue', 'Cantidad y funcionalidad de accesos operativos.', AppraisalSpecialAttributeOptions::level()],
            'energia_subestacion' => ['Energía eléctrica / subestación', 'Capacidad eléctrica disponible o instalada.', AppraisalSpecialAttributeOptions::level()],
            'red_contra_incendio' => ['Red contra incendio', 'Sistema de protección contra incendio observable/documentado.', AppraisalSpecialAttributeOptions::yesPartial()],
            'ventilacion_industrial' => ['Ventilación', 'Ventilación natural o mecánica para operación.', AppraisalSpecialAttributeOptions::quality()],
            'cubierta' => ['Cubierta', 'Estado y funcionalidad de la cubierta.', AppraisalSpecialAttributeOptions::condition()],
            'mezanine_oficinas' => ['Mezanine u oficinas internas', 'Áreas complementarias para administración u operación.', AppraisalSpecialAttributeOptions::yesPartial()],
            'cerramiento_industrial' => ['Cerramiento', 'Control perimetral y seguridad física.', AppraisalSpecialAttributeOptions::condition()],
            'seguridad_industrial' => ['Seguridad', 'Controles de acceso, vigilancia o sistemas de seguridad.', AppraisalSpecialAttributeOptions::level()],
            'compatibilidad_logistica' => ['Compatibilidad logística', 'Ajuste entre inmueble, vías, operación y uso previsto.', AppraisalSpecialAttributeOptions::compatibility()],
        ];
    }

    private static function lot(): array
    {
        return [
            'frente_lote' => ['Frente', 'Longitud y exposición del frente.', AppraisalSpecialAttributeOptions::front()],
            'fondo_lote' => ['Fondo', 'Profundidad y relación frente-fondo.', AppraisalSpecialAttributeOptions::front()],
            'forma_lote' => ['Forma', 'Regularidad y aprovechamiento geométrico.', AppraisalSpecialAttributeOptions::shape()],
            'topografia' => ['Topografía', 'Pendiente y condición física del terreno.', AppraisalSpecialAttributeOptions::topography()],
            'acceso_lote' => ['Acceso', 'Ingreso físico y conectividad inmediata.', AppraisalSpecialAttributeOptions::level()],
            'cerramiento_lote' => ['Cerramiento', 'Cierre físico del predio.', AppraisalSpecialAttributeOptions::yesPartial()],
            'servicios_lote' => ['Servicios', 'Disponibilidad de servicios públicos o acometidas.', AppraisalSpecialAttributeOptions::services()],
            'urbanismo_disponible' => ['Urbanismo disponible', 'Vías, andenes, redes o urbanismo construido.', AppraisalSpecialAttributeOptions::level()],
            ...AppraisalSpecialAttributeAdvancedGroups::lotRemainder(),
        ];
    }

    private static function building(): array
    {
        return AppraisalSpecialAttributeAdvancedGroups::building();
    }

    private static function rural(): array
    {
        return AppraisalSpecialAttributeAdvancedGroups::rural();
    }

    private static function hotel(): array
    {
        return AppraisalSpecialAttributeAdvancedGroups::hotel();
    }

    private static function parking(): array
    {
        return AppraisalSpecialAttributeAdvancedGroups::parking();
    }
}
