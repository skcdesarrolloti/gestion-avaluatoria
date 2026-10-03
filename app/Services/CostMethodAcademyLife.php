<?php
declare(strict_types=1);
namespace App\Services;

/** Teaching material only: never adopts a life, state or value for an appraisal. */
final class CostMethodAcademyLife
{
    public static function topics(): array
    {
        return [
            ['id'=>'vidas','title'=>'5. Edad y vidas útiles: los 100 años no son universales','reference'=>'Art. 29 · Anexo 2.3.1, tabla 2','page'=>22,'items'=>[
                'Edad (x): tiempo transcurrido de la construcción a la fecha de valoración. Documenta fechas, etapas e intervenciones; pintar o cambiar acabados no reinicia automáticamente la edad estructural.',
                'Vida útil de referencia (VUR): horizonte de la categoría constructiva. La tabla conserva 100 años para permanentes; también contiene 5, 30, 50 y 70 años. Selecciona por sistema y especificaciones, con sustento; no por nombre comercial del inmueble.',
                'Vida remanente (VR): tiempo adicional estimado conforme al procedimiento de prolongación. Vida útil prolongada (VUP): edad más esa vida remanente. Son magnitudes diferentes; VR no sustituye por sí sola el denominador de Ross.',
                'Para obras que no encajen en la tabla: estudios, fabricante, especificaciones o literatura técnica verificable. La referencia IGAC de una tipología no reemplaza la justificación de la vida adoptada.'],
                'headers'=>['Categoría','Referencia','Orientación resumida'], 'rows'=>[
                    ['Temporal / desmontable','5 años','Construcciones provisionales.'],
                    ['Vida corta','30 años','Materiales de desecho, madera ordinaria y otras soluciones de bajas especificaciones.'],
                    ['Vida media','50 años','Adobe, madera y sistemas prefabricados; revisar especificaciones de la tabla.'],
                    ['Vida larga','70 años','Muros de carga, albañilería y sistemas descritos en la tabla 2.'],
                    ['Permanente','100 años','Pórticos de concreto, sistemas reforzados y soluciones que cumplan las condiciones de la tabla.'],
                    ['Patrimonial','Más de 100 años','Tratamiento especial: no depreciar por edad; consultar el art. 30.'],
                    ['Especial','Sustentada técnicamente','Infraestructuras o tecnologías no tradicionales.'],
                    ['Prolongada','Edad + VR','Sólo tras evaluar y sustentar la procedencia.'],
                ]],
            ['id'=>'prolongada','title'=>'6. Cuándo procede la vida útil prolongada','reference'=>'Art. 29 · Anexo 2.3.2','page'=>24,'items'=>[
                'Puede evaluarse si se supera la vida de referencia o desde que se alcanza el 90% de ella. El umbral habilita la evaluación: no concede prolongación automática.',
                'Debe evidenciarse capacidad de seguir prestando servicio: condiciones constructivas, estructurales y funcionales, conservación e incidencia de mantenimiento, rehabilitación o remodelaciones. Sustenta con visita y documentos.',
                'Sólo son admisibles los estados EC de 2,5 a 4,5. No procede para 1,0 a 2,0 ni para 5,0. No aplica en BIC. EC es la calificación del estado; no es el porcentaje de depreciación ni el factor E.',
                'Un reforzamiento requiere evaluar técnicamente la aplicabilidad del procedimiento; cualquier ajuste de edad debe justificarse. La existencia de una intervención, por sí sola, no determina nueva edad ni VUP.']],
            ['id'=>'remanente','title'=>'7. Vida remanente: fórmula y ejemplos de lectura','reference'=>'Anexo 2.3.2 · ecuación 1 y tabla 3','page'=>24,'items'=>[
                'VR = VUR ÷ (EC × 2). VUP = x + VR. VUR, x, VR y VUP se expresan en años; EC es una calificación sin unidad. Primero verifica las condiciones de procedencia.',
                'El anexo indica un límite de VR de un tercio de la VUR. No usar ese tercio como una asignación fija: la ecuación usa el estado admisible. Con EC entre 2,5 y 4,5, el resultado de la fórmula ya es menor que ese límite.',
                'Ejemplo del anexo: VUR 100 años, edad 110, EC 3,0. VR = 100 ÷ 6 = 16,7 años; VUP = 126,7; la tabla ilustra adopción de 127 años.',
                'Ejemplo en etapa final: VUR 100, edad 92, EC 3,0. VR = 16,7 y VUP = 108,7; la tabla ilustra 109 años. Alcanzar el 92% no exime de sustentar conservación y procedencia.',
                'Estos ejemplos son material académico, no valores de esta unidad. Conserva precisión intermedia y documenta el criterio de adopción; un redondeo del ejemplo no es una regla universal.']],
            ['id'=>'conservacion','title'=>'8. Conservación Heideck: estado, porcentaje y factor','reference'=>'Anexo 2.3.3–2.3.4 · tablas 4–6','page'=>26,'items'=>[
                'La inspección, las observaciones y las fotografías sustentan el estado adoptado. La escala tiene nueve estados; el rótulo por sí solo no sustituye sus criterios físicos.',
                'Heideck aporta h, porcentaje de depreciación por conservación. E = (100 − h) ÷ 100 es el factor que permanece. No introducir EC = 3,0 como si fuera h = 3% o E = 3.',
                'El estado 1,5 tiene h = 0,032%, que equivale a 0,00032 en decimal; E = 0,99968. No confundirlo con 3,2%.'],
                'headers'=>['EC','Estado','h · pérdida por estado','E · factor remanente'], 'rows'=>[
                    ['1,0','Óptimo','0,00%','1,000000'],['1,5','Muy bueno','0,032%','0,999680'],
                    ['2,0','Bueno','2,52%','0,974800'],['2,5','Intermedio','8,09%','0,919100'],
                    ['3,0','Regular','18,10%','0,819000'],['3,5','Deficiente','33,20%','0,668000'],
                    ['4,0','Malo','52,60%','0,474000'],['4,5','Muy malo','75,20%','0,248000'],
                    ['5,0','Demolición / ruinoso','100,00%','0,000000'],
                ]],
            ['id'=>'ross','title'=>'9. Ross–Heideck continuo: cómo se interpreta','reference'=>'Art. 30 · Anexo 2.3.4, ecuaciones 2–7','page'=>32,'items'=>[
                'Ross por edad: r = ½ × [x/n + (x/n)²]. x es edad y n es vida útil adoptada conforme al procedimiento aplicable. Usar la misma unidad temporal.',
                'Factor remanente combinado = (1 − r) × E. Valor actual VA = Vn × (1 − r) × E. Pérdida acumulada monetaria = Vn − VA. La disminución por edad y la de conservación no se suman como dos porcentajes independientes.',
                'El anexo denomina FD al factor multiplicador del valor actual: aquí se explica como factor remanente para distinguirlo del porcentaje perdido. La tabla Ross–Heideck usa K como coeficiente perdido: VA = Vn × (1 − K).',
                'Ejemplo académico: Vn 800.000 COP/m², edad 14, vida 70 y estado 2,0. r = 0,12; E = 0,9748; factor remanente = 0,857824; VA = 686.259,20 COP/m²; pérdida = 113.740,80 COP/m².',
                'No convertir el cociente edad/vida en escalones ni adoptar vida por defecto. Si se agota o supera la referencia, revisar el procedimiento aplicable, incluida VUP cuando proceda. C1 explica; no liquida ni cambia parámetros del expediente.']],
            ['id'=>'especiales','title'=>'10. Patrimonio, etapas e intervenciones','reference'=>'Art. 30, parágrafo · Anexo 2.3.1–2.3.2','page'=>23,'items'=>[
                'BIC, conservación arquitectónica, inmuebles históricos o monumentales y casos cuya antigüedad sea atributo de valor: no aplicar depreciación acumulada por edad. Su conservación sí afecta el valor; sustenta la condición especial.',
                'Cuando corresponda al tratamiento patrimonial, la pérdida por conservación puede sustentarse en presupuestos de restauración, reforzamiento, adecuación o intervención especializada. No descontar además otro factor por el mismo daño.',
                'No aplicar VUP a BIC. La referencia patrimonial superior a 100 años no autoriza a depreciarlos por edad.',
                'Componentes o etapas con sistemas, edades, estados o vidas diferentes pueden estudiarse independientemente con justificación técnica. No asignar a garaje o depósito automáticamente la edad y conservación de la oficina.']],
        ];
    }
}
