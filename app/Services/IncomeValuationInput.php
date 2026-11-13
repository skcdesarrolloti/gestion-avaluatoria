<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class IncomeValuationInput
{
    public static function section(string $section,mixed $input): array
    {
        if (!in_array($section,['projection','costs','events','rates','adoption'],true) || !is_array($input))
            throw new HttpException(422,'Revisa los datos de la valoración.');
        $errors=[];$out=[];$list=in_array($section,['costs','events','rates'],true);
        if ($list && count($input)>($section==='events'?600:40)) throw new HttpException(422,'Demasiadas filas en una sección.');
        $fields=$section==='rates'?IncomeValuationFields::rates():IncomeValuationFields::group($section);
        foreach ($list?array_values($input):[$input] as $i=>$row) {
            if (!is_array($row) || array_diff(array_keys($row),array_keys($fields))) throw new HttpException(422,'Se recibieron campos no admitidos.');
            $clean=[];
            foreach ($fields as $key=>$field) {
                $value=$row[$key] ?? ''; $path='data'.($list?'['.$i.']':'').'['.$key.']';
                if (!is_string($value) && !is_int($value) && !is_float($value)) {$errors[$path]='Valor inválido.';continue;}
                $value=trim((string)$value);$type=$field['type'];
                if ($value==='') {$clean[$key]='';continue;}
                if (in_array($type,['money','rate','percent','integer','number'],true)) {
                    // Decimal comma is accepted; thousands separators are deliberately rejected.
                    $n=str_replace(',','.',$value);
                    if (!preg_match('/^-?\d+(?:\.\d{1,8})?$/D',$n) || !is_finite((float)$n)) $errors[$path]='Usa cifras sin separadores de miles; decimal con coma o punto.';
                    elseif ((float)$n<($type==='rate'?-99.99:0) || (float)$n>($type==='money'?1e15:($type==='percent'?100:($type==='integer'?600:1000))) || ($type==='integer' && (float)$n!==(float)(int)$n)) $errors[$path]='Valor fuera del intervalo admitido.';
                    else $clean[$key]=$n;
                } elseif ($type==='select' && !isset($field['options'][$value])) $errors[$path]='Selecciona una opción de la lista.';
                elseif (in_array($type,['date','month'],true) && !self::date($value,$type==='month')) $errors[$path]='Fecha inválida (1900–2199).';
                elseif (in_array($type,['id','lease','candidate'],true) && !preg_match('/^[a-f0-9]{16,32}$/D',$value)) $errors[$path]='Referencia inválida.';
                elseif (mb_strlen($value)>($type==='textarea'?2000:180)) $errors[$path]='Texto demasiado largo.';
                else $clean[$key]=$value;
            }
            $out[]=$clean;
        }
        if ($section==='rates') {
            $ids=array_column($out,'id');if (count($ids)!==count(array_unique($ids)) || in_array('',$ids,true)) $errors['data']='Cada alternativa necesita un identificador único.';
        }
        if ($errors) throw new HttpException(422,'Revisa los campos señalados. El borrador permanece en pantalla.',$errors);
        return $list?$out:($out[0] ?? []);
    }
    public static function date(string $s,bool $month=false): bool
    {
        if (!preg_match($month?'/^(19|20|21)\d{2}-\d{2}$/D':'/^(19|20|21)\d{2}-\d{2}-\d{2}$/D',$s)) return false;
        $d=\DateTimeImmutable::createFromFormat('!Y-m-d',$month?$s.'-01':$s);
        return $d && $d->format($month?'Y-m':'Y-m-d')===$s;
    }
}
