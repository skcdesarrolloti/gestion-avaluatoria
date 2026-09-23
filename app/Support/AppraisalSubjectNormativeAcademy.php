<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSubjectNormativeAcademy
{
    /** @return array<int, array{src:string, quote:string, use:string, report:string}> */
    public static function for(string $module): array
    {
        return self::rows()[$module] ?? [];
    }

    /** @return array<string, array<int, array{src:string, quote:string, use:string, report:string}>> */
    private static function rows(): array
    {
        return [
            '3.1' => [
                ['src'=>'NTS S 03 / NTS I 01', 'quote'=>'Informe con alcance, identificación del bien, información examinada, hipótesis y salvedades.', 'use'=>'Amarrar nombre del sujeto, unidad, dirección, matrícula, catastro, uso, PH y fuente adoptada.', 'report'=>'Pasa la identificación adoptada y la fuente; lo dudoso queda como salvedad o soporte.'],
                ['src'=>'Decreto 1420 · arts. 21 y 22', 'quote'=>'El avalúo considera localización, características físicas, jurídicas y económicas del inmueble.', 'use'=>'Separar ubicación física, registro, norma urbana, restricciones, servicios y entorno.', 'report'=>'Pasan los datos que explican identidad, ubicación, uso y condiciones del bien.'],
                ['src'=>'IVS 104 / IVS 106', 'quote'=>'Datos relevantes, observables cuando sea posible, consistentes con el propósito y trazables.', 'use'=>'No mezclar fuentes; conservar dirección de CTL, MIDAS, predial, escritura y dirección adoptada.', 'report'=>'El lector entiende qué dato se adoptó y de dónde salió.'],
            ],
            '3.2' => [
                ['src'=>'NTS S 03 / NTS I 01', 'quote'=>'El informe debe permitir seguir la fuente de la información usada.', 'use'=>'Registrar área por escritura, CTL, catastro, MIDAS, plano o medición y justificar el área adoptada.', 'report'=>'Pasa el área adoptada con fuente; las diferencias quedan visibles como salvedad.'],
                ['src'=>'Decreto 1420 · arts. 21 y 22', 'quote'=>'Las áreas, linderos y condiciones del terreno forman parte de la caracterización valuatoria.', 'use'=>'Revisar forma, frente, fondo, topografía, cerramiento, servicios y restricciones del suelo.', 'report'=>'Pasan variables que afectan aprovechamiento, comparación o cálculo del terreno.'],
                ['src'=>'IVS 104', 'quote'=>'Los insumos significativos deben ser suficientes y apropiados para el propósito.', 'use'=>'No adoptar automáticamente el primer dato; priorizar fuente confiable y consistencia técnica.', 'report'=>'El área queda defendible frente a mercado, costo o método aplicado.'],
            ],
            '3.3' => [
                ['src'=>'Decreto 1420 · arts. 21 y 22', 'quote'=>'La construcción, edad, estado y conservación son características relevantes del inmueble.', 'use'=>'Registrar tipo constructivo, área construida, vetustez, vida útil, estado de obra y conservación.', 'report'=>'Pasan los elementos que soportan depreciación, reposición o comparabilidad.'],
                ['src'=>'NTS M 01', 'quote'=>'La metodología debe estar soportada en información verificable y pertinente.', 'use'=>'Conectar materiales, componentes y estado con vida útil, costos o comparación de mercado.', 'report'=>'El informe explica por qué la construcción aporta, limita o requiere ajuste.'],
                ['src'=>'IVS 105 / IVS 106', 'quote'=>'El enfoque y los datos usados deben quedar explicados y documentados.', 'use'=>'Distinguir construcción principal, anexos, cubiertas, cerramientos, equipos y obras inconclusas.', 'report'=>'Pasan características materiales, no listados sin efecto valuatorio.'],
            ],
            '3.4' => [
                ['src'=>'NTS M 01', 'quote'=>'Los ajustes deben guardar relación con información pertinente, verificable y comparable.', 'use'=>'Calificar atributos y deméritos solo cuando incidan en mercado, uso, renta, costo o liquidez.', 'report'=>'Pasan ajustes justificados; la escala interna no reemplaza el juicio del perito.'],
                ['src'=>'IVS 103 / IVS 104', 'quote'=>'El valuador selecciona enfoques e insumos significativos según el activo y el propósito.', 'use'=>'Usar evidencia: visita, foto, documento, comparable, mercado o criterio técnico verificable.', 'report'=>'El informe conserva trazabilidad del diferencial que afecta la unidad.'],
                ['src'=>'NTS S 03', 'quote'=>'Las conclusiones deben tener soportes y limitaciones claras.', 'use'=>'Si se marca evidencia fotográfica, cerrar soporte en 3.7 o dejar salvedad expresa.', 'report'=>'No queda ajuste sin respaldo visible o explicación técnica.'],
            ],
            '3.7' => [
                ['src'=>'NTS S 03 / NTS I 01', 'quote'=>'El informe debe identificar soportes, información examinada y salvedades.', 'use'=>'Cargar fotos por grupo: sujeto, unidad, construcción, terreno, atributo y PH cuando aplique.', 'report'=>'Las imágenes soportan existencia, estado, acceso, entorno y diferenciales descritos.'],
                ['src'=>'IVS 104 / IVS 106', 'quote'=>'La documentación debe permitir seguir la lógica entre dato, observación y conclusión.', 'use'=>'Nombrar la foto según lo que evidencia; evitar imágenes genéricas sin relación con el informe.', 'report'=>'El lector puede rastrear cada afirmación visualmente relevante.'],
                ['src'=>'Alcance técnico', 'quote'=>'La foto evidencia visita o soporte visual; no certifica por sí sola cumplimiento jurídico o técnico.', 'use'=>'Usar salvedad cuando estado, funcionamiento o cumplimiento requiera documento o prueba adicional.', 'report'=>'La foto acompaña el análisis; no sustituye certificado, norma, prueba ni estudio especializado.'],
            ],
        ];
    }
}
