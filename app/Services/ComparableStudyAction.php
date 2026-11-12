<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException};
use App\Models\{AppraisalRepository,AppraisalComparableRepository};

final class ComparableStudyAction
{
    public static function persist(AppraisalRepository $appraisals,AppraisalComparableRepository $comparables,int $owner,string $id): int
    {
        $record=$appraisals->find($id,$owner);
        if (($_POST['matrix_complete'] ?? '')!=='1') throw new HttpException(422,'El envío de la matriz llegó incompleto; no se guardaron cambios.');
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Falta la versión de la matriz. Recarga antes de guardar.');
        $key=is_string($_POST['component_scope'] ?? null)?$_POST['component_scope']:'';
        MethodologyValuationPlan::validateWorkKey($key,MethodologyWorkflow::components($record,$appraisals->units($id,$owner)));
        ComparableStudy::assertRequest($record,$key,$_POST);
        $rows=ComparableStudy::merge($comparables->forAppraisal($id,$owner),AppraisalComparableInput::rows($_POST),$key,$record);
        return $comparables->saveAll($id,$owner,$rows,$version);
    }
    public static function assign(AppraisalRepository $appraisals,AppraisalComparableRepository $comparables,int $owner,string $id): never
    {
        $record=$appraisals->find($id,$owner);
        $key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        MethodologyValuationPlan::validateWorkKey($key,MethodologyWorkflow::components($record,$appraisals->units($id,$owner)));
        $context=ComparableStudy::context($record,$key);
        $sourceStudy=$_POST['study_scope'] ?? '';
        if (!is_string($sourceStudy) || $sourceStudy==='') throw new HttpException(409,'Recarga para identificar la investigación antes de reasignar.');
        $source=is_string($_POST['source_scope'] ?? null)?$_POST['source_scope']:'';
        if ($source===$key) throw new HttpException(422,'Selecciona un destino distinto.');
        $ids=$_POST['samples'] ?? [];
        if (!is_array($ids) || $ids===[]) throw new HttpException(422,'Marca las muestras que deseas asignar.');
        $rows=$comparables->forAppraisal($id,$owner);$found=[];
        foreach ($rows as &$row) if (in_array($row['id'],$ids,true)) {
            if (($row['component_key'] ?? '')!==$source) throw new HttpException(409,'La muestra cambió de componente. Recarga para revisar.');
            if ($sourceStudy!=='bank' && ComparableStudy::identity($row)!==$sourceStudy) throw new HttpException(409,'La muestra pertenece a otra investigación.');
            if (!ComparableStudy::compatible($row,$context)) throw new HttpException(422,'El destino pertenece a otra investigación. Las ventas y los arriendos se conservan separados.');
            $identity=ComparableStudy::identity($row);
            if ($identity!=='') [$row['research_method'],$row['research_operation']]=explode(':',$identity);
            $row['component_key']=$key;$found[]=$row['id'];
        }
        unset($row);
        if (count(array_unique($ids))!==count($found)) throw new HttpException(422,'Una muestra no pertenece al expediente.');
        $comparables->saveAll($id,$owner,$rows,(int)($_POST['version'] ?? -1));
        Http::redirect('avaluos/'.$id.'/metodologia-valuatoria?component='.rawurlencode($key).'&stage=3');
    }
}
