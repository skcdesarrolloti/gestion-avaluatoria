<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalObsolescenceCatalog
{
    public static function groups(): array
    {
        return [
            'fisica' => ['OBS-FIS', 'Obsolescencia física', 'Curabilidad predominante del hallazgo', ['curable'=>'Curable','parcial'=>'Parcialmente curable','no_curable'=>'No curable','nd'=>'No determinado'], [
                'estructura' => 'Estructura', 'cubiertas' => 'Cubiertas, fachadas y cerramientos',
                'instalaciones' => 'Instalaciones y redes', 'acabados' => 'Acabados',
                'mantenimiento' => 'Conservación y mantenimiento']],
            'funcional' => ['OBS-FUN', 'Obsolescencia funcional', 'Origen funcional predominante', ['capital'=>'Exceso de costo de capital','operativo'=>'Exceso de costo operativo','utilidad'=>'Menor productividad / utilidad','uso'=>'Limitación de uso','especializacion'=>'Especialización','tecnologia'=>'Tecnología','otro'=>'Otro'], [
                'distribucion' => 'Distribución', 'dimensiones' => 'Dimensiones', 'flexibilidad' => 'Flexibilidad',
                'adecuacion' => 'Adecuación al uso', 'especializacion' => 'Especialización', 'tecnologia' => 'Tecnología']],
            'externa' => ['OBS-EXT', 'Obsolescencia externa / económica', 'Temporalidad predominante', ['temporal'=>'Temporal','permanente'=>'Permanente','indeterminada'=>'Indeterminada'], [
                'mercado' => 'Mercado / demanda', 'entorno' => 'Entorno', 'accesibilidad' => 'Accesibilidad',
                'ambiental' => 'Ambiental', 'urbanistico' => 'Urbanístico / regulatorio', 'vocacion' => 'Vocación del sector']],
        ];
    }
    public static function scores(): array { return ['na'=>'No aplica', '0'=>'Sin hallazgo', '1'=>'Hallazgo leve', '2'=>'Hallazgo relevante', '3'=>'Hallazgo crítico']; }
    public static function scoreHelp(): array { return [''=>'Pendiente: todavía no revisado.', 'na'=>'No aplica: no corresponde al caso.', '0'=>'Sin hallazgo: revisado y funciona razonablemente.', '1'=>'Leve: señal menor, sin efecto material claro.', '2'=>'Relevante: puede afectar uso, comparación o negociación.', '3'=>'Crítica: afecta de forma importante y requiere soporte claro.']; }
    public static function metaHelp(): array { return [''=>'Es una conclusión general del bloque; no aplica a los factores marcados sin hallazgo ni modifica la lectura normativa.', 'curable'=>'Curable: el hallazgo predominante podría corregirse con una intervención razonable.', 'parcial'=>'Parcialmente curable: una intervención ayuda al hallazgo predominante, pero no elimina todo el efecto.', 'no_curable'=>'No curable: el hallazgo predominante no se corrige físicamente o su corrección no es razonable.', 'nd'=>'No determinado: úsalo cuando no hay soporte suficiente para definir curabilidad.', 'capital'=>'Exceso de costo de capital: el diseño o configuración exige más inversión que una alternativa comparable.', 'operativo'=>'Exceso de costo operativo: genera mayores costos de operación o mantenimiento.', 'utilidad'=>'Menor productividad / utilidad: reduce aprovechamiento, renta, ocupación o eficiencia.', 'uso'=>'Limitación de uso: restringe el uso esperado para la tipología.', 'especializacion'=>'Especialización: configuración muy específica que reduce mercado potencial.', 'tecnologia'=>'Tecnología: rezago técnico frente a inmuebles comparables.', 'otro'=>'Otro: describe el origen en el soporte breve.', 'temporal'=>'Temporal: condición externa que puede cambiar en el corto o mediano plazo.', 'permanente'=>'Permanente: condición externa estructural o difícil de remover.', 'indeterminada'=>'Indeterminada: no hay soporte suficiente para definir duración.']; }

    public static function factorHelp(): array
    {
        return [
            'fisica' => [
                'estructura'=>'Elementos portantes, estabilidad aparente, fisuras, asentamientos o señales que comprometan vida útil.',
                'cubiertas'=>'Cubierta, fachadas y cerramientos: protección frente a intemperie, filtraciones, humedad, fisuras o deterioro visible.',
                'instalaciones'=>'Redes eléctricas, hidrosanitarias, voz/datos, gas, equipos y suficiencia técnica frente al uso.',
                'acabados'=>'Estado y vigencia de pisos, muros, cielos, carpinterías y terminaciones frente a inmuebles comparables.',
                'mantenimiento'=>'Conservación general, rutinas de mantenimiento, reparaciones diferidas y estado observable del inmueble.'
            ],
            'funcional' => [
                'distribucion'=>'Mira si la organización de espacios, circulaciones y accesos permite operar bien el uso valuado.',
                'dimensiones'=>'Área, frente, fondo, altura, proporciones y capacidad frente a lo esperado por mercado para esta tipología.',
                'flexibilidad'=>'Capacidad de adaptarse a otro usuario, división, ampliación o cambio sin obras desproporcionadas.',
                'adecuacion'=>'Compatibilidad entre diseño actual, uso permitido, tipología y operación real del inmueble.',
                'especializacion'=>'Grado en que el inmueble fue hecho para un usuario o actividad específica y reduce mercado alternativo.',
                'tecnologia'=>'Rezago o suficiencia de sistemas técnicos, conectividad, automatización, eficiencia y soporte operativo.'
            ],
            'externa' => [
                'mercado'=>'Oferta, demanda, vacancia, competencia y apetito de mercado por esta tipología en el sector.',
                'entorno'=>'Vecindario, mezcla de usos, imagen, seguridad percibida y compatibilidad de actividades alrededor.',
                'accesibilidad'=>'Ingreso, transporte, conectividad vial, parqueo, maniobra y facilidad para usuarios o visitantes.',
                'ambiental'=>'Ruido, olores, inundación, contaminación, restricciones ambientales o externalidades físicas del entorno.',
                'urbanistico'=>'Usos permitidos, cargas, restricciones, licencias, afectaciones o cambios regulatorios que incidan en el inmueble.',
                'vocacion'=>'Coherencia entre el inmueble y la tendencia del sector: consolidación, cambio de uso o pérdida de atractivo.'
            ],
        ];
    }

    public static function readerGuidance(): array
    {
        return [
            'fisica' => [
                'definition' => 'Se relaciona con la pérdida de funcionalidad o valor por deterioro físico, edad, uso, falta de mantenimiento, abandono u otros factores observables.',
                'no_finding' => 'No se evidencian condiciones de obsolescencia física; el inmueble presenta estado de conservación funcional para su uso, sin señales aparentes que comprometan la ocupación.',
                'support' => 'Sustento típico: visita, fotografías, edad aproximada, estado de conservación, mantenimiento, patologías visibles e instalaciones.',
            ],
            'funcional' => [
                'definition' => 'Se relaciona con la pérdida de utilidad por distribución, dimensiones, adecuaciones especializadas, limitaciones de uso o diseño menos eficiente frente al mercado.',
                'no_finding' => 'No se evidencian condiciones de obsolescencia funcional; los espacios y la distribución son acordes con la destinación y permiten el uso previsto.',
                'support' => 'Sustento típico: tipología, uso permitido, distribución, circulaciones, áreas, flexibilidad, adecuaciones y comparación con inmuebles similares.',
            ],
            'externa' => [
                'definition' => 'Se relaciona con pérdida de valor por factores externos: mercado, entorno, accesibilidad, regulación, ambiente o cambios en la vocación del sector.',
                'no_finding' => 'No se evidencian condiciones de obsolescencia externa; el inmueble se integra al entorno y las actividades del sector son compatibles con su uso potencial.',
                'support' => 'Sustento típico: entorno, mercado, accesibilidad, seguridad, compatibilidad de usos, norma urbana, vocación del sector y evidencia de visita.',
            ],
        ];
    }

    public static function normativeAcademy(): array
    {
        return [
            ['src'=>'NTS S 03 · Informe', 'quote'=>'Alcance, información examinada, metodología, soportes y salvedades.', 'use'=>'Cada hallazgo queda separado por tipo de obsolescencia, factor revisado y soporte breve.', 'report'=>'Al entregable pasa el diagnóstico corto; la evidencia queda como respaldo.'],
            ['src'=>'NTS M 01 · metodología', 'quote'=>'Método explicado y soportado por información pertinente.', 'use'=>'Marcar un hallazgo no descuenta valor; advierte si debe afectar costo, mercado, renta o comparación.', 'report'=>'Al entregable pasa solo el efecto material sustentado.'],
            ['src'=>'Decreto 422 de 2000', 'quote'=>'Indicar el sistema de depreciación utilizado.', 'use'=>'Si se cuantifica obsolescencia, se explica aparte el método usado y su soporte.', 'report'=>'Sin método y soporte no se presenta descuento económico.'],
            ['src'=>'IVS 103 · Enfoques', 'quote'=>'Enfoque y método apropiados según activo, propósito y datos.', 'use'=>'La obsolescencia se lee como insumo del enfoque seleccionado; no reemplaza el juicio profesional.', 'report'=>'El informe explica si incide en costo, mercado o renta.'],
            ['src'=>'IVS 104 · Datos', 'quote'=>'Datos relevantes, observables cuando sea posible y consistentes.', 'use'=>'El soporte breve debe indicar fuente: visita, foto, documento, mercado, comparable, norma o cálculo.', 'report'=>'El informe conserva solo datos confiables o salvedades necesarias.'],
            ['src'=>'IVS 106 · Reporte', 'quote'=>'Documentar lógica, supuestos, limitaciones y conclusiones.', 'use'=>'El texto separa física, funcional, externa e incidencia valuatoria.', 'report'=>'El lector no debe confundir diagnóstico con descuento automático.'],
            ['src'=>'Ayuda interna', 'quote'=>'La escala no es fórmula normativa.', 'use'=>'Solo prioriza revisión y alertas en pantalla.', 'report'=>'No es porcentaje de depreciación ni genera castigo automático.'],
        ];
    }

    public static function valuationGuidance(): array
    {
        return [
            'Si no hay hallazgo: dejarlo dicho en cada bloque y no aplicar descuento por obsolescencia.',
            'Si hay hallazgo leve: documentar la señal; normalmente queda como advertencia si no tiene efecto material.',
            'Si hay hallazgo relevante o crítico: soportar el efecto con visita, fotografías, documento, mercado, comparables, costo de corrección o restricción verificable.',
            'Si se cuantifica: explicar aparte el método usado y la razón técnica; el puntaje de esta pantalla no es el cálculo económico.',
        ];
    }
    public static function defaults(): array { return ['summary_text'=>'', 'diagnosis_text'=>'', 'quantification_text'=>'', 'normative_text'=>'', 'factors'=>[], 'updated_at'=>null]; }
}