<?php
declare(strict_types=1);
namespace App\Support;

final class UrbanNormativeAcademy
{
    public static function blocks(): array
    {
        return [
            ['MIDAS · Uso del suelo', 'Consulta predial por referencia o número predial en MIDAS. Debe registrar opción consultada, resultado leído, capa o soporte y fecha.', 'Fuente operativa del dato que se transcribe al numeral 5; no sustituye concepto formal si el caso lo requiere.'],
            ['POT Cartagena · Decreto 0977 de 2001', 'Base vigente del ordenamiento territorial de Cartagena mientras no se adopte formalmente un POT nuevo.', 'Soporta clasificación, áreas de actividad, tratamientos, cuadros de uso y restricciones urbanísticas.'],
            ['Cuadros de reglamentación de usos', 'Cruce entre categoría de actividad y uso principal, compatible, complementario, restringido o prohibido.', 'Permite explicar si el uso actual o pretendido es admisible, condicionado o no permitido.'],
            ['Concepto de uso del suelo', 'Dictamen escrito de Planeación sobre usos permitidos de un predio o edificación conforme al POT e instrumentos que lo desarrollen.', 'Se registra radicado, fecha, actividad consultada, conclusión y salvedades cuando exista.'],
            ['Ley 388 de 1997 y Decreto 1077 de 2015', 'Marco nacional de ordenamiento territorial, licenciamiento y desarrollo urbano.', 'Sirve como soporte general; la conclusión del predio depende de la norma local e instrumentos aplicables.'],
            ['Patrimonio y conservación', 'Centro Histórico, área de influencia, periferia histórica o bienes con tratamiento de conservación requieren lectura especial.', 'Activa restricciones, autoridad competente y salvedades para no asumir edificabilidad ordinaria.'],
            ['Determinantes ambientales y riesgo', 'Rondas, protección, amenaza, riesgo, zonas ambientales o afectaciones externas pueden limitar uso o desarrollo.', 'Debe cruzarse con MIDAS, POT, autoridad ambiental o soporte aportado y pasar a condiciones restrictivas.'],
            ['Instrumentos complementarios', 'Planes parciales, unidades de actuación, resoluciones, licencias o actos especiales pueden modificar o precisar la norma base.', 'El analista registra el instrumento pertinente si el predio no se resuelve con el cuadro general.'],
        ];
    }

    public static function sourceOptions(): array
    {
        return ['pendiente' => 'Pendiente', 'midas' => 'MIDAS uso del suelo', 'concepto' => 'Concepto oficial',
            'pot' => 'POT / cuadro de usos', 'mixta' => 'MIDAS + norma + concepto', 'no_disponible' => 'No disponible'];
    }

    public static function useResults(): array
    {
        return ['' => 'Pendiente de cruce', 'principal' => 'Uso principal', 'compatible' => 'Uso compatible',
            'complementario' => 'Uso complementario', 'restringido' => 'Uso restringido',
            'prohibido' => 'Uso prohibido', 'no_determinado' => 'No determinado con los soportes disponibles'];
    }
}
