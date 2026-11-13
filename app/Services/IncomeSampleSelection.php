<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class IncomeSampleSelection
{
    public const REASONS=['error'=>'Error de dato u operación','different'=>'Inmueble o modalidad no comparable',
        'duplicate'=>'Anuncio duplicado del mismo inmueble','evidence'=>'Evidencia insuficiente o contradictoria',
        'other'=>'Otra razón técnica','restore'=>'Reincorporación tras revisión'];

    public static function rows(array $source): array
    {
        return array_map(static function(array $r): array {
            $price=is_numeric($r['price_amount'] ?? null)?(float)$r['price_amount']:null;
            $area=is_numeric($r['area_m2'] ?? null)?(float)$r['area_m2']:null;$blocked=[];
            if (ComparableStudy::operation($r['operation'] ?? '')!=='arriendo') $blocked[]='Operación sin confirmar';
            if (preg_match('~(?:^|[/_-])venta(?:[/_? -]|$)~iu',(string)($r['source_url'] ?? ''))) $blocked[]='Enlace de venta';
            if (($r['price_unit'] ?? '')!=='canon_mensual') $blocked[]='Falta confirmar canon mensual total';
            if ($price===null || $price<=0) $blocked[]='Falta importe positivo';
            if ($area===null || $area<=0) $blocked[]='Falta área positiva';
            if (($r['active'] ?? '')==='no' || ($r['capture_confirmation'] ?? '')==='excluded' || ($r['intake_state'] ?? '')==='not_selected') $blocked[]='No participa en captura';
            $text=mb_strtolower((string)($r['published_text'] ?? '').' '.($r['evidence_detail'] ?? ''));
            $included=preg_match('/arriendo con administraci[oó]n|administraci[oó]n incluida|canon incluye administraci[oó]n/u',$text);
            $services=preg_match('/cowork|regus|oficina spaces|espacio compartido|para \d+ persona/u',$text);
            return ['id'=>$r['id'],'label'=>'Muestra '.($r['sample_index'] ?? '').' · '.($r['source_name'] ?? ''),
                'url'=>(string)($r['source_url'] ?? ''),'code'=>(string)($r['listing_code'] ?? ''),
                'price'=>$price,'area'=>$area,'value'=>$blocked?null:$price/$area,'blocked'=>$blocked,
                'condition'=>$services?'Servicios / coworking / por personas':($included?'Administración incluida':'Administración sin aclarar'),
                'ph'=>(string)($r['ph_regime'] ?? ''),'verification'=>(string)($r['verification_detail'] ?? '')];
        },$source);
    }

    public static function report(array $rows,array $state): array
    {
        $eligible=array_values(array_filter($rows,static fn($r)=>$r['value']!==null));
        $kept=array_values(array_filter($eligible,static fn($r)=>empty($state['excluded'][$r['id']])));
        return ['baseline'=>IncomeSampleStatistics::summarize($eligible),'current'=>IncomeSampleStatistics::summarize($kept),
            'total'=>count($rows),'blocked'=>count($rows)-count($eligible),'excluded'=>count($eligible)-count($kept),
            'ids'=>array_column($kept,'id')];
    }

    public static function input(array $post): array
    {
        $out=[];$errors=[];
        foreach (['sample'=>32,'category'=>30,'reason'=>1600,'support'=>1600] as $key=>$max) {
            $v=$post[$key] ?? '';
            if (!is_string($v) || mb_strlen($v)>$max) $errors[$key]='Revisa el dato: máximo '.$max.' caracteres.';
            else $out[$key]=trim($v);
        }
        $out['operation']=$post['decision'] ?? $post['operation'] ?? 'draft';
        if (!is_string($out['operation']) || !in_array($out['operation'],['draft','exclude','restore'],true)) $errors['operation']='Decisión inválida.';
        if (!preg_match('/^[a-f0-9]{32}$/D',$out['sample'] ?? '')) $errors['sample']='Selecciona una muestra.';
        if (($out['category'] ?? '')!=='' && !isset(self::REASONS[$out['category']])) $errors['category']='Selecciona una razón técnica.';
        if ($out['operation']!=='draft') foreach (['category','reason','support'] as $key)
            if (($out[$key] ?? '')==='') $errors[$key]='Completa este campo para dejar constancia de la decisión.';
        if ($errors) throw new HttpException(422,'Completa la justificación y su soporte.',$errors);
        return $out;
    }

    public static function change(array $state,array $rows,array $input,array $actor,string $time): array
    {
        $id=$input['sample'];$index=array_column($rows,null,'id');
        if (!isset($index[$id])) throw new HttpException(422,'La muestra no pertenece a esta investigación.');
        $draft=array_intersect_key($input,array_flip(['category','reason','support']));
        $state['drafts'][$id]=$draft;$state['last_sample']=$id;
        if ($input['operation']==='draft') return $state;
        $excluded=!empty($state['excluded'][$id]);
        if (($input['operation']==='exclude')===$excluded) throw new HttpException(409,'La muestra ya tiene esa decisión. Actualiza la pantalla.');
        $before=self::report($rows,$state);$hash=hash('sha256',json_encode($rows,JSON_THROW_ON_ERROR));
        if ($input['operation']==='exclude') $state['excluded'][$id]=true;else unset($state['excluded'][$id]);
        $after=self::report($rows,$state);
        $state['datasets'][$hash] ??= $rows;
        $state['events'][]=$draft+['sample'=>$id,'operation'=>$input['operation'],'at'=>$time,
            'actor'=>['id'=>$actor['id'],'analyst_id'=>$actor['analyst_id'] ?? null,'name'=>$actor['name']],
            'dataset'=>$hash,'before'=>$before['current'],'after'=>$after['current'],
            'before_ids'=>$before['ids'],'after_ids'=>$after['ids']];
        unset($state['drafts'][$id]);
        return $state;
    }
}
