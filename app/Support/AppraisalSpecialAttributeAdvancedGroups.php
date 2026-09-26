<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeAdvancedGroups
{
    public static function lotRemainder(): array
    {
        return [
            'riesgos_fisicos_lote' => ['Riesgos físicos', 'Inundación, remoción, erosión u otras condiciones físicas.', AppraisalSpecialAttributeOptions::risk()],
            'afectaciones_lote' => ['Afectaciones', 'Retiros, servidumbres, rondas, reservas o limitaciones observables.', AppraisalSpecialAttributeOptions::restriction()],
            'potencial_normativo' => ['Potencial normativo', 'Capacidad de desarrollo según uso, edificabilidad o norma aplicable.', AppraisalSpecialAttributeOptions::potential()],
            'visibilidad_comercial_lote' => ['Visibilidad o exposición comercial', 'Exposición comercial cuando el uso o corredor lo haga relevante.', AppraisalSpecialAttributeOptions::level()],
            'mejoras_lote' => ['Mejoras o construcciones menores', 'Mejoras no predominantes que pueden requerir separación valuatoria.', AppraisalSpecialAttributeOptions::relevance()],
        ];
    }

    public static function building(): array
    {
        return [
            'uso_predominante_edificio' => ['Uso predominante', 'Actividad principal que explica el mercado del edificio.', AppraisalSpecialAttributeOptions::compatibility()],
            'mezcla_usos_edificio' => ['Mezcla de usos', 'Combinación residencial, comercial, oficinas, servicios u otros usos.', AppraisalSpecialAttributeOptions::level()],
            'area_rentable' => ['Área rentable', 'Calidad o suficiencia de la información de área generadora de renta.', AppraisalSpecialAttributeOptions::quality()],
            'ocupacion_edificio' => ['Ocupación o disponibilidad', 'Nivel observado o documentado de ocupación, vacancia o explotación.', AppraisalSpecialAttributeOptions::level()],
            'flexibilidad_edificio' => ['Flexibilidad funcional', 'Posibilidad de adaptar plantas, unidades o usos sin intervención mayor.', AppraisalSpecialAttributeOptions::flexibility()],
            'estado_fachada_edificio' => ['Estado de fachada', 'Presentación exterior, mantenimiento e imagen del edificio.', AppraisalSpecialAttributeOptions::condition()],
            'ascensores_edificio' => ['Ascensores', 'Disponibilidad, suficiencia y estado del transporte vertical.', AppraisalSpecialAttributeOptions::level()],
            'servicios_comunes_edificio' => ['Servicios comunes', 'Portería, lobby, baterías de baños, circulaciones o áreas comunes operativas.', AppraisalSpecialAttributeOptions::level()],
            'seguridad_edificio' => ['Seguridad del edificio', 'Vigilancia, control de acceso, CCTV o sistemas de seguridad.', AppraisalSpecialAttributeOptions::level()],
            'administracion_edificio' => ['Administración', 'Organización operativa, mantenimiento y soporte administrativo del edificio.', AppraisalSpecialAttributeOptions::quality()],
            'equipos_especiales' => ['Equipos especiales', 'Plantas, bombas, subestación, sistemas técnicos o equipos relevantes.', AppraisalSpecialAttributeOptions::yesPartial()],
            'parqueaderos_edificio' => ['Parqueaderos', 'Dotación y funcionalidad de parqueaderos del edificio.', AppraisalSpecialAttributeOptions::level()],
            'potencial_reconversion' => ['Potencial de reconversión', 'Posibilidad de cambio de uso o redistribución frente al mercado.', AppraisalSpecialAttributeOptions::potential()],
        ];
    }

    public static function rural(): array
    {
        return [
            'disponibilidad_agua' => ['Disponibilidad de agua', 'Fuente, suficiencia o acceso a agua para vivienda, recreación o producción.', AppraisalSpecialAttributeOptions::level()],
            'productividad_suelo' => ['Productividad o vocación del suelo', 'Aptitud agropecuaria, recreativa, suburbana o de conservación.', AppraisalSpecialAttributeOptions::level()],
            'cultivos_mejoras' => ['Cultivos o mejoras productivas', 'Cultivos, cercas, corrales, beneficiaderos u otras mejoras rurales.', AppraisalSpecialAttributeOptions::relevance()],
            'casa_principal_finca' => ['Casa principal', 'Existencia y estado de la vivienda o construcción principal.', AppraisalSpecialAttributeOptions::condition()],
            'anexos_productivos' => ['Anexos productivos', 'Galpones, establos, kioscos, piscinas, tanques u otros anexos.', AppraisalSpecialAttributeOptions::relevance()],
            'acceso_rural' => ['Acceso rural', 'Tipo, estado y restricciones de acceso al predio.', AppraisalSpecialAttributeOptions::level()],
            'entorno_paisaje' => ['Entorno o paisaje', 'Atractivo visual, privacidad, ruido o condiciones del entorno.', AppraisalSpecialAttributeOptions::quality()],
            'parcelacion_condominio' => ['Parcelación o condominio', 'Servicios comunes, administración o reglas de conjunto campestre.', AppraisalSpecialAttributeOptions::yesPartial()],
        ];
    }

    public static function hotel(): array
    {
        return [
            'recepcion_hotel' => ['Recepción', 'Área de ingreso, atención y control de huéspedes.', AppraisalSpecialAttributeOptions::quality()],
            'habitaciones_hotel' => ['Habitaciones', 'Cantidad, estado y estándar de habitaciones frente al mercado.', AppraisalSpecialAttributeOptions::level()],
            'banos_hotel' => ['Baños', 'Dotación y estado de baños privados o comunes.', AppraisalSpecialAttributeOptions::condition()],
            'cocina_restaurante' => ['Cocina o restaurante', 'Soporte de alimentos, restaurante, bar o cocina operativa.', AppraisalSpecialAttributeOptions::yesPartial()],
            'zonas_comunes_hotel' => ['Zonas comunes', 'Lobby, salones, terrazas, piscina u otras áreas de huéspedes.', AppraisalSpecialAttributeOptions::level()],
            'lavanderia_equipos' => ['Lavandería y equipos', 'Equipos de operación, lavandería, aire, bombeo o soporte técnico.', AppraisalSpecialAttributeOptions::yesPartial()],
            'planta_electrica_hotel' => ['Planta eléctrica', 'Existencia y alcance para operación, habitaciones o zonas comunes.', AppraisalSpecialAttributeOptions::yesPartial()],
            'seguridad_hotel' => ['Seguridad', 'Control de acceso, vigilancia, cámaras o protocolos de huéspedes.', AppraisalSpecialAttributeOptions::level()],
            'ocupacion_operacion' => ['Operación u ocupación', 'Evidencia de operación, escala, ocupación o estado operativo.', AppraisalSpecialAttributeOptions::level()],
            'ubicacion_turistica' => ['Ubicación turística o comercial', 'Relación con demanda turística, corporativa o de servicios.', AppraisalSpecialAttributeOptions::level()],
            'parqueaderos_hotel' => ['Parqueaderos', 'Dotación para huéspedes, visitantes o operación.', AppraisalSpecialAttributeOptions::level()],
        ];
    }

    public static function parking(): array
    {
        return [
            'facilidad_maniobra' => ['Facilidad de maniobra', 'Acceso, giro y uso cómodo del cupo.', AppraisalSpecialAttributeOptions::level()],
            'cobertura_parqueadero' => ['Cobertura', 'Condición cubierta o descubierta.', ['' => 'No verificado', 'descubierto' => 'Descubierto', 'cubierto' => 'Cubierto']],
            'relacion_juridica_parqueadero' => ['Relación jurídica', 'Matrícula independiente, uso exclusivo, asignado o comunal.', ['' => 'No verificado', 'matricula' => 'Matrícula independiente', 'uso_exclusivo' => 'Uso exclusivo', 'asignado' => 'Asignado', 'comunal' => 'Comunal']],
            'ubicacion_interna_parqueadero' => ['Ubicación interna', 'Cercanía a acceso, ascensor, rampa o circulación principal.', AppraisalSpecialAttributeOptions::level()],
            'seguridad_parqueadero' => ['Seguridad', 'Control, vigilancia, iluminación o cerramiento del estacionamiento.', AppraisalSpecialAttributeOptions::level()],
            'demanda_sector_parqueadero' => ['Demanda del sector', 'Presión de demanda o escasez de parqueaderos comparables.', AppraisalSpecialAttributeOptions::level()],
        ];
    }
}
