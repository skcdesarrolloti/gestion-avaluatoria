<?php
declare(strict_types=1);
namespace App\Services;

final class CostMethodAcademy
{
    public const ANNEX_URL='https://www.igac.gov.co/sites/default/files/transparencia/normograma/Anexo_Final_.pdf';
    public const RESOLUTION_URL='https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf';
    public static function topics(): array
    {
        return array_merge([
            ['id'=>'objeto','title'=>'1. Qué se valora y qué información se necesita','reference'=>'Arts. 5, 11, 12 y 27','items'=>[
                'El método integra terreno y valor actual de construcciones y anexos incluidos en el encargo. VC = VT + CT − D: CT es costo a nuevo total y D es pérdida acumulada monetaria. El terreno requiere valoración propia; no se deprecia con Ross.',
                'Consulta capítulos 1 y 3: unidades, uso, cantidades y unidades de medida, tipología, materiales, condición física y jurídica, edad, conservación e intervenciones. Cada anexo conserva su propia identidad y soporte.',
                'En PH, confronta composición privada, derechos, elementos comunes y tratamiento de anexos. Un costo físico no acredita matrícula independiente. Controla integración conforme al art. 36 y evita sumar otra vez terreno o componentes ya contemplados.',
                'Fuentes verificables, limitaciones expresas y cálculos reconstruibles son parte del soporte del avalúo. Un indicador verde acredita presencia de un dato, no su suficiencia técnica.']],
            ['id'=>'reposicion','title'=>'2. Reposición y reproducción a nuevo','reference'=>'Arts. 27–28','items'=>[
                'Reposición: construcción similar con técnicas y materiales actuales. Reproducción: réplica que procura conservar diseños y métodos originales, con materiales originales o sustitutos; considerar bienes patrimoniales cuando corresponda.',
                'Costo a nuevo y valor actual son diferentes. Primero se sustenta el costo de construir; después se aplica la depreciación pertinente. Conservar importe unitario, unidad, cantidades y total sin confundir COP/m² con COP.',
                'El costo debe corresponder a las especificaciones y localización del caso. Una oficina comercial no adquiere un costo sólo por su nombre; compara estructura, acabados, redes y componentes.']],
            ['id'=>'fuentes','title'=>'3. Fuentes del costo y papel de los APU','reference'=>'Art. 28, parágrafo 2 · art. 5','items'=>[
                'Planos, especificaciones, memorias y cantidades sustentan un presupuesto. Cuando no existen, no están disponibles o son insuficientes, pueden emplearse tipologías, modelos, bases de datos o publicaciones técnicas idóneas y verificables, explicando la razón.',
                'Registrar fuente, edición, fecha del precio, región, página, unidad y alcance. Distinguir costo publicado, estimación propia y dato pendiente; fecha de captura o edición no prueba vigencia del precio.',
                'Los APU pueden apoyar un presupuesto particular, pero C1 no exige diligenciar el catálogo completo. El desarrollo principal del presupuesto de proyecto se estudiará en Residual; no confundirlo con el costo de la construcción existente.',
                'SISPAC es una publicación de Sistemas para Arquitectura y Construcción S.A.S., no del DANE. Una tipología IGAC identifica características; no proporciona por sí sola un precio actualizado.']],
            ['id'=>'indirectos','title'=>'4. Directos e indirectos sin duplicaciones','reference'=>'Art. 28','items'=>[
                'El costo a nuevo comprende directos e indirectos pertinentes. Identifica cuáles contiene la fuente antes de adicionar rubros. La localización incide en el soporte del costo.',
                'Como apoyo de revisión: materiales, mano de obra, equipos y partidas de ejecución; estudios, diseños, licencias, administración de obra, interventoría y pólizas según el caso. Justificar clasificación e inclusión de cada rubro.',
                'Un porcentaje necesita rubro, base monetaria y soporte; no adoptar AIU universal. Gastos comerciales y financieros de un proyecto residual no se trasladan automáticamente a reposición.',
                'Si una publicación entrega sólo directos, documenta los indirectos adicionales; si entrega costo total, comprueba qué incluye antes de volver a sumarlos.']],
        ],CostMethodAcademyLife::topics(),[
            ['id'=>'ejecucion','title'=>'11. Ejecución, capítulos SISPAC y remanente físico','reference'=>'Apoyo operativo · no sustituye Ross–Heideck','items'=>[
                'Avance ejecutado, pérdida material y depreciación acumulada son conceptos distintos. Inspecciona lo que existe y declara el alcance de su costo a nuevo.',
                'Distingue cero confirmado, pendiente y no aplica. Conserva pesos y fuentes de modelos SISPAC; un modelo estimado no se presenta como tabla oficial.',
                'Ejemplo de ponderación: un capítulo con peso 19,35% ejecutado al 50% aporta 9,675 puntos al modelo completo. Si el alcance es parcial, declara denominador y presupuesto; no reducirlo silenciosamente.',
                'No encadenar porcentaje de ejecución, detrimento y Ross descontando dos veces la misma pérdida. Su aplicación exige análisis sustentado en C4; C1 sólo enseña la distinción.']],
            ['id'=>'retiro','title'=>'12. Demolición y desmantelamiento: dos preguntas distintas','reference'=>'Anexo tablas 4–6 · presupuesto complementario','page'=>26,'items'=>[
                'Estado 5 describe deterioro ruinoso y pérdida por conservación del 100%. Ese estado no calcula cuánto cuesta demoler ni retirar materiales.',
                'Remanente físico: documentar elementos existentes y retirados. Presupuesto futuro de retiro: cantidades, protecciones, desmontaje, cargue, transporte, disposición e indirectos pertinentes; no usar área del lote por defecto.',
                'La posibilidad de vender recuperables requiere sustento de cantidades, condición, precio y costos asociados. No asignar salvamento automático ni importar la fórmula de maquinaria a edificios.',
                'Un presupuesto de retiro no se deprecia como una construcción. Su incidencia en el avalúo requiere justificar encargo e integración; no se resta automáticamente por marcar estado 5.']],
            ['id'=>'cierre','title'=>'13. Qué debe quedar sustentado antes del análisis','reference'=>'Arts. 5, 11–14 y 27–30','items'=>[
                'Identificación por unidad; especificaciones y cantidades con fuente; costo a nuevo y alcance de directos/indirectos; edad a fecha de valoración; categoría y vida de referencia; conservación con evidencia.',
                'Cuando aplique: procedencia de VUP, EC admisible, vida remanente, vida adoptada y criterio de precisión; condición patrimonial; desagregación por etapas; pérdidas o retiro y control de duplicación.',
                'Conservar decisiones, fuentes y limitaciones para reconstruir la memoria. C1 no adopta edad, vida, conservación o valor ni guarda cálculos: es consulta académica.']],
        ]);
    }
}
