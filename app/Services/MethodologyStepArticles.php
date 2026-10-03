<?php
declare(strict_types=1);
namespace App\Services;

final class MethodologyStepArticles
{
    public static function forStep(string $stage, string $method, bool $ph): array
    {
        if ($stage==='plan') return ['Organizar qué se valora', [5,11,14,15,27,...($ph?[36]:[])]];
        if ($stage==='integration' || $stage==='report') return ['Consolidación, alcance e informe', [14,15,27,...($ph?[36]:[])]];
        if ($stage==='decision') return ['Selección de métodos', [15,16,22,27,31]];
        if ($stage==='2') return ['Seleccionar método y delimitar alcance', [15,16,22,27,28,31,...($ph?[36]:[])]];
        $groups = [
            'mercado'=>['1'=>[15,16,19],'2'=>[15,16,19],'3'=>[16,17,18,19],'4'=>[19,20,21],'5'=>[14,21]],
            'costo'=>['1'=>[27,28,29,30],'2'=>[15,27,28],'3'=>[28,29],'4'=>[29,30],'5'=>[14,27]],
            'renta'=>['1'=>[22,23,24,25,26],'2'=>[15,22],'3'=>[23,24,25,26],'4'=>[22,23,24,25,26],'5'=>[14,22]],
            'residual'=>['1'=>[31,32,33,34],'2'=>[15,31],'3'=>[32,33,34],'4'=>[31,32,33,34],'5'=>[14,31]],
        ];
        $step = $stage==='components'?'1':$stage;
        $labels=['1'=>'Academia','2'=>'Método y alcance','3'=>'Insumos y fuentes','4'=>'Análisis','5'=>'Resultado e informe'];
        return [$labels[$step] ?? 'Consulta del paso', array_values(array_unique([...($groups[$method][$step] ?? [15]),...($ph?[36]:[])]))];
    }
}
