<?php
declare(strict_types=1);
namespace App\Services;

final class IncomeOperatingCosts
{
    public static function errors(array $state): array
    {
        $p=$state['projection'] ?? [];$errors=[];
        foreach (['vacancy','arrears','loss_support','cost_review','cost_support'] as $f)
            if (($p[$f] ?? '')==='') $errors[]='Completa '.IncomeValuationFields::group('projection')[$f]['label'].'.';
        foreach ($state['costs'] ?? [] as $i=>$c) {
            $fields=['name','payer','source'];
            if (($c['payer'] ?? '')==='owner') $fields=array_merge($fields,['amount','basis','growth']);
            foreach ($fields as $f) if (($c[$f] ?? '')==='') $errors[]='Costo '.($i+1).': falta '.IncomeValuationFields::group('costs')[$f]['label'].'.';
            if (in_array($c['basis'] ?? '',['potential','effective'],true) && (float)($c['amount'] ?? 0)>100) $errors[]='Costo '.($i+1).': el porcentaje no puede superar 100.';
        }
        return $errors;
    }
    public static function month(float $potential,array $p,array $costs,float $fraction=1,int $year=0): array
    {
        $vacancy=$potential*(float)$p['vacancy']/100;$after=$potential-$vacancy;
        $arrears=$after*(float)$p['arrears']/100;$effective=$after-$arrears;$expenses=0;$detail=[];
        foreach ($costs as $c) {
            $amount=0;
            if ($c['payer']==='owner') {
                $base=match($c['basis']) {'monthly'=>$fraction,'annual'=>$fraction/12,'potential'=>$potential/100,'effective'=>$effective/100};
                $amount=(float)$c['amount']*$base*pow(1+(float)$c['growth']/100,$year);
            }
            $detail[]=['name'=>$c['name'],'amount'=>$amount];$expenses+=$amount;
        }
        return ['potential'=>$potential,'vacancy'=>$vacancy,'arrears'=>$arrears,'effective'=>$effective,'costs'=>$expenses,'noi'=>$effective-$expenses,'detail'=>$detail];
    }
}
