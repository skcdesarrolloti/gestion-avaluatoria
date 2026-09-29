<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPhGeneratedText
{
    public static function replaceable(string $text): bool
    {
        $text = trim($text);
        if ($text === '') return true;
        foreach (self::prefixes() as $prefix) if (str_starts_with($text, $prefix)) return true;
        return false;
    }

    private static function prefixes(): array
    {
        return ['Base comparativa:', 'Trazabilidad:', 'Identificación:', 'Tipología y régimen:',
            'Configuración predial:', 'Bienes comunes y soporte:', 'Reglas de uso y operación:',
            'Las reglas de uso y operación de', 'Administración y cargas:', 'Incidencia valuatoria:',
            'Notas y salvedades:', 'Notas normativas y salvedades:', 'La copropiedad corresponde preliminarmente',
            'Se revisa preliminarmente como', 'Lectura preliminar PH sin hallazgos suficientes',
            'Para el análisis de propiedad horizontal se tuvo como soporte', 'Condición especial PH:',
            'Trazabilidad documental:', 'Lectura comparativa:', 'El inmueble objeto de análisis forma parte de',
            'El inmueble objeto de medición se localiza en', 'La copropiedad ', 'Se verifican ',
            'Quedan por confirmar ', 'Se registran alertas o salvedades en ',
            'Los bienes comunes específicos deben confirmarse', 'Bienes comunes esenciales:',
            'Bienes comunes no esenciales', 'Áreas comunes de uso exclusivo:',
            'Soporte operativo y técnico común:', 'No se han marcado bienes comunes verificados',
            'No se han identificado bienes comunes', 'Para la tipología '];
    }
}
