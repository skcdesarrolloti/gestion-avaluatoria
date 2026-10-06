<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ComparableAnalysisReview
{
    public static function normalize(mixed $items): array
    {
        if (!is_array($items) || count($items)>160) throw new HttpException(422,'Historial de depuración inválido.');
        $out=[];
        foreach ($items as $item) {
            if (!is_object($item) || !is_string($item->id ?? null) || !preg_match('/^[a-f0-9]{32}$/D',$item->id)
                || !in_array($item->reason ?? null,['other','unknown'],true)) throw new HttpException(422,'Muestra del historial inválida.');
            foreach (['at','restored_at'] as $key) {
                $value=$item->$key ?? null;
                if (!is_string($value) || ($value==='' && $key==='at') || ($value!=='' &&
                    (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/D',$value) || strtotime($value)===false)))
                    throw new HttpException(422,'Fecha de depuración inválida.');
            }
            $out[$item->id]=['id'=>$item->id,'reason'=>$item->reason,'at'=>$item->at,'restored_at'=>$item->restored_at];
        }
        return array_values($out);
    }
}
