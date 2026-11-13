<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,HttpException,Session};
use App\Models\IncomeOriginRepository;
final class IncomeOriginController
{
    public function __construct(private IncomeOriginRepository $repo,private array $user) {}
    public function save(string $id): never
    {
        $key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        $url='avaluos/'.$id.'/metodologia-valuatoria?stage=3&method=renta&component='.rawurlencode($key).'&renta_paso=evidence#renta-origen';
        try {$result=$this->repo->save($id,$this->user['id'],$key,$_POST);}
        catch (HttpException $e) {
            if (Http::wantsJson() || !in_array($e->status,[422,409],true)) throw $e;
            $old=[];
            foreach (['version','occupation_version'] as $field) if (is_scalar($_POST[$field] ?? null)) $old[$field]=(int)$_POST[$field];
            foreach (['origin','occupation'] as $group) {
                $old[$group]=[];
                foreach (is_array($_POST[$group] ?? null)?$_POST[$group]:[] as $field=>$value)
                    if (is_string($value)) $old[$group][$field]=mb_substr($value,0,1500);
            }
            Session::flash('income_origin_'.$id,json_encode(['old'=>$old,'errors'=>$e->errors,'message'=>$e->getMessage()],JSON_THROW_ON_ERROR));
            Http::redirect($url);
        }
        if (Http::wantsJson()) Http::json($result);
        Http::redirect($url);
    }
}
