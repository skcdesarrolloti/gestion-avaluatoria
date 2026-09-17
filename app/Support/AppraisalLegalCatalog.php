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
                'nupre', 'direccion', 'tipo_predio', 'area', 'area_privada', 'area_construida',
                'coeficiente', 'cabida_linderos']],
            'ph' => ['PH y titularidad', ['reglamento_ph', 'matricula_matriz', 'matriculas_derivadas',
                'unidad_privada', 'coeficiente_ph', 'reformas_ph', 'titular_actual',
                'documento_soporte_actual', 'valor_ultimo_acto']],
            'informe' => ['Texto para informe', ['reporte_matricula', 'reporte_escritura_propiedad',
                'reporte_cedula_catastral', 'reporte_constitucion_ph', 'reporte_titular_actual',
                'reporte_afectaciones', 'reporte_gravamenes', 'reporte_conclusion_entregable']],
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
            'nupre' => 'NUPRE', 'direccion' => 'Dirección', 'tipo_predio' => 'Tipo de predio',
            'area' => 'Área', 'area_privada' => 'Área privada', 'area_construida' => 'Área construida',
            'coeficiente' => 'Coeficiente', 'cabida_linderos' => 'Cabida y linderos',
            'reglamento_ph' => 'Reglamento PH', 'matricula_matriz' => 'Matrícula matriz',
            'matriculas_derivadas' => 'Matrículas derivadas', 'unidad_privada' => 'Unidad privada',
            'coeficiente_ph' => 'Coeficiente PH', 'reformas_ph' => 'Reformas PH',
            'titular_actual' => 'Titular actual', 'documento_soporte_actual' => 'Documento soporte actual',
            'valor_ultimo_acto' => 'Valor último acto', 'reporte_matricula' => 'Matrícula para informe',
            'reporte_escritura_propiedad' => 'Escritura / título para informe',
            'reporte_cedula_catastral' => 'Cédula catastral para informe',
            'reporte_constitucion_ph' => 'Constitución PH para informe',
            'reporte_coeficiente_propiedad' => 'Coeficiente para informe',
            'reporte_titular_actual' => 'Titularidad para informe',
            'reporte_afectaciones' => 'Afectaciones para informe',
            'reporte_gravamenes' => 'Gravámenes para informe',
            'reporte_conclusion_entregable' => 'Conclusión jurídica preliminar',
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
