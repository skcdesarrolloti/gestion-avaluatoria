<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Database,Http,HttpException};
use App\Models\{AppraisalRepository,AppraisalComparableRepository,ComparableAreaRepository};

final class ComparableAreaAction
{
    public static function show(AppraisalRepository $appraisals,AppraisalComparableRepository $comparables,int $owner,string $id): void
    {
        header('Cache-Control: no-store');$record=$appraisals->find($id,$owner);
        $key=is_string($_GET['component'] ?? null)?$_GET['component']:'';
        $components=MethodologyWorkflow::components($record,$appraisals->units($id,$owner));
        MethodologyValuationPlan::validateWorkKey($key,$components);
        $study=ComparableStudy::context($record,$key);
        $rows=MethodologyComparableScope::rows($comparables->forAppraisal($id,$owner),$key,$record);
        $portals=array_values(array_unique(array_column($rows,'source_name')));sort($portals);
        $portal=is_string($_GET['portal'] ?? null)?$_GET['portal']:'';
        if ($portal!=='') $rows=array_values(array_filter($rows,static fn($r)=>$r['source_name']===$portal));
        $total=count($rows);$pages=max(1,(int)ceil($total/10));$page=max(1,min($pages,(int)($_GET['page'] ?? 1)));
        $rows=array_slice($rows,($page-1)*10,10);
        view('appraisals/comparable-areas',['title'=>'Completar áreas de los anuncios','record'=>$record,'componentKey'=>$key,
            'componentLabel'=>$components[$key]['label'] ?? 'Banco de muestras','study'=>$study,'rows'=>$rows,'total'=>$total,
            'page'=>$page,'pages'=>$pages,'portal'=>$portal,'portals'=>$portals]);
    }
    public static function save(AppraisalRepository $appraisals,int $owner,string $id): never
    {
        $record=$appraisals->find($id,$owner);$key=is_string($_POST['component_scope'] ?? null)?$_POST['component_scope']:'';
        MethodologyValuationPlan::validateWorkKey($key,MethodologyWorkflow::components($record,$appraisals->units($id,$owner)));
        $next=(new ComparableAreaRepository(Database::connection()))->save($id,$owner,$key,$_POST);
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '','application/json')) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect('avaluos/'.$id.'/metodologia-valuatoria/comparables/areas?component='.rawurlencode($key));
    }
}
