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
                'bienes_comunes_esenciales' => 'Bienes comunes esenciales',
                'bienes_comunes_no_esenciales' => 'Bienes comunes no esenciales',
                'areas_uso_exclusivo' => 'Áreas comunes de uso exclusivo',
                'amenidades_relevantes' => 'Amenidades relevantes según tipología',
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
                'dotacion_tipologia' => 'Perfil de dotación frente a la tipología',
                'nivel_dotacion_comparativa' => 'Lectura comparativa de dotación',
                'comparacion_mercado_ph' => 'Comparación con copropiedades similares',
                'lectura_valuatoria' => 'Incidencia funcional y valuatoria de la copropiedad',
                'salvedades_reglamento' => 'Aspectos extraídos directamente del reglamento',
                'salvedades_visita' => 'Aspectos que requieren visita',
                'salvedades_validacion' => 'Aspectos que requieren plano, certificado o validación',
                'observaciones_extraccion' => 'Salvedades de lectura y validación documental',
                'notas_normativas_ph' => 'Notas normativas sugeridas',
            ]],
        ];
    }

    public static function technicalApplicability(): array
    {
        $base = ['fuente_documental', 'escritura_reforma', 'ciudad_municipio', 'direccion_referencia',
            'tipo_propiedad_horizontal', 'naturaleza_conjunto', 'uso_dominante', 'numero_edificios',
            'numero_unidades', 'resumen_areas_conjunto', 'ubicacion_unidad',
            'bienes_comunes_esenciales', 'bienes_comunes_no_esenciales', 'areas_uso_exclusivo',
            'porteria_administracion_vigilancia', 'cctv_control_acceso', 'usos_permitidos',
            'usos_restringidos', 'reglas_constructivas', 'coeficientes_copropiedad',
            'expensas_cuotas', 'responsabilidades_bienes_comunes', 'lectura_valuatoria',
            'salvedades_reglamento', 'salvedades_visita', 'salvedades_validacion',
            'observaciones_extraccion', 'dotacion_tipologia', 'nivel_dotacion_comparativa',
            'comparacion_mercado_ph', 'notas_normativas_ph'];
        return [
            'residencial' => array_merge($base, ['etapas_copropiedad', 'numero_edificios',
                'amenidades_relevantes', 'condiciones_normativas_operativas',
                'incidencia_valor_soporte_comun']),
            'oficinas' => array_merge($base, ['relacion_funcional_usos', 'regimen_especial',
                'amenidades_relevantes', 'equipamiento_tecnico', 'red_contra_incendios',
                'condiciones_normativas_operativas', 'cargas_comercializacion',
                'incidencia_valor_soporte_comun']),
            'comercio' => array_merge($base, ['relacion_funcional_usos', 'usos_complementarios',
                'amenidades_relevantes', 'zonas_espera', 'reglas_cargue_descargue', 'cargue_descargue',
                'condiciones_normativas_operativas', 'cargas_comercializacion',
                'incidencia_comercializacion_interna']),
            'bodegas' => array_merge($base, ['regimen_especial', 'etapas_copropiedad', 'lotes_por_etapa',
                'organizacion_interna', 'subdivisiones_futuras', 'vias_internas', 'red_contra_incendios',
                'equipamiento_tecnico', 'apoyo_logistico_aduanero', 'muelles', 'patios_maniobra',
                'circulacion_pesada', 'zonas_espera', 'reglas_cargue_descargue', 'cargue_descargue',
                'condiciones_usuario_operador', 'cargas_comercializacion', 'incidencia_operacion_bodegas',
                'incidencia_restricciones_regimen', 'incidencia_comercializacion_interna']),
            'mixto' => array_keys(array_merge(...array_values(array_map(static fn (array $group): array => $group[1],
                self::technicalGroups())))),
        ];
    }

    public static function commonAreas(): array
    {
        return AppraisalPhComparativeCatalog::commonAreas();
    }

    public static function commonAreaGroups(): array
    {
        return AppraisalPhComparativeCatalog::commonAreaGroups();
    }

    public static function typologyPriorities(): array
    {
        return AppraisalPhComparativeCatalog::typologyPriorities();
    }

    public static function dotationLevels(): array
    {
        return AppraisalPhComparativeCatalog::dotationLevels();
    }

    public static function normNotes(): array
    {
        return AppraisalPhComparativeCatalog::normNotes();
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
            'version' => 0, 'ph_key' => '', 'ph_name' => '', 'administration_name' => '', 'administration_contact' => '',
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
