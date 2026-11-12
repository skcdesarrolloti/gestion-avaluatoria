<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Http,HttpException,Session};
use App\Models\{AppraisalRepository,MethodologyWorkflowRepository};

final class IncomeConceptAction
{
    public static function flashKey(string $id,string $key): string
    { return 'income_concept_'.hash('sha256',$id.':'.$key); }

    public static function save(AppraisalRepository $appraisals,MethodologyWorkflowRepository $workflow,int $owner,string $id): never
    {
        $record=$appraisals->find($id,$owner);
        $key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        $components=MethodologyWorkflow::components($record,$appraisals->units($id,$owner));
        MethodologyWorkflow::validateKey($key,$components);
        MethodologyValuationPlan::validateWorkKey($key,$components);
        if ($key==='' || (MethodologyWorkflow::saved($record)[$key]['method'] ?? '')!=='renta')
            throw new HttpException(422,'Selecciona un recorrido activo de Renta.');
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
        try {
            $concept=IncomeConcept::input($_POST['income_concept'] ?? null);
            $next=$workflow->save($id,$owner,$version,$key,['income_concept'=>$concept]);
        } catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[422,409],true)) throw $error;
            $old=[];
            foreach (array_merge(array_keys(IncomeConcept::questions()),array_keys(IncomeConcept::texts())) as $field) {
                $value=$_POST['income_concept'][$field] ?? null;
                if (is_string($value)) $old[$field]=mb_substr($value,0,1100);
            }
            Session::flash(self::flashKey($id,$key),json_encode(['values'=>$old,'errors'=>$error->errors,
                'message'=>$error->getMessage(),'version'=>$version],JSON_THROW_ON_ERROR));
            Http::redirect('avaluos/'.$id.'/metodologia-valuatoria?method=renta&stage=3&component='.rawurlencode($key).'#renta-concepto');
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Session::flash('methodology_message','Borrador conceptual de Renta guardado.');
        Http::redirect('avaluos/'.$id.'/metodologia-valuatoria?method=renta&stage=3&component='.rawurlencode($key).'#renta-concepto');
    }
}
