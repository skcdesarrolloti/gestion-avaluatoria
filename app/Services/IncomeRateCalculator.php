<?php
declare(strict_types=1);
namespace App\Services;

final class IncomeRateCalculator
{
    public static function all(array $rows,array $adoption): array
    {
        $out=[];
        foreach ($rows as $r) if (($r['method'] ?? '')!=='gordon') $out[$r['id']]=self::one($r);
        foreach ($rows as $r) if (($r['method'] ?? '')==='gordon') {
            $k=$out[$adoption['discount_id'] ?? ''] ?? [];
            $out[$r['id']]=self::one($r,$k['value'] ?? null,$adoption['terminal_growth'] ?? '');
        }
        return $out;
    }
    public static function one(array $r,?float $discount=null,string $growth=''): array
    {
        $errors=[];$v=null;$method=$r['method'] ?? '';$purpose=$r['purpose'] ?? '';$basis=$r['basis'] ?? '';
        $needs=match($method) {'market'=>['income','price'],'build'=>['reference','premium'],'capm'=>['reference','premium','beta'],
            'wacc'=>['equity','debt','debt_weight'],'manual'=>['rate'],'gordon'=>[],default=>['method']};
        foreach (array_merge(['name','purpose','basis','date','source'],$needs) as $f)
            if (($r[$f] ?? '')==='') $errors[]='Falta '.(IncomeValuationFields::rates()[$f]['label'] ?? $f).'.';
        if (!in_array($purpose,['direct','discount','exit'],true)) $errors[]='Define la aplicación de la tasa.';
        if (in_array($method,['build','capm','wacc'],true) && $purpose!=='discount') $errors[]='Esta construcción estima descuento, no capitalización directa o terminal.';
        if ($method==='market' && !in_array($purpose,['direct','exit'],true)) $errors[]='El cociente ingreso/precio es capitalización, no descuento.';
        if ($method==='market' && !in_array($basis,['gross','net'],true)) $errors[]='Identifica ingreso comparable bruto o neto.';
        if ($purpose==='discount' && $basis!=='cash') $errors[]='El descuento corresponde al flujo del activo.';
        if ($purpose==='direct' && !in_array($basis,['gross','net'],true)) $errors[]='La capitalización directa requiere renta bruta o neta.';
        if ($purpose==='exit' && !in_array($basis,['net','cash'],true)) $errors[]='La salida requiere NOI neto o flujo después de reposiciones.';
        if ($method==='gordon' && ($purpose!=='exit' || $basis!=='cash')) $errors[]='La perpetuidad creciente corresponde a salida sobre flujo después de reposiciones.';
        if (!$errors) {
            $n=static fn($f)=>(float)($r[$f] ?? 0);
            if ($method==='market') {
                if ($n('price')<=0 || $n('income')<=0) $errors[]='Precio e ingreso anual deben ser positivos.';
                else $v=$n('income')/$n('price');
            } elseif ($method==='build') $v=($n('reference')+$n('premium'))/100;
            elseif ($method==='capm') $v=($n('reference')+$n('beta')*$n('premium'))/100;
            elseif ($method==='wacc') $v=($n('equity')*(1-$n('debt_weight')/100)+$n('debt')*$n('debt_weight')/100)/100;
            elseif ($method==='manual') $v=$n('rate')/100;
            elseif ($method==='gordon') {
                if ($discount===null || $growth==='') $errors[]='Selecciona descuento válido y crecimiento terminal.';
                else $v=$discount-(float)$growth/100;
            }
        }
        if ($v!==null && (!is_finite($v) || $v<=0)) {$errors[]='La tasa debe ser positiva; en perpetuidad, descuento debe superar crecimiento.';$v=null;}
        return ['value'=>$errors?null:$v,'errors'=>$errors,'purpose'=>$purpose,'basis'=>$basis,'method'=>$method,'name'=>$r['name'] ?? ''];
    }
}
