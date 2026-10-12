<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ComparableCourseExecution
{
    public static function normalize(string $text): string
    {
        if ($text==='') return '';
        $v=json_decode($text,true,8);
        if (!is_array($v) || ($v['v']??null)!==1 || !in_array($v['basis']??null,['offer','adjusted'],true)
            || !in_array($v['confidence']??null,[.9,.95,.99],true) || !is_int($v['step']??null) || $v['step']<0 || $v['step']>6
            || !is_array($v['result']??null)) self::invalid();
        $r=$v['result'];
        if (!is_string($r['at']??null) || strtotime($r['at'])===false || strlen($r['at'])>40
            || !in_array($r['basis']??null,['offer','adjusted'],true) || !is_bool($r['simulated']??null)
            || !is_string($r['stamp']??null) || strlen($r['stamp'])>40000 || !is_array($r['rows']??null)
            || !array_is_list($r['rows']) || count($r['rows'])<2 || count($r['rows'])>160 || !is_array($r['summary']??null)) self::invalid();
        $ids=[];
        foreach ($r['rows'] as $row) {
            if (!is_array($row) || !preg_match('/^[a-f0-9]{32}$/D',(string)($row['id']??'')) || isset($ids[$row['id']])) self::invalid();
            $ids[$row['id']]=true;
            foreach (['y','area','offer','discount','final'] as $key) if (!array_key_exists($key,$row) || ($row[$key]!==null && (!is_numeric($row[$key]) || !is_finite((float)$row[$key])))) self::invalid();
            foreach (['label','areaNote','regime'] as $key) if (!is_string($row[$key]??null) || mb_strlen($row[$key])>1600) self::invalid();
            if (!is_array($row['reasons']??null) || !array_is_list($row['reasons']) || count($row['reasons'])>10) self::invalid();
            foreach ($row['reasons'] as $reason) if (!is_string($reason) || mb_strlen($reason)>500) self::invalid();
        }
        foreach ($r['summary'] as $key=>$value) {
            if (!in_array($key,['n','mean','median','sd','cv','skew','kurt','q1','q3','lower','upper','min','max','range','variance','mad','confidence','t','se','margin','lowerCI','upperCI','relativeMargin','estimators','modes'],true)) self::invalid();
            if (in_array($key,['estimators','modes'],true)) continue;
            if ($value!==null && (!is_int($value)&&!is_float($value) || !is_finite((float)$value))) self::invalid();
        }
        if (!in_array($r['summary']['confidence']??null,[.9,.95,.99],true) || !is_array($r['summary']['estimators']??null) || count($r['summary']['estimators'])!==4) self::invalid();
        foreach ($r['summary']['estimators'] as $e) {
            if (!is_array($e) || !in_array($e['label']??null,['Media','Mediana','Media acotada (40% total)','Media geométrica'],true)) self::invalid();
            foreach (['value','mape'] as $key) if (!array_key_exists($key,$e) || ($e[$key]!==null && (!is_numeric($e[$key]) || !is_finite((float)$e[$key])))) self::invalid();
        }
        if (!is_array($r['summary']['modes']??null) || !array_is_list($r['summary']['modes']) || count($r['summary']['modes'])>160) self::invalid();
        foreach ($r['summary']['modes'] as $number) if (!is_int($number)&&!is_float($number) || !is_finite((float)$number)) self::invalid();
        if (!is_array($v['notes']??null) || count($v['notes'])>160 || !is_string($v['conclusion']??null) || mb_strlen($v['conclusion'])>6000) self::invalid();
        foreach ($v['notes'] as $id=>$note) if (!isset($ids[$id]) || !is_string($note) || mb_strlen($note)>1600) self::invalid();
        if (!is_bool($r['reconstructed']??false)) self::invalid();
        if (($r['bootstrap']??null)!==null) {
            $b=$r['bootstrap'];
            if (!is_array($b) || ($b['repetitions']??null)!==10000 || ($b['seed']??null)!==2026) self::invalid();
            foreach (['mean','median','cv'] as $key) {
                if (!is_array($b[$key]??null) || count($b[$key])!==2) self::invalid();
                foreach ($b[$key] as $number) if ($number!==null && (!is_numeric($number) || !is_finite((float)$number))) self::invalid();
            }
        }
        $v['notes']=(object)$v['notes'];
        return json_encode($v,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    }
    private static function invalid(): never { throw new HttpException(422,'Ejecución estadística inválida. Se conserva el registro anterior.'); }
}
