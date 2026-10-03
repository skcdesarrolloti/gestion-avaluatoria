<?php
declare(strict_types=1);
namespace App\Services;

final class CostMethodAcademy
{
    public static function topics(): array
    {
        return [
            ['title'=>'Qué valora Costo', 'items'=>[
                'Distingue el valor de una construcción existente, su remanente y el presupuesto de retirarla. Son objetos distintos aunque compartan cantidades y APU.',
                'Los datos físicos provienen de capítulos 1 y 3. Un costo de componente no acredita por sí solo un derecho independiente en PH.',
                'Reposición considera técnicas y materiales actuales; reproducción conserva las características originales. El terreno se estudia aparte y se controla su integración.']],
            ['title'=>'Costos directos e indirectos', 'items'=>[
                'Directos: cantidades, materiales, mano de obra, equipos, transporte y demás partidas del presupuesto, con precio, unidad, fecha y localización verificables.',
                'Indirectos: identificar rubro, alcance, valor o porcentaje, base monetaria, fuente y si ya está incluido. No aplicar un AIU o porcentaje universal.',
                'Estudios, diseños, licencias, administración de obra, interventoría y pólizas pueden requerir consideración según el caso. Clasifica y justifica cada inclusión.',
                'Los rubros comerciales o financieros del módulo Proyectos no se trasladan automáticamente al costo del avalúo. Evita duplicar administración incluida en un modelo o APU.']],
            ['title'=>'SISPAC, IGAC y presupuestos', 'items'=>[
                'SISPAC es una publicación especializada de Sistemas para Arquitectura y Construcción S.A.S.; no atribuirla al DANE. Documenta edición, región, fecha y página.',
                'El catálogo de InversKC contiene modelos base y modelos expresamente estimados. Conserva sus pesos originales y la condición de publicado, estimado o pendiente de verificar.',
                'IGAC orienta la identificación constructiva. Registrar una tipología no proporciona automáticamente un costo vigente ni sustituye el soporte del precio.',
                'Conserva códigos de insumo, APU y presupuesto para actualizar referencias sin perder relaciones. Fecha de captura y fecha del precio son datos diferentes.']],
            ['title'=>'Avance de construcción', 'items'=>[
                'Separa cero confirmado, dato pendiente y no aplica. Un capítulo sin porcentaje no equivale a un capítulo no construido.',
                'Una estructura con peso del 19,35%, ejecutada al 50%, aporta 9,675 puntos al modelo completo si su base es 100%. El 50% de ese capítulo no es el avance global.',
                'Si se estudia sólo un alcance parcial, declara su presupuesto y denominador. No excluir del modelo completo partidas por el solo hecho de que aún no estén ejecutadas.',
                'La participación de administración o preliminares en un costo no debe confundirse con integridad física recuperable. Revisa qué base corresponde al caso.']],
            ['title'=>'Desmantelamiento y demolición', 'items'=>[
                'Pérdida material ya ocurrida: inspecciona qué permanece, qué fue retirado y cuál es la evidencia. Esto describe el remanente existente.',
                'Trabajo futuro de retiro: presupuestar desmontaje, demolición, protecciones, cargue, transporte y disposición, más indirectos pertinentes. Requiere cantidades propias.',
                'Material recuperable: documenta cantidad, condición, posibilidad de venta, fuente y costos asociados. No atribuyas un ingreso automático por salvamento.',
                'No calcular demolición sobre el área del lote por defecto. No descontar ese presupuesto automáticamente del valor del inmueble: primero justifica alcance e integración.']],
            ['title'=>'Depreciación Ross–Heideck', 'items'=>[
                'Para construcciones y anexos, la guía del costo adopta Ross–Heideck continuo por edad y conservación. Los campos antiguos de Fitto se conservan como antecedentes, no como motor del nuevo costo.',
                'Confirma edad adoptada, vida útil, estado de conservación y evidencia. Distingue depreciación acumulada, factor remanente y descuento monetario.',
                'Consulta las condiciones de vida útil prolongada y las excepciones patrimoniales de la Resolución 941; no sustituyas el análisis con una vida útil por defecto.',
                'No reconocer nuevamente con depreciación un daño ya descontado en el remanente o en otra partida. El cálculo reproducible se desarrollará en C4.']],
        ];
    }
}
