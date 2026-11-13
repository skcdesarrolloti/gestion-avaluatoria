<?php
declare(strict_types=1);
namespace App\Services;

/** Descriptive statistics only: no adoption of rent or property value. */
final class IncomeSampleStatistics
{
    public static function summarize(array $rows): array
    {
        $values=array_column($rows,'value');sort($values,SORT_NUMERIC);$n=count($values);
        $empty=['n'=>$n,'mean'=>null,'median'=>null,'sd'=>null,'cv'=>null,'q1'=>null,'q3'=>null,'flags'=>[]];
        if (!$n) return $empty;
        $mean=array_sum($values)/$n;$sum=array_sum(array_map(static fn($v)=>($v-$mean)**2,$values));
        $sd=$n>1?sqrt($sum/($n-1)):null;
        $q1=self::quantile($values,.25);$q3=self::quantile($values,.75);$median=self::quantile($values,.5);
        $deviations=array_map(static fn($v)=>abs($v-$median),$values);sort($deviations,SORT_NUMERIC);
        $mad=self::quantile($deviations,.5);$flags=[];
        foreach ($rows as $row) {
            $v=$row['value'];$reasons=[];
            if ($v<$q1-1.5*($q3-$q1) || $v>$q3+1.5*($q3-$q1)) $reasons[]='Cuartiles (1,5 RIC)';
            if ($mad>0 && abs(.6744897501960817*($v-$median)/$mad)>3.5) $reasons[]='Z robusto > 3,5';
            if ($n>=3 && $sd>0 && $n*self::normalTail(abs($v-$mean)/$sd)<.5) $reasons[]='Chauvenet';
            if ($reasons) $flags[$row['id']]=$reasons;
        }
        return ['n'=>$n,'mean'=>$mean,'median'=>$median,'sd'=>$sd,'cv'=>$sd!==null && $mean!=0?100*$sd/abs($mean):null,
            'q1'=>$q1,'q3'=>$q3,'flags'=>$flags];
    }

    private static function quantile(array $values,float $q): float
    { $i=(count($values)-1)*$q;$lo=(int)floor($i);return $values[$lo]+($values[(int)ceil($i)]-$values[$lo])*($i-$lo); }

    /** Two-sided standard-normal tail, Abramowitz-Stegun 7.1.26 (absolute error < 1.5e-7). */
    public static function normalTail(float $z): float
    {
        $x=abs($z)/sqrt(2);$t=1/(1+.3275911*$x);
        return max(0,min(1,((((1.061405429*$t-1.453152027)*$t+1.421413741)*$t-.284496736)*$t+.254829592)*$t*exp(-$x*$x)));
    }
}
