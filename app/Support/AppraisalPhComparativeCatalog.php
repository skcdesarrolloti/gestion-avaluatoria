<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPhComparativeCatalog
{
    public static function commonAreaGroups(): array
    {
        return [
            'esenciales' => ['Bienes comunes esenciales', [
                'terreno_estructura' => 'Terreno, cimentación y estructura',
                'fachadas_cubiertas' => 'Fachadas, cubiertas y envolvente común',
                'circulaciones_esenciales' => 'Circulaciones indispensables',
                'redes_servicios' => 'Redes generales de servicios públicos',
                'equipos_seguridad_vida' => 'Equipos de seguridad, evacuación o incendio',
            ]],
            'no_esenciales' => ['Bienes comunes no esenciales y amenidades', [
                'lobby' => 'Lobby o recepción',
                'salon_social' => 'Salón social',
                'piscina' => 'Piscina',
                'gimnasio' => 'Gimnasio',
                'zonas_humedas' => 'Zonas húmedas',
                'coworking_salas' => 'Coworking o salas comunes',
                'juegos' => 'Juegos / recreación',
                'zonas_verdes' => 'Zonas verdes',
            ]],
            'uso_exclusivo' => ['Bienes comunes de uso exclusivo', [
                'parqueaderos_privados' => 'Parqueaderos privados o asignados',
                'depositos' => 'Depósitos',
                'terrazas_patios_exclusivos' => 'Terrazas o patios de uso exclusivo',
                'zonas_asignadas_sector' => 'Zonas asignadas a una unidad o sector',
            ]],
            'soporte_operativo' => ['Soporte operativo y técnico común', [
                'porteria' => 'Portería / acceso controlado',
                'cctv_control' => 'CCTV y control de acceso',
                'parqueaderos_visitantes' => 'Parqueaderos de visitantes',
                'ascensores' => 'Ascensores',
                'vias_internas' => 'Vías internas',
                'patios_maniobra' => 'Patios de maniobra',
                'muelles_bahias' => 'Muelles, bahías y rampas',
                'planta_electrica' => 'Planta eléctrica',
                'tanques_bombeo' => 'Tanques / bombeo',
                'subestacion' => 'Subestación o cuarto técnico',
                'basuras_residuos' => 'Cuarto de basuras o manejo de residuos',
                'cerramiento' => 'Cerramiento y seguridad perimetral',
            ]],
        ];
    }

    public static function commonAreas(): array
    {
        $items = [];
        foreach (self::commonAreaGroups() as $group) $items += $group[1];
        return $items;
    }

    public static function typologyPriorities(): array
    {
        return [
            'residencial' => ['lobby', 'salon_social', 'piscina', 'gimnasio', 'zonas_humedas',
                'juegos', 'zonas_verdes', 'parqueaderos_visitantes', 'ascensores', 'cctv_control'],
            'oficinas' => ['lobby', 'ascensores', 'parqueaderos_visitantes', 'cctv_control',
                'planta_electrica', 'tanques_bombeo', 'subestacion', 'coworking_salas'],
            'comercio' => ['lobby', 'parqueaderos_visitantes', 'cctv_control', 'muelles_bahias',
                'basuras_residuos', 'planta_electrica', 'redes_servicios'],
            'bodegas' => ['vias_internas', 'patios_maniobra', 'muelles_bahias', 'cctv_control',
                'equipos_seguridad_vida', 'planta_electrica', 'subestacion', 'cerramiento'],
            'mixto' => ['lobby', 'parqueaderos_visitantes', 'vias_internas', 'cctv_control',
                'ascensores', 'basuras_residuos', 'zonas_asignadas_sector'],
        ];
    }

    public static function dotationLevels(): array
    {
        return [
            'basica' => 'Básica: soporte común mínimo para operar la copropiedad.',
            'estandar' => 'Estándar: dotación esperable para su tipología y mercado.',
            'superior' => 'Superior: varios elementos mejoran funcionalidad o deseabilidad.',
            'especializada' => 'Especializada: infraestructura técnica u operativa diferencial.',
            'sin_evidencia' => 'Sin evidencia: el documento no permite concluir.',
        ];
    }

    public static function commonAreaRules(): array
    {
        return [
            'terreno_estructura' => ['bienes comunes esenciales', 'terreno', 'cimientos', 'estructura', 'columnas', 'placas'],
            'fachadas_cubiertas' => ['fachada', 'fachadas', 'cubierta', 'cubiertas', 'tejado'],
            'circulaciones_esenciales' => ['escalera', 'escaleras', 'pasillos', 'circulaciones'],
            'redes_servicios' => ['redes generales', 'servicios publicos', 'acueducto', 'alcantarillado', 'instalaciones generales'],
            'equipos_seguridad_vida' => ['red contra incendio', 'hidrante', 'evacuacion', 'gabinete'],
            'lobby' => ['lobby', 'recepcion', 'vestibulo'],
            'salon_social' => ['salon social', 'sala comunal'],
            'piscina' => ['piscina'],
            'gimnasio' => ['gimnasio'],
            'zonas_humedas' => ['sauna', 'turco', 'zona humeda'],
            'coworking_salas' => ['coworking', 'sala de reuniones', 'salas comunes'],
            'juegos' => ['juegos infantiles', 'recreacion'],
            'zonas_verdes' => ['zonas verdes', 'jardines'],
            'parqueaderos_privados' => ['parqueadero privado', 'garaje', 'estacionamiento privado'],
            'depositos' => ['deposito', 'cuarto util'],
            'terrazas_patios_exclusivos' => ['uso exclusivo', 'terraza', 'patio exclusivo'],
            'zonas_asignadas_sector' => ['asignado al sector', 'uso exclusivo del sector'],
            'porteria' => ['porteria', 'portería', 'vigilancia'],
            'cctv_control' => ['cctv', 'control de acceso', 'camaras'],
            'parqueaderos_visitantes' => ['visitantes', 'parqueaderos de visitantes'],
            'ascensores' => ['ascensor', 'ascensores'],
            'vias_internas' => ['vias internas', 'vías internas', 'circulacion vehicular'],
            'patios_maniobra' => ['patio de maniobra', 'maniobra', 'radio de giro'],
            'muelles_bahias' => ['muelle', 'bahia', 'bahía', 'rampa'],
            'planta_electrica' => ['planta electrica', 'planta eléctrica'],
            'tanques_bombeo' => ['tanques', 'bombeo'],
            'subestacion' => ['subestacion', 'subestación', 'cuarto tecnico'],
            'basuras_residuos' => ['basuras', 'residuos'],
            'cerramiento' => ['cerramiento', 'perimetral'],
        ];
    }

    public static function normNotes(): array
    {
        return [
            'Bienes comunes esenciales y no esenciales: Ley 675 de 2001, arts. 3, 19, 20 y 24.',
            'Coeficientes, módulos y expensas comunes: Ley 675 de 2001, arts. 25 a 31.',
            'Incidencia en avalúos de inmuebles PH: Decreto 1420 de 1998, arts. 21 y 22.',
            'Trazabilidad, suficiencia y salvedades del informe: Resolución IGAC 941 de 2026 y anexo técnico.',
            'Juicio profesional, datos, insumos y limitaciones: IVS 2025, IVS 104, IVS 105, IVS 106 e IVS 400.',
        ];
    }
}
