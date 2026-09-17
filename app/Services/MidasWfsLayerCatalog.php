<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalSectorAdvancedCatalog;

final class MidasWfsLayerCatalog
{
    private const WFS_BASE = 'https://midas.cartagena.gov.co:2053/cgi-bin/mapserv.exe';

    public static function layers(): array
    {
        return [
            'barrios' => self::layer('Territorio', 'division_politica',
                'Division_Politica_Territorios_Barrios', 'Polygon',
                ['01' => ['barrio', 'localidad', 'comuna', 'area_hectareas',
                    'perimetro_metros', 'fuente_base_delimitacion']]),
            'pot_clasificacion' => self::layer('Clasificación del suelo', 'pot2001',
                'Clasificacion', 'Polygon', ['05' => ['clasificacion_suelo', 'midas_lectura_manual']]),
            'pot_uso_suelo' => self::layer('Uso del suelo', 'pot2001',
                'UsoDeSueloDTS', 'Polygon', ['05' => ['norma_base', 'fuente_normativa',
                    'soporte_normativo_sector'], '16' => ['literal_h_uso_suelo']]),
            'acueducto' => self::layer('Acueducto', 'sintesis_diagnostico_pot',
                'sintesis_pot_sp_area_prestacion_servicio_acueducto', 'Polygon',
                ['03' => ['fuente_servicios', 'acueducto_detalle']]),
            'alcantarillado' => self::layer('Alcantarillado', 'sintesis_diagnostico_pot',
                'sintesis_pot_sp_area_prestacion_servicio_alcantarillado', 'Polygon',
                ['03' => ['alcantarillado_detalle', 'aguas_lluvias_detalle']]),
            'gas' => self::layer('Gas', 'sintesis_diagnostico_pot',
                'sp_area_prestacion_servicio_gas', 'Polygon', ['03' => ['gas_detalle']]),
            'aseo' => self::layer('Aseo', 'servicios_publicos', 'Recoleccion_Residuos',
                'Polygon', ['03' => ['aseo_prestadores', 'aseo_detalle']]),
            'alumbrado' => self::layer('Alumbrado público', 'servicios_publicos',
                'Cobertura_Alumbrado', 'Polygon', ['03' => ['energia_detalle']]),
            'rutas' => self::layer('Rutas Transcaribe', 'transcaribe', 'Transcaribe_Rutas',
                'LineString', ['11' => ['servicio_transporte_predominante',
                    'tipos_transporte_identificados', 'detalle_rutas_transporte']]),
            'paraderos' => self::layer('Paraderos Transcaribe', 'transcaribe',
                'Transcaribe_Paraderos', 'Point', ['11' => ['detalle_paraderos_transporte'],
                    '07' => ['amoblamiento_seleccionado']]),
            'educacion' => self::layer('Educación básica y media', 'educacion',
                'Educacion_Instituciones_Educativas', 'Point',
                ['07' => ['equipamientos_seleccionados'], '12' => ['categorias_edificaciones',
                    'edificaciones_ancla']]),
            'deporte' => self::layer('Escenarios deportivos', 'ider',
                'Ider_Escenarios_Deportivos', 'Point',
                ['07' => ['equipamientos_seleccionados'], '12' => ['edificaciones_ancla']]),
            'salud' => self::layer('Prestadores de salud', 'salud', 'salud_ips_reps',
                'Point', ['07' => ['equipamientos_seleccionados'],
                    '12' => ['categorias_edificaciones', 'edificaciones_ancla']]),
            'cai' => self::layer('CAI', 'seguridad', 'Distriseguridad_Cais_Policia',
                'Point', ['12' => ['categorias_edificaciones', 'edificaciones_ancla'],
                    '13' => ['externalidades_positivas']]),
            'cuadrantes' => self::layer('Cuadrantes de Policía', 'seguridad',
                'Distriseguridad_Cuadrantes_Policia', 'Point',
                ['13' => ['observacion_externalidades']]),
            'riesgo' => self::layer('Riesgo POT', 'pot2001', 'Riesgo', 'Polygon',
                ['13' => ['externalidades_negativas', 'observacion_externalidades'],
                    '16' => ['literal_e_amenazas']]),
            'inundacion_pluvial' => self::layer('Inundación pluvial', 'cambio_climatico',
                'InundacionPluvial', 'Polygon', ['13' => ['externalidades_negativas',
                    'observacion_externalidades'], '16' => ['literal_e_amenazas']]),
        ];
    }

    public static function url(string $key, string $format = 'application/json', ?array $bbox = null): string
    {
        $layer = self::layers()[$key] ?? null;
        if (!$layer) return '';
        $url = self::WFS_BASE . '?map=' . rawurlencode($layer['map'])
            . '&VERSION=1.1.0&REQUEST=GETFEATURE&OUTPUTFORMAT=' . rawurlencode($format)
            . '&SRSNAME=EPSG:4326&SERVICE=WFS&TYPENAME=' . rawurlencode($layer['typename']);
        if ($bbox && count($bbox) === 4) {
            $url .= '&BBOX=' . implode(',', array_map(static fn (float|int $n): string => (string) $n, $bbox))
                . ',EPSG:4326';
        }
        return $url;
    }

    public static function missingModuleFields(): array
    {
        $valid = [];
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [, $fields]) {
            foreach ($fields as $field) $valid[$code][$field[0]] = true;
        }
        $missing = [];
        foreach (self::layers() as $key => $layer) {
            foreach ($layer['targets'] as $code => $fields) {
                foreach ($fields as $field) {
                    if (!isset($valid[$code][$field])) $missing[] = $key . ':' . $code . '.' . $field;
                }
            }
        }
        return $missing;
    }

    private static function layer(string $title, string $map, string $typename, string $geometry, array $targets): array
    {
        return compact('title', 'map', 'typename', 'geometry', 'targets');
    }
}
