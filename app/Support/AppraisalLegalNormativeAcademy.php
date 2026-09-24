<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalLegalNormativeAcademy
{
    /** @return array<int, array{src:string, quote:string, use:string, report:string}> */
    public static function rows(): array
    {
        return [
            ['src' => 'NTS S 03 / NTS I 01', 'quote' => 'El informe identifica derechos, información examinada, hipótesis, restricciones y salvedades.', 'use' => 'Amarrar certificado, folio, titular, derecho valuado, fuente registral y alcance de la revisión.', 'report' => 'Pasan datos adoptados, fuente y salvedades; el soporte completo queda como respaldo.'],
            ['src' => 'Decreto 1420 · arts. 21 y 22', 'quote' => 'El avalúo considera características físicas, jurídicas y económicas del inmueble.', 'use' => 'Separar matrícula, catastro, titularidad, PH, gravámenes, afectaciones y restricciones que inciden en valor.', 'report' => 'Pasan las condiciones jurídicas relevantes y su posible incidencia valuatoria.'],
            ['src' => 'IVS 104 / IVS 106', 'quote' => 'Los datos significativos, supuestos y limitaciones deben ser suficientes y trazables.', 'use' => 'Distinguir lectura automática, validación del analista, documentos aportados y datos pendientes.', 'report' => 'El lector entiende qué se verificó, qué quedó pendiente y qué no constituye estudio de títulos.'],
            ['src' => 'IVS 400', 'quote' => 'El derecho inmobiliario comprende titularidad, restricciones, cargas y circunstancias del activo.', 'use' => 'Conectar cargas, limitaciones, medidas cautelares, PH y derechos observados con comercialización o salvedades.', 'report' => 'La conclusión jurídica se expresa como soporte del avalúo, no como certificación legal absoluta.'],
        ];
    }
}