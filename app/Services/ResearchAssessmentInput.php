<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ResearchAssessmentInput
{
    public static function input(mixed $items,array $factors): array
    {
        if (!is_array($items) || count($items)>500) throw new HttpException(422,'Revisa las calificaciones de investigación.');
        $out=[];
        foreach ($items as $id=>$values) {
            if ($id!=='subject' && !preg_match('/^[a-f0-9]{32}$/D',(string)$id)) throw new HttpException(422,'Inmueble de calificación inválido.');
            if (!is_array($values) || count($values)>count(ResearchFactorCatalog::all())) throw new HttpException(422,'Revisa los factores calificados.');
            foreach ($values as $key=>$item) {
                if (!isset($factors[$key]) || in_array($key,['area','land','built','destination'],true) || !is_array($item)) throw new HttpException(422,'Califica atributos; el área es base de cálculo y destinación es filtro.');
                $out[$id][$key]=[];
                foreach (['value'=>120,'support'=>600,'basis'=>64,'scale_kind'=>20,'scale_categories'=>1200] as $field=>$max) {
                    $text=$item[$field] ?? '';
                    if (!is_string($text) || mb_strlen($text)>$max) throw new HttpException(422,'Revisa dato, escala y soporte de la calificación.');
                    $out[$id][$key][$field]=trim($text);
                }
                if ($out[$id][$key]['value']!=='' && !preg_match('/^[a-f0-9]{64}$/D',$out[$id][$key]['basis'])) throw new HttpException(422,'Recarga la información antes de calificar.');
                $factor=$factors[$key]; $value=$out[$id][$key]['value'];
                if ($value==='') continue;
                // Retain historical assessments after an explicit scale change, but never reinterpret them.
                if ($out[$id][$key]['scale_kind']!==$factor['kind'] || $out[$id][$key]['scale_categories']!==$factor['categories']) continue;
                $valid=$factor['kind']==='numeric'?preg_match('/^\d+(?:[.,]\d+)?$/D',$value):in_array($value,explode("\n",$factor['categories']),true);
                if (!$valid) throw new HttpException(422,'La calificación no corresponde a la escala del factor.');
            }
        }
        return $out;
    }
    public static function validateScope(array $plan,array $rows,array $previous=[]): void
    {
        $ids=array_keys(ComparableIntake::groups($rows));
        foreach ($plan['assessments'] ?? [] as $id=>$values) {
            if ($id==='subject' || in_array($id,$ids,true)) continue;
            if ($values!==($previous['assessments'][$id] ?? null)) throw new HttpException(422,'La calificación pertenece a otro inmueble o a un grupo que cambió.');
        }
    }
}
