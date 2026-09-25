<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeCatalog
{
    public static function __callStatic(string $name, array $arguments): array
    {
        return AppraisalSpecialAttributeOptions::$name(...$arguments);
    }

    public static function groups(string $propertyType = ''): array
    {
        $groups = self::allGroups();
        $specific = match (self::normalizedType($propertyType)) {
            'vivienda' => ['vivienda'],
            'local_comercial' => ['local_comercial'],
            'oficina_consultorio' => ['oficina_consultorio'],
            'bodega_industrial' => ['bodega_industrial'],
            'lote' => ['lote'],
            'edificio' => ['vivienda', 'local_comercial', 'oficina_consultorio'],
            'parqueadero' => ['parqueadero'],
            default => [],
        };
        return array_intersect_key($groups, array_flip(array_merge(['comun'], $specific)));
    }

    private static function normalizedType(string $propertyType): string
    {
        $text = mb_strtolower(trim($propertyType));
        $text = strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
        if ($text === '') return '';
        if (str_contains($text, 'parqueadero') || str_contains($text, 'garaje')) return 'parqueadero';
        if (str_contains($text, 'lote') || str_contains($text, 'terreno') || str_contains($text, 'finca')) return 'lote';
        if (str_contains($text, 'bodega') || str_contains($text, 'industrial') || str_contains($text, 'logistic')) return 'bodega_industrial';
        if (str_contains($text, 'oficina') || str_contains($text, 'consultorio')) return 'oficina_consultorio';
        if (str_contains($text, 'local') || str_contains($text, 'comerc')) return 'local_comercial';
        if (str_contains($text, 'edificio')) return 'edificio';
        if (str_contains($text, 'apart') || str_contains($text, 'casa') || str_contains($text, 'vivienda') || str_contains($text, 'hotel')) return 'vivienda';
        return $text;
    }

    public static function allGroups(): array
    {
        return [
            'comun' => ['Base común', [
                'ubicacion_especial' => ['Ubicación especial', 'Localización o exposición que diferencia la unidad frente al mercado.', self::location()],
                'acceso' => ['Acceso', 'Facilidad real de ingreso peatonal, vehicular u operativo.', self::level()],
                'estado_conservacion' => ['Estado de conservación', 'Condición física observable y mantenimiento general.', self::condition()],
                'mejoras_relevantes' => ['Mejoras relevantes', 'Adecuaciones u obras que agregan funcionalidad o valor.', self::relevance()],
                'riesgos_afectaciones_fisicas' => ['Riesgos o afectaciones físicas', 'Humedad, inundación, remoción, deterioros o restricciones físicas observables.', self::risk()],
                'evidencia_fotografica' => ['Evidencia fotográfica', 'Atributo de soporte: indica si este diferencial debe documentarse con fotografía en 3.7.', self::evidenceNeed()],
                'impacto_valuatorio' => ['Impacto valuatorio', 'Lectura técnica del efecto esperado en valor.', self::marketImpact()],
                'otro_atributo_especial' => ['Otro diferencial', 'Campo de apoyo para un atributo o demérito no previsto en el catálogo.', self::other()],
            ]],
            'vivienda' => ['Vivienda, apartamento o casa', [
                'vista_vivienda' => ['Vista', 'Interior, calle, paisajística, mar, parque u obstruida.', self::view()],
                'iluminacion_ventilacion' => ['Iluminación y ventilación', 'Entrada de luz y circulación de aire en espacios principales.', self::quality()],
                'privacidad' => ['Privacidad', 'Nivel de exposición frente a vecinos, vías o zonas comunes.', self::privacy()],
                'balcon_terraza_patio_jardin' => ['Balcón, terraza, patio o jardín', 'Área exterior privada que pueda incidir en deseabilidad.', self::amenity()],
                'acabados_interiores' => ['Acabados interiores', 'Calidad observable de pisos, carpintería, pintura y detalles.', self::finish()],
                'cocina_banos_closets' => ['Cocina, baños y closets', 'Dotación y estado de elementos interiores principales.', self::finish()],
                'parqueadero_deposito' => ['Parqueadero o depósito', 'Disponibilidad y funcionalidad de anexos asociados.', self::annex()],
                'ruido_humedad_asoleamiento' => ['Ruido, humedad o asoleamiento', 'Condiciones de confort que pueden castigar o premiar el valor.', self::comfortRisk()],
            ]],
            'local_comercial' => ['Local comercial', [
                'frente_comercial' => ['Frente comercial', 'Longitud y calidad del frente útil para exhibición o acceso.', self::front()],
                'vitrina' => ['Vitrina', 'Capacidad de exhibición hacia zona de clientes.', self::level()],
                'visibilidad_peatonal' => ['Visibilidad peatonal', 'Exposición frente al flujo de peatones.', self::level()],
                'visibilidad_vehicular' => ['Visibilidad vehicular', 'Exposición desde vía o circulación vehicular.', self::level()],
                'flujo_personas' => ['Flujo de personas', 'Intensidad observable de potenciales clientes.', self::level()],
                'esquinero_medianero' => ['Esquinero o medianero', 'Ubicación dentro de la manzana o corredor comercial.', self::corner()],
                'altura_libre_comercial' => ['Altura libre', 'Altura funcional para operación, exhibición o adecuaciones.', self::height()],
                'facilidad_parqueo' => ['Facilidad de parqueo', 'Disponibilidad cercana para clientes o usuarios.', self::level()],
                'bahia_cargue_descargue' => ['Bahía de cargue/descargue', 'Facilidad operativa para abastecimiento.', self::yesPartial()],
                'compatibilidad_uso' => ['Compatibilidad de uso', 'Coherencia entre uso actual, norma y dinámica comercial.', self::compatibility()],
                'restricciones_aviso_horario_actividad' => ['Restricciones de aviso, horario o actividad', 'Limitaciones que puedan afectar explotación comercial.', self::restriction()],
                'anclas_comerciales' => ['Cercanía a anclas comerciales', 'Proximidad a marcas, equipamientos o flujos que atraen demanda.', self::level()],
            ]],
            'oficina_consultorio' => ['Oficina o consultorio', [
                'imagen_corporativa' => ['Imagen corporativa del edificio', 'Presentación y percepción profesional del inmueble.', self::quality()],
                'piso_altura' => ['Piso o altura', 'Nivel dentro del edificio y efecto funcional o comercial.', self::floor()],
                'vista_oficina' => ['Vista', 'Calidad visual desde áreas de trabajo o atención.', self::view()],
                'iluminacion_natural_oficina' => ['Iluminación natural', 'Entrada de luz natural en áreas laborales.', self::quality()],
                'modularidad' => ['Modularidad', 'Facilidad para adaptar puestos, salas o consultorios.', self::flexibility()],
                'divisiones_internas' => ['Divisiones internas', 'Distribución construida y posibilidad de ajuste.', self::division()],
                'cableado_redes' => ['Cableado / redes', 'Soporte para datos, energía regulada o comunicaciones.', self::quality()],
                'aire_acondicionado' => ['Aire acondicionado', 'Disponibilidad y condición de climatización.', self::yesPartial()],
                'ascensores' => ['Ascensores', 'Disponibilidad y suficiencia de transporte vertical.', self::level()],
                'recepcion' => ['Recepción', 'Control o atención de ingreso al edificio o unidad.', self::yesPartial()],
                'parqueaderos_oficina' => ['Parqueaderos', 'Disponibilidad para usuarios, visitantes o propietarios.', self::level()],
                'seguridad_control_acceso' => ['Seguridad y control de acceso', 'Vigilancia, portería, tarjetas o filtros de ingreso.', self::level()],
                'servicios_empresariales' => ['Cercanía a servicios empresariales', 'Entorno de bancos, notarías, comercio, transporte o apoyo profesional.', self::level()],
            ]],
            'bodega_industrial' => ['Bodega o industrial', [
                'altura_libre_industrial' => ['Altura libre', 'Altura útil para almacenamiento, estantería u operación.', self::height()],
                'resistencia_piso' => ['Resistencia de piso', 'Capacidad aparente del piso para carga o uso industrial.', self::floorStrength()],
                'muelles' => ['Muelles', 'Disponibilidad de muelles para cargue o descargue.', self::level()],
                'bahias' => ['Bahías', 'Áreas de cargue, espera o operación vehicular.', self::level()],
                'patio_maniobra' => ['Patio de maniobra', 'Espacio funcional para circulación interna.', self::level()],
                'acceso_tractomulas' => ['Acceso tractomulas', 'Capacidad de ingreso de vehículos pesados.', self::yesPartial()],
                'ancho_via' => ['Ancho de vía', 'Condición vial para logística y maniobra.', self::level()],
                'puertas_cargue' => ['Puertas de cargue', 'Cantidad y funcionalidad de accesos operativos.', self::level()],
                'energia_subestacion' => ['Energía eléctrica / subestación', 'Capacidad eléctrica disponible o instalada.', self::level()],
                'red_contra_incendio' => ['Red contra incendio', 'Sistema de protección contra incendio observable/documentado.', self::yesPartial()],
                'ventilacion_industrial' => ['Ventilación', 'Ventilación natural o mecánica para operación.', self::quality()],
                'cubierta' => ['Cubierta', 'Estado y funcionalidad de la cubierta.', self::condition()],
                'mezanine_oficinas' => ['Mezanine u oficinas internas', 'Áreas complementarias para administración u operación.', self::yesPartial()],
                'cerramiento_industrial' => ['Cerramiento', 'Control perimetral y seguridad física.', self::condition()],
                'seguridad_industrial' => ['Seguridad', 'Controles de acceso, vigilancia o sistemas de seguridad.', self::level()],
                'compatibilidad_logistica' => ['Compatibilidad logística', 'Ajuste entre inmueble, vías, operación y uso previsto.', self::compatibility()],
            ]],
            'lote' => ['Lote', [
                'frente_lote' => ['Frente', 'Longitud y exposición del frente.', self::front()],
                'fondo_lote' => ['Fondo', 'Profundidad y relación frente-fondo.', self::front()],
                'forma_lote' => ['Forma', 'Regularidad y aprovechamiento geométrico.', self::shape()],
                'topografia' => ['Topografía', 'Pendiente y condición física del terreno.', self::topography()],
                'acceso_lote' => ['Acceso', 'Ingreso físico y conectividad inmediata.', self::level()],
                'cerramiento_lote' => ['Cerramiento', 'Cierre físico del predio.', self::yesPartial()],
                'servicios_lote' => ['Servicios', 'Disponibilidad de servicios públicos o acometidas.', self::services()],
                'urbanismo_disponible' => ['Urbanismo disponible', 'Vías, andenes, redes o urbanismo construido.', self::level()],
                'riesgos_fisicos_lote' => ['Riesgos físicos', 'Inundación, remoción, erosión u otras condiciones físicas.', self::risk()],
                'afectaciones_lote' => ['Afectaciones', 'Retiros, servidumbres, rondas, reservas o limitaciones observables.', self::restriction()],
                'potencial_normativo' => ['Potencial normativo', 'Capacidad de desarrollo según uso, edificabilidad o norma aplicable.', self::potential()],
                'visibilidad_comercial_lote' => ['Visibilidad o exposición comercial', 'Exposición comercial cuando el uso o corredor lo haga relevante.', self::level()],
            ]],
            'parqueadero' => ['Parqueadero', [
                'facilidad_maniobra' => ['Facilidad de maniobra', 'Acceso, giro y uso cómodo del cupo.', self::level()],
                'cobertura_parqueadero' => ['Cobertura', 'Condición cubierta o descubierta.', ['' => 'No verificado', 'descubierto' => 'Descubierto', 'cubierto' => 'Cubierto']],
            ]],
        ];
    }

    public static function flatKeys(): array
    {
        $keys = [];
        foreach (self::allGroups() as $group) $keys = array_merge($keys, array_keys($group[1]));
        return $keys;
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::allGroups() as $group) foreach ($group[1] as $key => $attribute) $labels[$key] = (string) $attribute[0];
        return $labels;
    }

    public static function selectOptions(): array
    {
        return [
            'state' => ['' => 'No verificado', 'bueno' => 'Bueno', 'regular' => 'Regular', 'malo' => 'Malo', 'no_aplica' => 'No aplica'],
            'impact' => ['' => 'No definido', 'positivo_alto' => 'Positivo alto', 'positivo_medio' => 'Positivo medio',
                'neutro' => 'Neutro', 'negativo_medio' => 'Negativo medio', 'negativo_alto' => 'Negativo alto'],
            'evidence' => ['' => 'No verificado', 'foto' => 'Foto', 'visita' => 'Visita', 'documento' => 'Documento',
                'anuncio' => 'Anuncio', 'declaracion' => 'Declaración'],
            'rating' => ['' => 'Sin calificar', '1' => '1 Muy desfavorable', '2' => '2 Desfavorable',
                '3' => '3 Normal', '4' => '4 Favorable', '5' => '5 Muy favorable'],
            'weight' => ['' => 'Sin peso', '1' => 'Bajo', '2' => 'Medio', '3' => 'Alto'],
        ];
    }

}
