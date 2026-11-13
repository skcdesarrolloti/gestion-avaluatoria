<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,HttpException,Session};
use App\Models\{AppraisalRepository,IncomeSampleSelectionRepository};
use App\Services\{MethodologyWorkflow,ComparableStudy};

final class IncomeSampleSelectionController
{
    public function __construct(private AppraisalRepository $appraisals,private IncomeSampleSelectionRepository $selection,private array $user) {}
    public function save(string $id): never
    {
        $this->appraisals->find($id,$this->user['id']);
        $key=is_string($_POST['component'] ?? null)?$_POST['component']:'';
        $url='avaluos/'.$id.'/metodologia-valuatoria?stage=3&method=renta&component='.rawurlencode($key).'&renta_paso=analysis#renta-analisis';
        try { $next=$this->selection->save($id,$this->user,$key,$_POST); }
        catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[422,409],true)) throw $error;
            $old=array_intersect_key($_POST,array_flip(['sample','category','reason','support','version','samples_version']));
            $old=array_filter($old,'is_string');
            Session::flash('income_selection_'.$id.'_'.$key,json_encode(['old'=>$old,'message'=>$error->getMessage(),'errors'=>$error->errors],JSON_THROW_ON_ERROR));
            Http::redirect($url);
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect($url);
    }
    public function export(string $id): never
    {
        $record=$this->appraisals->find($id,$this->user['id']);$key=is_string($_GET['component'] ?? null)?$_GET['component']:'';
        MethodologyWorkflow::validateKey($key,MethodologyWorkflow::components($record,$this->appraisals->units($id,$this->user['id'])));
        if ($key==='' || ComparableStudy::context($record,$key)['token']!=='renta:arriendo') throw new HttpException(422,'Abre el recorrido de Renta.');
        header('Content-Disposition: attachment; filename="constancia-exclusiones-renta.json"');
        Http::json(['expediente'=>$id,'recorrido'=>$key,'study'=>'renta:arriendo','exported_at'=>gmdate('c'),
            'basis'=>'Importe anunciado mensual / área publicada. Exploratorio; administración y anexos pendientes de depuración.',
            'audit'=>MethodologyWorkflow::saved($record)[$key]['income_selection'] ?? []]);
    }
}
