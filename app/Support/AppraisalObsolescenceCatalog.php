<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalObsolescenceCatalog
{
    public static function groups(): array
    {
        return [
            'fisica' => ['OBS-FIS', 'Obsolescencia física', 'Curabilidad', ['curable'=>'Curable','parcial'=>'Parcialmente curable','no_curable'=>'No curable','nd'=>'No determinado'], [
                'estructura' => 'Estructura', 'cubiertas' => 'Cubiertas, fachadas y cerramientos',
                'instalaciones' => 'Instalaciones y redes', 'acabados' => 'Acabados',
                'mantenimiento' => 'Conservación y mantenimiento']],
            'funcional' => ['OBS-FUN', 'Obsolescencia funcional', 'Origen funcional', ['capital'=>'Exceso de costo de capital','operativo'=>'Exceso de costo operativo','utilidad'=>'Menor productividad / utilidad','uso'=>'Limitación de uso','especializacion'=>'Especialización','tecnologia'=>'Tecnología','otro'=>'Otro'], [
                'distribucion' => 'Distribución', 'dimensiones' => 'Dimensiones', 'flexibilidad' => 'Flexibilidad',
                'adecuacion' => 'Adecuación al uso', 'especializacion' => 'Especialización', 'tecnologia' => 'Tecnología']],
            'externa' => ['OBS-EXT', 'Obsolescencia externa / económica', 'Temporalidad', ['temporal'=>'Temporal','permanente'=>'Permanente','indeterminada'=>'Indeterminada'], [
                'mercado' => 'Mercado / demanda', 'entorno' => 'Entorno', 'accesibilidad' => 'Accesibilidad',
                'ambiental' => 'Ambiental', 'urbanistico' => 'Urbanístico / regulatorio', 'vocacion' => 'Vocación del sector']],
        ];
    }
    public static function scores(): array { return ['na'=>'No aplica al caso', '0'=>'Sin hallazgo', '1'=>'Leve', '2'=>'Relevante', '3'=>'Crítica']; }
    public static function scoreHelp(): array { return [''=>'Pendiente: déjalo así si todavía no revisaste este factor.', 'na'=>'No aplica: úsalo si el factor no corresponde a la tipología o al encargo.', '0'=>'Sin hallazgo: úsalo cuando revisaste el aspecto y no observas problema. Puedes dejar el soporte vacío o escribir “sin hallazgos en visita”.', '1'=>'Leve: hay una señal menor. Basta una nota corta; la foto es opcional.', '2'=>'Relevante: puede afectar uso, negociación o comparación. Escribe el soporte y agrega foto en 3.7 si el hecho es visible.', '3'=>'Crítica: afecta de forma importante. Requiere soporte claro; si se observa en campo, conviene foto en 3.7.']; }
    public static function metaHelp(): array { return [''=>'Si no hay obsolescencia en este bloque, puedes dejar este campo sin seleccionar.', 'curable'=>'Curable: el problema podría corregirse con una intervención razonable.', 'parcial'=>'Parcialmente curable: una intervención ayuda, pero no elimina todo el efecto.', 'no_curable'=>'No curable: no se corrige físicamente o su corrección no es razonable.', 'nd'=>'No determinado: úsalo cuando no hay soporte suficiente para definir curabilidad.', 'capital'=>'Exceso de costo de capital: el diseño o configuración exige más inversión que una alternativa comparable.', 'operativo'=>'Exceso de costo operativo: genera mayores costos de operación o mantenimiento.', 'utilidad'=>'Menor productividad / utilidad: reduce aprovechamiento, renta, ocupación o eficiencia.', 'uso'=>'Limitación de uso: restringe el uso esperado para la tipología.', 'especializacion'=>'Especialización: configuración muy específica que reduce mercado potencial.', 'tecnologia'=>'Tecnología: rezago técnico frente a inmuebles comparables.', 'otro'=>'Otro: describe el origen en el soporte breve.', 'temporal'=>'Temporal: condición externa que puede cambiar en el corto o mediano plazo.', 'permanente'=>'Permanente: condición externa estructural o difícil de remover.', 'indeterminada'=>'Indeterminada: no hay soporte suficiente para definir duración.']; }
    public static function defaults(): array { return ['summary_text'=>'', 'diagnosis_text'=>'', 'quantification_text'=>'', 'normative_text'=>'', 'factors'=>[], 'updated_at'=>null]; }
}