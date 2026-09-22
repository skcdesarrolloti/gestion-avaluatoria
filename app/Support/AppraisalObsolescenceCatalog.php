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
    public static function scores(): array { return ['na'=>'N/A · No aplica', '0'=>'0 · Sin hallazgo', '1'=>'1 · Leve', '2'=>'2 · Relevante', '3'=>'3 · Crítica']; }
    public static function scoreHelp(): array { return [''=>'Pendiente: todavía no revisado.', 'na'=>'No aplica: no corresponde al caso.', '0'=>'Sin hallazgo: revisado y funciona razonablemente.', '1'=>'Leve: señal menor, sin efecto material claro.', '2'=>'Relevante: puede afectar uso, comparación o negociación.', '3'=>'Crítica: afecta de forma importante y requiere soporte claro.']; }
    public static function metaHelp(): array { return [''=>'Es una conclusión general del bloque; no aplica a los factores marcados sin hallazgo ni modifica el IEO.', 'curable'=>'Curable: el hallazgo predominante podría corregirse con una intervención razonable.', 'parcial'=>'Parcialmente curable: una intervención ayuda al hallazgo predominante, pero no elimina todo el efecto.', 'no_curable'=>'No curable: el hallazgo predominante no se corrige físicamente o su corrección no es razonable.', 'nd'=>'No determinado: úsalo cuando no hay soporte suficiente para definir curabilidad.', 'capital'=>'Exceso de costo de capital: el diseño o configuración exige más inversión que una alternativa comparable.', 'operativo'=>'Exceso de costo operativo: genera mayores costos de operación o mantenimiento.', 'utilidad'=>'Menor productividad / utilidad: reduce aprovechamiento, renta, ocupación o eficiencia.', 'uso'=>'Limitación de uso: restringe el uso esperado para la tipología.', 'especializacion'=>'Especialización: configuración muy específica que reduce mercado potencial.', 'tecnologia'=>'Tecnología: rezago técnico frente a inmuebles comparables.', 'otro'=>'Otro: describe el origen en el soporte breve.', 'temporal'=>'Temporal: condición externa que puede cambiar en el corto o mediano plazo.', 'permanente'=>'Permanente: condición externa estructural o difícil de remover.', 'indeterminada'=>'Indeterminada: no hay soporte suficiente para definir duración.']; }

    public static function factorHelp(): array
    {
        return [
            'fisica' => ['estructura'=>'Elementos portantes, estabilidad aparente, fisuras, asentamientos o señales que comprometan vida útil.', 'cubiertas'=>'Protección frente a intemperie: cubierta, fachada, cerramientos, filtraciones, humedad o deterioro visible.', 'instalaciones'=>'Redes eléctricas, hidrosanitarias, voz/datos, gas, equipos y suficiencia técnica frente al uso.', 'acabados'=>'Vigencia y estado de pisos, muros, cielos, carpinterías y terminaciones frente a comparables.', 'mantenimiento'=>'Conservación general, rutinas de mantenimiento, reparaciones diferidas y estado observable del inmueble.'],
            'funcional' => ['distribucion'=>'Relación entre espacios, circulaciones, accesos, operación interna y facilidad de uso para la actividad valuada.', 'dimensiones'=>'Área, frente, fondo, altura, proporciones y capacidad física frente a lo esperado por mercado.', 'flexibilidad'=>'Capacidad de adaptarse a otros usuarios, divisiones, ampliaciones o cambios sin obras desproporcionadas.', 'adecuacion'=>'Compatibilidad entre diseño actual, uso permitido, tipología y operación real del inmueble.', 'especializacion'=>'Grado en que el inmueble fue hecho para un usuario o actividad específica y reduce mercado alternativo.', 'tecnologia'=>'Rezago o suficiencia de sistemas técnicos, conectividad, automatización, eficiencia y soporte operativo.'],
            'externa' => ['mercado'=>'Condiciones de oferta, demanda, absorción, vacancia, competencia y apetito por esta tipología.', 'entorno'=>'Calidad urbana, vecindario, mezcla de usos, imagen, seguridad percibida y compatibilidad del sector.', 'accesibilidad'=>'Ingreso, transporte, conectividad vial, parqueo, maniobra y facilidad para usuarios o visitantes.', 'ambiental'=>'Ruido, olores, inundación, contaminación, restricciones ambientales o externalidades físicas del entorno.', 'urbanistico'=>'Normativa, usos permitidos, cargas, restricciones, licencias, afectaciones o cambios regulatorios.', 'vocacion'=>'Coherencia entre el inmueble y la tendencia del sector: consolidación, cambio de uso o pérdida de atractivo.'],
        ];
    }
    public static function defaults(): array { return ['summary_text'=>'', 'diagnosis_text'=>'', 'quantification_text'=>'', 'normative_text'=>'', 'factors'=>[], 'updated_at'=>null]; }
}