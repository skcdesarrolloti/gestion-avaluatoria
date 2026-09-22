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
    public static function defaults(): array { return ['summary_text'=>'', 'diagnosis_text'=>'', 'quantification_text'=>'', 'normative_text'=>'', 'factors'=>[], 'updated_at'=>null]; }
}