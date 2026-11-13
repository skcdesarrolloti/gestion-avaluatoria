<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException,Session};
use App\Models\AppraisalRepository;

final class LeaseAction
{
    public static function save(AppraisalRepository $repo,int $owner,string $id,bool $antecedent=false): never
    {
        $record=$repo->find($id,$owner);
        $key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        $components=MethodologyWorkflow::components($record,$repo->units($id,$owner));
        MethodologyWorkflow::validateKey($key,$components);
        if ($key==='' || (MethodologyWorkflow::saved($record)[$key]['method'] ?? '')!=='renta')
            throw new HttpException(422,'Abre un recorrido activo de Renta.');
        $leaseId=is_string($_POST['lease_id'] ?? null)?$_POST['lease_id']:'';
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
        $redirect='avaluos/'.$id.'/metodologia-valuatoria?method=renta&stage=3&component='.rawurlencode($key)
            .'&lease='.rawurlencode($leaseId).'#renta-concepto';
        try {
            if (!$antecedent) {
                $linked=$_POST['lease_units'] ?? [];
                if (!is_array($linked) || !in_array($components[$key]['unit']['id'],$linked,true))
                    throw new HttpException(422,'El contrato debe incluir la unidad abierta.',['lease_units'=>'Conserva la unidad de este recorrido.']);
            }
            $next=$antecedent?$repo->saveLeaseAntecedent($id,$owner,$version,$_POST['antecedent'] ?? null)
                :$repo->saveLease($id,$owner,$version,$leaseId,$_POST['lease_units'] ?? null,$_POST['lease'] ?? null);
        } catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[422,409],true)) throw $error;
            $values=[];
            $posted=$_POST[$antecedent?'antecedent':'lease'] ?? [];
            foreach (is_array($posted)?$posted:[] as $field=>$value)
                if (is_string($value)) $values[$field]=mb_substr($value,0,1600);
            Session::flash('lease_'.$id,json_encode(['lease_id'=>$leaseId,'antecedent'=>$antecedent,'values'=>$values,
                'units'=>is_array($_POST['lease_units'] ?? null)?array_filter($_POST['lease_units'],'is_string'):[],
                'version'=>$version,'errors'=>$error->errors,'message'=>$error->getMessage()],JSON_THROW_ON_ERROR));
            Http::redirect($redirect);
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect($redirect);
    }
}
