<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhExtractionRules
{
    public static function technical(): array
    {
        return [
            'fuente_documental' => ['escritura publica', 'reglamento de propiedad horizontal', 'documento'],
            'escritura_reforma' => ['escritura', 'notaria', 'acto de referencia'],
            'direccion_referencia' => ['ubicacion y determinacion', 'ubicado en', 'localizado en', 'direccion del inmueble'],
            'tipo_propiedad_horizontal' => ['propiedad horizontal', 'sometimiento'],
            'regimen_especial' => ['zona franca', 'usuario operador', 'regimen especial'],
            'naturaleza_conjunto' => ['descripcion del edificio', 'descripcion general', 'parque industrial', 'centro logistico'],
            'uso_dominante' => ['destino:', 'uso dominante', 'destinacion del edificio', 'actividad principal'],
            'usos_complementarios' => ['usos complementarios', 'usos permitidos'],
            'relacion_funcional_usos' => ['relacion funcional', 'usos mixtos', 'integracion de usos'],
            'etapas_copropiedad' => ['etapas del proyecto', 'etapas del conjunto', 'sectores del conjunto'],
            'numero_edificios' => ['bloques', 'torres', 'naves', 'edificios'],
            'numero_unidades' => ['unidades privadas', 'unidades inmobiliarias'],
            'resumen_areas_conjunto' => ['area privada', 'area comun', 'area construida', 'cuadro de areas'],
            'desarrollos_relevantes' => ['desenglobe', 'subdivision', 'ampliacion'],
            'lotes_por_etapa' => ['lote matriz', 'lotes resultantes', 'lotes por etapa'],
            'organizacion_interna' => ['organizacion interna', 'sectores internos', 'distribucion por pisos'],
            'ubicacion_unidad' => ['ubicacion de la unidad analizada'],
            'subdivisiones_futuras' => ['futura subdivision', 'integracion futura'],
            'vias_internas' => ['vias internas', 'circulacion vehicular'],
            'red_contra_incendios' => ['red contra incendios', 'hidrante', 'gabinetes contra incendio'],
            'equipamiento_tecnico' => ['subestacion', 'planta electrica', 'bascula', 'cuarto tecnico', 'ascensores', 'bombas'],
            'porteria_administracion_vigilancia' => ['porteria', 'administracion', 'vigilancia'],
            'cctv_control_acceso' => ['cctv', 'control de acceso'],
            'apoyo_logistico_aduanero' => ['apoyo logistico', 'aduanero', 'zona franca'],
            'muelles' => ['muelle', 'bahia', 'rampa'],
            'patios_maniobra' => ['patio de maniobra', 'maniobra', 'radio de giro'],
            'circulacion_pesada' => ['tractomula', 'camion', 'vehiculo pesado'],
            'zonas_espera' => ['zona de espera', 'espera de vehiculos'],
            'reglas_cargue_descargue' => ['reglas de cargue', 'reglas de descargue'],
            'cargue_descargue' => ['cargue', 'descargue', 'flujo logistico'],
            'usos_permitidos' => ['usos permitidos', 'destinacion permitida', 'destino:', 'destinados a'],
            'usos_restringidos' => ['prohibido', 'restriccion', 'limitacion'],
            'reglas_constructivas' => ['reglas constructivas', 'licencia', 'cerramiento'],
            'condiciones_normativas_operativas' => ['residuos', 'aislamiento', 'normas de funcionamiento'],
            'condiciones_usuario_operador' => ['funciones del administrador', 'funciones del consejo', 'usuario operador'],
            'expensas_cuotas' => ['expensas', 'cuota de administracion', 'cuotas de administracion', 'fondo de imprevistos'],
            'coeficientes_copropiedad' => ['coeficiente de copropiedad', 'coeficientes'],
            'responsabilidades_bienes_comunes' => ['conservacion de los bienes comunes', 'bienes comunes'],
            'cargas_comercializacion' => ['comercializacion', 'restricciones de venta', 'arriendo'],
            'incidencia_operacion_bodegas' => ['operacion de bodegas', 'logistica', 'maniobra'],
            'incidencia_valor_soporte_comun' => ['soporte comun', 'aporte de valor'],
            'incidencia_restricciones_regimen' => ['regimen especial', 'restricciones del regimen'],
            'incidencia_comercializacion_interna' => ['comercializacion interna', 'organizacion interna'],
            'lectura_valuatoria' => ['valor', 'valuacion', 'avaluo'],
            'salvedades_reglamento' => ['salvedades al reglamento', 'aclaraciones al reglamento'],
            'salvedades_visita' => ['visita', 'inspeccion'],
            'salvedades_validacion' => ['validacion documental', 'certificado', 'plano'],
            'observaciones_extraccion' => ['observacion', 'nota'],
        ];
    }

}
