<?php
declare(strict_types=1);
namespace App\Services;

final class Resolution941CommonReading
{
    public static function articles(): array
    {
        return [
            1 => ['Objeto', 'Comprueba que el encargo corresponda al objeto de la resolución.'],
            2 => ['Ámbito de aplicación', 'Verifica la obligatoriedad según el proceso y el encargo. La reproducción del Diario Oficial repite «Artículo 1°» en este encabezado; se conserva ese texto.'],
            3 => ['Anexo técnico', 'El anexo forma parte integral del acto; consulta también el desarrollo del método en ese documento.'],
            4 => ['Definiciones', 'Usa las definiciones del acto para interpretar los datos y el resultado.'],
            5 => ['Principios generales', 'Revisa los principios que deben orientar el procedimiento y su sustentación.'],
            6 => ['Solicitud del avalúo', 'Confirma la solicitud, el objeto y las condiciones del encargo.'],
            7 => ['Marco jurídico del encargo', 'Verifica el marco jurídico que debe informar el solicitante.'],
            8 => ['Documentos adjuntos', 'Comprueba los documentos exigidos y las condiciones para complementar información faltante.'],
            9 => ['Plazo de entrega', 'Revisa el plazo aplicable y las condiciones para acordarlo.'],
            10 => ['Visita técnica', 'Verifica la inspección del sujeto, la evidencia y las condiciones de la investigación.'],
            11 => ['Parámetros del avalúo', 'Aplica los parámetros correspondientes al tipo de inmueble; el texto íntegro conserva también los casos rurales.'],
            12 => ['Identificación física, jurídica y normativa', 'Contrasta áreas, derechos, norma y restricciones del inmueble.'],
            13 => ['Etapas del avalúo', 'Comprueba el procedimiento, el registro de fuentes y la sustentación del resultado.'],
            14 => ['Informe técnico', 'Revisa el contenido mínimo, la memoria y los soportes exigidos para el informe.'],
            15 => ['Métodos valuatorios', 'Documenta la selección del método conforme al inmueble y a la información disponible.'],
            35 => ['Caso especial: dotacional o institucional', 'Solo si corresponde: distingue la preferencia por métodos generales de la excepcionalidad y verifica consistencia.'],
            39 => ['Caso especial: condición VIS', 'Solo para ese encargo: considera la totalidad del inmueble y verifica la condición VIS con las disposiciones vigentes.'],
            40 => ['Caso especial: interés cultural', 'Verifica las restricciones y el procedimiento propio de los bienes de interés cultural; distingue PH y NPH.'],
            41 => ['Caso especial: expansión urbana', 'Si no hay plan parcial aprobado, revisa las condiciones y el uso que permite considerar el artículo; no supongas un desarrollo futuro autorizado.'],
            42 => ['Caso especial: maquinaria y equipos', 'Comprueba categoría RAA, pertinencia del método y equipos ya incluidos en el valor de la construcción para evitar doble conteo.'],
            57 => ['Revisión e impugnación', 'Conserva trazabilidad y comprueba el procedimiento aplicable a las solicitudes de revisión o impugnación.'],
            58 => ['Información de avalúos comerciales', 'Verifica la obligación y el procedimiento de disposición de información. Esta consulta no realiza envíos.'],
            59 => ['Transitoriedad', 'Comprueba la norma aplicable según la situación y fecha del avalúo.'],
            60 => ['Vigencia y derogatoria', 'Revisa la entrada en vigor y las disposiciones derogadas.'],
            61 => ['Publicación', 'Consulta la publicación del acto y su fuente.'],
        ];
    }
}
