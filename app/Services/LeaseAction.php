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
            .'&lease='.rawurlencode($leaseId).'&lease_year='.LeaseMonthlyHistory::year($_POST['lease_year'] ?? null,[])
            .'&renta_paso='.(($_POST['return_step'] ?? '')==='evidence'?'evidence':'concept').'#historial-arriendo';
        try {
            if (!$antecedent && isset($_POST['lease_payload'])) {
                if (!is_string($_POST['lease_payload']) || strlen($_POST['lease_payload'])>1500000) throw new HttpException(422,'Ficha demasiado extensa.');
                $payload=json_decode($_POST['lease_payload'],true);
                if (!is_array($payload)) throw new HttpException(422,'No se pudo leer el borrador del arriendo.');
                $_POST['lease']=$payload;
            }
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
            if (!$antecedent && is_array($posted['monthly_history'] ?? null)) {
                foreach (array_slice($posted['monthly_history'],0,12,true) as $month=>$row) {
                    if (!is_string($month) || !preg_match('/^(19|20|21)\d{2}-(0[1-9]|1[0-2])$/D',$month) || !is_array($row)) continue;
                    foreach (LeaseMonthlyHistory::fields() as $field=>$definition)
                        if (is_string($row[$field] ?? null)) $values['monthly_history'][$month][$field]=mb_substr($row[$field],0,1600);
                }
            }
            foreach (['rent_adjustments','admin_adjustments','vacancy_periods'] as $group)
                if (!$antecedent && is_array($posted[$group] ?? null)) $values[$group]=$posted[$group];
            Session::flash('lease_'.$id,json_encode(['lease_id'=>$leaseId,'antecedent'=>$antecedent,'values'=>$values,
                'units'=>is_array($_POST['lease_units'] ?? null)?array_filter($_POST['lease_units'],'is_string'):[],
                'version'=>$version,'errors'=>$error->errors,'message'=>$error->getMessage()],JSON_THROW_ON_ERROR));
            Http::redirect($redirect);
        }
        if (Http::wantsJson()) {
            $record=$repo->find($id,$owner);
            Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c'),
                'income_context'=>IncomeValuationContext::build($record,$components[$key])]);
        }
        Http::redirect($redirect);
    }
}
