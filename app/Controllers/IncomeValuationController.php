<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,HttpException,Session};
use App\Models\{AppraisalRepository,IncomeValuationRepository};
use App\Services\{MethodologyWorkflow,ComparableStudy,IncomeValuationContext};

final class IncomeValuationController
{
    public function __construct(private AppraisalRepository $appraisals,private IncomeValuationRepository $valuation,private array $user) {}
    public function save(string $id): never
    {
        $this->appraisals->find($id,$this->user['id']);$key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        $step=in_array($_POST['return_step'] ?? '',['evidence','valuation','review'],true)?$_POST['return_step']:'valuation';
        $url='avaluos/'.$id.'/metodologia-valuatoria?stage=3&method=renta&component='.rawurlencode($key).'&renta_paso='.$step;
        try { $saved=$this->valuation->save($id,$this->user,$key,$_POST); }
        catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[422,409],true)) throw $error;
            $old=array_intersect_key($_POST,array_flip(['data','payload','version','context_hash']));
            Session::flash('income_valuation_'.$id.'_'.$key,json_encode(['old'=>$old,'message'=>$error->getMessage(),'errors'=>$error->errors],JSON_THROW_ON_ERROR));
            Http::redirect($url);
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$saved['version'],'saved_at'=>gmdate('c')]);
        Http::redirect($url);
    }
    public function export(string $id): never
    {
        $record=$this->appraisals->find($id,$this->user['id']);$key=is_string($_GET['component'] ?? null)?$_GET['component']:'';
        $components=MethodologyWorkflow::components($record,$this->appraisals->units($id,$this->user['id']));
        MethodologyWorkflow::validateKey($key,$components);
        if ($key==='' || ComparableStudy::context($record,$key)['token']!=='renta:arriendo') throw new HttpException(422,'Abre el recorrido de Renta.');
        $state=MethodologyWorkflow::saved($record)[$key]['income_valuation'] ?? [];$context=IncomeValuationContext::build($record,$components[$key]);
        header('Content-Disposition: attachment; filename="memoria-calculos-renta.json"');
        Http::json(['expediente'=>$id,'recorrido'=>$key,'study'=>'renta:arriendo','exported_at'=>gmdate('c'),
            'current'=>isset($state['calculation']) && $state['calculation']['fingerprint']===IncomeValuationContext::fingerprint($state,$context),'valuation'=>$state]);
    }
}
