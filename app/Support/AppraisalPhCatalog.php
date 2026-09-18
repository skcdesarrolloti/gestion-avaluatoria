<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPhCatalog
{
    public static function statusOptions(): array
    {
        return ['' => 'Pendiente', 'ok' => 'Verificado / adecuado', 'warn' => 'Por confirmar',
            'risk' => 'Alerta relevante', 'na' => 'No aplica'];
    }

    public static function typologies(): array
    {
        return [
            'residencial' => 'Residencial · PH habitacional',
            'oficinas' => 'Oficinas y consultorios · PH corporativa',
            'comercio' => 'Locales y comercio · PH comercial',
            'bodegas' => 'Bodegas y logística · PH industrial/logística',
            'mixto' => 'Mixto · PH de usos combinados',
        ];
    }

    public static function technicalGroups(): array
    {
        return [
            'identificacion' => ['Identificación y naturaleza', [
                'fuente_documental' => 'Fuente documental',
                'escritura_reforma' => 'Escritura / acto de referencia',
                'ciudad_municipio' => 'Ciudad / municipio',
                'direccion_referencia' => 'Dirección o referencia general',
                'tipo_propiedad_horizontal' => 'Tipo de P.H. y sometimiento',
                'regimen_especial' => 'Régimen especial y marco normativo',
                'naturaleza_conjunto' => 'Naturaleza del conjunto',
                'uso_dominante' => 'Uso dominante',
                'usos_complementarios' => 'Usos complementarios permitidos',
                'relacion_funcional_usos' => 'Relación funcional entre usos',
            ]],
            'configuracion' => ['Configuración general', [
                'etapas_copropiedad' => 'Etapas, sectores o manzanas',
                'numero_edificios' => 'Número de bloques / torres / naves',
                'numero_unidades' => 'Número de unidades privadas',
                'resumen_areas_conjunto' => 'Cuadro general de áreas',
                'desarrollos_relevantes' => 'Desenglobes, subdivisiones o ampliaciones',
                'lotes_por_etapa' => 'Lote matriz y lotes resultantes',
                'organizacion_interna' => 'Organización por manzanas, lotes o sectores',
                'ubicacion_unidad' => 'Ubicación de la unidad dentro del conjunto',
                'subdivisiones_futuras' => 'Futuras subdivisiones o integraciones',
            ]],
            'soporte' => ['Bienes comunes y soporte operativo', [
                'porteria_administracion_vigilancia' => 'Portería, administración y vigilancia',
                'cctv_control_acceso' => 'CCTV y control de acceso',
                'vias_internas' => 'Vías internas',
                'red_contra_incendios' => 'Red contra incendios',
                'equipamiento_tecnico' => 'Básculas, subestación, patios y áreas técnicas',
                'apoyo_logistico_aduanero' => 'Áreas de apoyo logístico o aduanero',
                'muelles' => 'Muelles, bahías y rampas',
                'patios_maniobra' => 'Patios de maniobra',
                'circulacion_pesada' => 'Circulación de camiones y tractomulas',
                'zonas_espera' => 'Zonas de espera',
                'reglas_cargue_descargue' => 'Reglas de cargue y descargue',
                'cargue_descargue' => 'Operación de cargue, descargue y flujo logístico',
            ]],
            'reglas' => ['Condiciones normativas y cargas', [
                'usos_permitidos' => 'Usos permitidos',
                'usos_restringidos' => 'Usos restringidos o prohibidos',
                'reglas_constructivas' => 'Reglas constructivas',
                'condiciones_normativas_operativas' => 'Cerramientos, residuos, aislamientos y licencias',
                'condiciones_usuario_operador' => 'Condiciones de usuario operador o administración',
                'coeficientes_copropiedad' => 'Coeficientes de copropiedad',
                'expensas_cuotas' => 'Expensas o cuotas comunes',
                'responsabilidades_bienes_comunes' => 'Responsabilidades sobre bienes comunes',
                'cargas_comercializacion' => 'Cargas o restricciones que afecten operación o comercialización',
            ]],
            'incidencia' => ['Incidencia funcional y valuatoria', [
                'incidencia_operacion_bodegas' => 'Incidencia sobre la operación de las bodegas',
                'incidencia_valor_soporte_comun' => 'Aporte de valor del soporte común',
                'incidencia_restricciones_regimen' => 'Incidencia del régimen especial y sus restricciones',
                'incidencia_comercializacion_interna' => 'Incidencia de la organización interna en la comercialización',
                'lectura_valuatoria' => 'Incidencia funcional y valuatoria de la copropiedad',
                'salvedades_reglamento' => 'Aspectos extraídos directamente del reglamento',
                'salvedades_visita' => 'Aspectos que requieren visita',
                'salvedades_validacion' => 'Aspectos que requieren plano, certificado o validación',
                'observaciones_extraccion' => 'Salvedades de lectura y validación documental',
            ]],
        ];
    }

    public static function commonAreas(): array
    {
        return [
            'porteria' => 'Portería / acceso controlado', 'lobby' => 'Lobby o recepción',
            'ascensores' => 'Ascensores', 'circulaciones' => 'Circulaciones y escaleras',
            'parqueaderos_visitantes' => 'Parqueaderos de visitantes', 'zonas_verdes' => 'Zonas verdes',
            'salon_social' => 'Salón social', 'piscina' => 'Piscina', 'gimnasio' => 'Gimnasio',
            'juegos' => 'Juegos / recreación', 'vias_internas' => 'Vías internas',
            'planta_electrica' => 'Planta eléctrica', 'tanques' => 'Tanques / bombeo',
            'red_incendio' => 'Red contra incendio', 'basuras' => 'Cuarto de basuras',
            'cerramiento' => 'Cerramiento y seguridad perimetral',
        ];
    }

    public static function documents(): array
    {
        return [
            'reglamento' => 'Reglamento de propiedad horizontal', 'reformas' => 'Reformas al reglamento',
            'paz_salvo' => 'Paz y salvo de administración', 'recibo_administracion' => 'Recibo de administración',
            'certificado_administracion' => 'Certificado de existencia / representación',
            'presupuesto' => 'Presupuesto o cuota aprobada', 'estados_financieros' => 'Estados financieros',
            'actas' => 'Actas de asamblea relevantes', 'manual_convivencia' => 'Manual de convivencia',
            'polizas' => 'Pólizas / seguros comunes', 'planos_coeficientes' => 'Planos, áreas y coeficientes',
            'mantenimiento' => 'Soportes de mantenimiento / inspección',
            'paquete_zip' => 'Paquete ZIP/RAR de soportes PH',
        ];
    }

    public static function risks(): array
    {
        return [
            'mora_expensas' => 'Mora o paz y salvo de expensas', 'cuotas_extraordinarias' => 'Cuotas extraordinarias',
            'cartera_copropiedad' => 'Cartera elevada de la copropiedad', 'conflictos' => 'Conflictos o procesos internos',
            'deterioro_comunes' => 'Deterioro de zonas comunes', 'mantenimiento_diferido' => 'Mantenimiento diferido',
            'restricciones_uso' => 'Restricciones de uso, avisos, actividad u horario',
            'seguridad' => 'Condición de seguridad / control de acceso',
            'seguros' => 'Seguros comunes por confirmar', 'afectaciones_fisicas' => 'Afectaciones físicas comunes',
        ];
    }

    public static function photos(): array
    {
        return [
            'fachada_acceso' => 'Fachada y acceso de la copropiedad', 'porteria' => 'Portería / control',
            'circulaciones' => 'Circulaciones, escaleras y ascensores', 'parqueaderos' => 'Parqueaderos y accesos',
            'zonas_comunes' => 'Zonas comunes relevantes', 'equipos' => 'Equipos, tanques, planta o red incendio',
            'mantenimiento' => 'Estado de mantenimiento o afectaciones',
            'entorno' => 'Entorno inmediato de la copropiedad',
        ];
    }

    public static function defaults(): array
    {
        return [
            'ph_key' => '', 'ph_name' => '', 'administration_name' => '', 'administration_contact' => '',
            'ph_typology' => '', 'linkage' => [],
            'administration_phone' => '', 'administration_email' => '', 'matrix_registration' => '',
            'private_unit' => '', 'coefficient' => '', 'regulation_document' => '', 'reform_documents' => '',
            'monthly_fee' => '', 'fee_status' => '', 'reserve_fund' => '', 'insurance_status' => '',
            'restrictions_text' => '', 'common_areas' => [], 'documents' => [], 'risks' => [], 'photos' => [],
            'technical' => [], 'source_summary' => '', 'findings' => [],
            'diagnosis_text' => '', 'report_text' => '', 'updated_at' => null,
        ];
    }
}
