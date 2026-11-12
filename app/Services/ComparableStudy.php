<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ComparableStudy
{
    public static function token(string $method,mixed $operation): string
    { return $method.':'.($method==='renta'?'arriendo':(self::operation($operation) ?: 'venta')); }
    public static function operation(mixed $value): string
    {
        if (!is_string($value)) return '';
        return match(mb_strtolower(trim($value))) {
            'venta'=>'venta', 'arriendo','arrendamiento','alquiler'=>'arriendo', default=>'',
        };
    }
    public static function context(array $record,string $key): array
    {
        if ($key==='') return ['method'=>'','operation'=>'','token'=>'bank'];
        $method=MethodologyWorkflow::saved($record)[$key]['method'] ?? '';
        if (!isset(MethodologyWorkflow::METHODS[$method])) throw new HttpException(422,'Define el método de esta unidad antes de abrir su investigación.');
        $operation=explode(':',self::token($method,$record['tipo_negocio'] ?? ''))[1];
        return ['method'=>$method,'operation'=>$operation,'token'=>$method.':'.$operation];
    }
    public static function identity(array $row): string
    {
        $method=$row['research_method'] ?? '';$operation=$row['research_operation'] ?? '';
        if ($method!=='' || $operation!=='') {
            if (!isset(MethodologyWorkflow::METHODS[$method]) || !in_array($operation,['venta','arriendo'],true)) return '';
            return $method.':'.$operation;
        }
        $operation=self::operation($row['operation'] ?? '');
        if ($operation==='') return '';
        // Legacy evidence is classified only from its explicit operation, never the currently selected method.
        $method=preg_match('/:metodo:(mercado|renta|costo|residual)$/D',(string)($row['component_key'] ?? ''),$match)
            ? $match[1] : ($operation==='venta'?'mercado':'renta');
        return $method.':'.$operation;
    }
    public static function compatible(array $row,array $context): bool
    {
        if ($context['token']==='bank') return true;
        $declared=self::operation($row['operation'] ?? '');
        return self::identity($row)===$context['token'] && ($declared===$context['operation'] || trim((string)($row['operation'] ?? ''))==='');
    }
    public static function rows(array $rows,string $key,array $record): array
    {
        if ($key!=='' && !isset(MethodologyWorkflow::METHODS[MethodologyWorkflow::saved($record)[$key]['method'] ?? ''])) return [];
        $context=self::context($record,$key);
        return array_values(array_filter($rows,static fn($row)=>($row['component_key'] ?? '')===$key && self::compatible($row,$context)));
    }
    public static function assertRequest(array $record,string $key,array $post): array
    {
        $context=self::context($record,$key);
        if (($post['study_scope'] ?? null)!==$context['token']) throw new HttpException(409,'Cambió la investigación o la pantalla es anterior a la separación de ventas y arriendos. Conserva tus cambios y recarga antes de guardar.');
        return $context;
    }
    public static function merge(array $existing,array $incoming,string $key,array $record): array
    {
        $context=self::context($record,$key);
        $current=self::rows($existing,$key,$record);$editable=array_column($current,null,'id');
        $other=array_values(array_filter($existing,static fn($row)=>!isset($editable[$row['id']])));
        $protected=array_column($other,'id');$seen=[];
        foreach ($incoming as &$row) {
            $id=$row['id'] ?? '';
            if (in_array($id,$protected,true) || isset($seen[$id])) throw new HttpException(422,'La muestra pertenece a otra investigación o está repetida. No se guardaron cambios.');
            $seen[$id]=true;$row['component_key']=$key;
            unset($row['research_method'],$row['research_operation']);
            if ($context['token']==='bank') {
                $previous=isset($editable[$id])?self::identity($editable[$id]):'';
                if ($previous!=='') [$row['research_method'],$row['research_operation']]=explode(':',$previous);
                if (!empty($row['research_operation']) && self::operation($row['operation'] ?? '')!==$row['research_operation'])
                    throw new HttpException(422,'La operación está vinculada a su investigación. Conserva la operación original de la muestra.');
            } else {
                $operation=self::operation($row['operation'] ?? '');
                if (trim((string)($row['operation'] ?? ''))!=='' && $operation!==$context['operation'])
                    throw new HttpException(422,'Esta investigación es de '.$context['operation'].'. No cambies una muestra de venta por arriendo ni viceversa.');
                $row['research_method']=$context['method'];$row['research_operation']=$context['operation'];
            }
        }
        unset($row);
        return [...$other,...$incoming];
    }
    public static function historyKey(array $record,string $key): string
    { return $key.'|'.self::context($record,$key)['token']; }
    public static function counts(array $rows,string $key,array $record): array
    {
        $counts=[];
        foreach (['mercado','renta'] as $method) {
            $token=self::token($method,$record['tipo_negocio'] ?? '');
            $context=['token'=>$token,'operation'=>explode(':',$token)[1]];
            $counts[$method]=count(array_filter($rows,static fn($row)=>($row['component_key'] ?? '')===$key && self::compatible($row,$context)));
        }
        return $counts;
    }
}
