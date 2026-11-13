<?php
declare(strict_types=1);
namespace App\Services;
use DateTimeImmutable as Date;

final class IncomeCashFlow
{
    public static function forecast(array $s,array $source,float $discount): array
    {
        $p=$s['projection'];$origin=new Date($p['date']);$cursor=$origin->modify('+1 day');$rows=[];
        $events=[];foreach ($s['events'] ?? [] as $e) $events[$e['month']]=$e;
        for ($i=0;$i<(int)$p['months'];$i++) {
            $end=$cursor->modify('last day of this month');$days=(int)$cursor->format('t');
            $fraction=((int)$end->format('d')-(int)$cursor->format('d')+1)/$days;
            $potential=0;
            for ($d=$cursor;$d<=$end;$d=$d->modify('+1 day')) $potential+=self::rentAt($d,$p,$source)/$days;
            $event=$events[$end->format('Y-m')] ?? [];
            if (($event['rent'] ?? '')!=='') $potential=(float)$event['rent']*$fraction;
            $year=intdiv($i,12);$r=IncomeOperatingCosts::month($potential,$p,$s['costs'] ?? [],$fraction,$year);
            $r['extra']=(float)($event['extra'] ?? 0);$r['capex']=(float)($event['capex'] ?? 0);
            $r['noi']-=$r['extra'];$r['cash']=$r['noi']-$r['capex'];
            $r['date']=$end->format('Y-m-d');$r['month']=$end->format('Y-m');$r['fraction']=$fraction;
            $r['time']=(int)$origin->diff($end)->format('%a')/365;
            $r['pv']=$r['cash']/pow(1+$discount,$r['time']);$r['event']=$event['source'] ?? '';
            $rows[]=$r;$cursor=$end->modify('+1 day');
        }
        $last=end($rows);$lastDate=new Date($last['date']);
        // Terminal stabilization excludes extraordinary events; it is explicitly reviewed in adoption.
        $stabilized=IncomeOperatingCosts::month(self::rentAt($lastDate,$p,$source),$p,$s['costs'] ?? [],1,intdiv(count($rows)-1,12));
        return ['months'=>$rows,'pv_flows'=>array_sum(array_column($rows,'pv')),'terminal_noi'=>$stabilized['noi']*12,'terminal_time'=>$last['time']];
    }
    public static function rentAt(Date $day,array $p,array $source): float
    {
        $rent=$source['rent'];$lease=$source['lease'];$date=$day->format('Y-m-d');
        if ($lease) {
            if ($date<$lease['start']) return 0;
            if ($date>$lease['end']) {
                if ($p['after_contract']!=='market' || $date<$p['relet_date']) return 0;
                $rent=(float)$p['market_rent'];
            }
        }
        $n=0;$first=$p['next_increase'] ?? '';
        if ($first!=='' && $date>=$first) {
            $firstDate=new Date($first);$years=(int)$day->format('Y')-(int)$firstDate->format('Y');
            $anniversary=self::anniversary($firstDate,(int)$day->format('Y'));
            $n=$years+($day>=$anniversary?1:0);
        }
        return $rent*pow(1+(float)$p['growth']/100,$n);
    }
    private static function anniversary(Date $first,int $year): Date
    {
        $month=(int)$first->format('m');$base=new Date(sprintf('%04d-%02d-01',$year,$month));
        return $base->setDate($year,$month,min((int)$first->format('d'),(int)$base->format('t')));
    }
}
