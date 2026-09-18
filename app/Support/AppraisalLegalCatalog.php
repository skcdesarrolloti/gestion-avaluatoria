<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalLegalCatalog
{
    public static function groups(): array
    {
        return [
            'registral' => ['Identificación registral', ['matricula_inmobiliaria', 'circulo_registral',
                'orip', 'municipio', 'departamento', 'vereda', 'estado_folio', 'fecha_apertura',
                'fecha_expedicion', 'turno', 'pin']],
            'catastro' => ['Catastro y físico', ['codigo_catastral_actual', 'codigo_catastral_anterior',
                'nupre', 'observacion_catastral', 'direccion', 'tipo_predio', 'area', 'area_privada', 'area_construida',
                'coeficiente', 'cabida_linderos']],
            'ph' => ['PH y titularidad', ['reglamento_ph', 'matricula_matriz', 'matriculas_derivadas',
                'unidad_privada', 'coeficiente_ph', 'reformas_ph', 'titular_actual',
                'documento_soporte_actual', 'valor_ultimo_acto']],
            'tradicion' => ['Tradición y cargas', ['check_tradicion', 'check_gravamenes',
                'check_limitaciones_dominio', 'check_medidas_cautelares', 'check_propiedad_horizontal',
                'check_otras', 'revision_tradicion', 'revision_gravamenes', 'revision_limitaciones_dominio',
                'revision_medidas_cautelares', 'revision_propiedad_horizontal', 'revision_otras_cargas']],
            'informe' => ['Informe final', ['reporte_matricula', 'reporte_escritura_propiedad',
                'reporte_cedula_catastral', 'reporte_licencia_construccion', 'reporte_constitucion_ph',
                'reporte_coeficiente_propiedad', 'reporte_titular_actual', 'reporte_afectaciones',
                'reporte_gravamenes', 'semaforo_manual', 'clasificacion_manual', 'revision_analista',
                'salvedad_final', 'reporte_conclusion_entregable']],
            'impresion' => ['Impresión del entregable profesional', ['reporte_profesional_entregable']],
        ];
    }

    public static function labels(): array
    {
        return [
            'matricula_inmobiliaria' => 'Matrícula inmobiliaria', 'circulo_registral' => 'Círculo registral',
            'orip' => 'ORIP', 'municipio' => 'Municipio', 'departamento' => 'Departamento',
            'vereda' => 'Vereda', 'estado_folio' => 'Estado del folio', 'fecha_apertura' => 'Fecha de apertura',
            'fecha_expedicion' => 'Fecha de expedición', 'turno' => 'Turno', 'pin' => 'PIN',
            'codigo_catastral_actual' => 'Código catastral actual', 'codigo_catastral_anterior' => 'Código catastral anterior',
            'nupre' => 'NUPRE', 'observacion_catastral' => 'Observación catastral',
            'direccion' => 'Dirección', 'tipo_predio' => 'Tipo de predio',
            'area' => 'Área', 'area_privada' => 'Área privada', 'area_construida' => 'Área construida',
            'coeficiente' => 'Coeficiente', 'cabida_linderos' => 'Cabida y linderos',
            'reglamento_ph' => 'Reglamento PH', 'matricula_matriz' => 'Matrícula matriz',
            'matriculas_derivadas' => 'Matrículas derivadas', 'unidad_privada' => 'Unidad privada',
            'coeficiente_ph' => 'Coeficiente PH', 'reformas_ph' => 'Reformas PH',
            'titular_actual' => 'Titular actual', 'documento_soporte_actual' => 'Documento soporte actual',
            'valor_ultimo_acto' => 'Valor último acto', 'reporte_matricula' => 'Matrícula para informe',
            'check_tradicion' => 'Tradición revisada', 'check_gravamenes' => 'Gravámenes revisados',
            'check_limitaciones_dominio' => 'Limitaciones al dominio revisadas',
            'check_medidas_cautelares' => 'Medidas cautelares revisadas',
            'check_propiedad_horizontal' => 'Propiedad horizontal revisada', 'check_otras' => 'Otras cargas revisadas',
            'revision_tradicion' => 'Lectura de tradición', 'revision_gravamenes' => 'Lectura de gravámenes',
            'revision_limitaciones_dominio' => 'Lectura de limitaciones al dominio',
            'revision_medidas_cautelares' => 'Lectura de medidas cautelares',
            'revision_propiedad_horizontal' => 'Lectura de propiedad horizontal',
            'revision_otras_cargas' => 'Otras cargas o notas relevantes',
            'reporte_escritura_propiedad' => 'Escritura / título para informe',
            'reporte_cedula_catastral' => 'Cédula catastral para informe',
            'reporte_licencia_construccion' => 'Licencia / control urbanístico para informe',
            'reporte_constitucion_ph' => 'Constitución PH para informe',
            'reporte_coeficiente_propiedad' => 'Coeficiente para informe',
            'reporte_titular_actual' => 'Titularidad para informe',
            'reporte_afectaciones' => 'Afectaciones para informe',
            'reporte_gravamenes' => 'Gravámenes para informe',
            'semaforo_manual' => 'Nivel de atención pericial',
            'clasificacion_manual' => 'Conclusión pericial preliminar',
            'revision_analista' => 'Revisión humana / complementos del analista',
            'salvedad_final' => 'Salvedad para informe',
            'reporte_conclusion_entregable' => 'Conclusión jurídica preliminar',
            'reporte_profesional_entregable' => 'Reporte profesional integrado',
        ];
    }

    public static function fieldKeys(): array
    {
        $keys = [];
        foreach (self::groups() as $group) {
            $keys = array_merge($keys, $group[1]);
        }
        return array_values(array_unique($keys));
    }

    public static function defaults(): array
    {
        return array_fill_keys(self::fieldKeys(), '');
    }
}
