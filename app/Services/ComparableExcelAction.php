<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException};
use App\Models\{AppraisalRepository,AppraisalComparableRepository};

final class ComparableExcelAction
{
    public static function run(AppraisalRepository $appraisals, AppraisalComparableRepository $comparables, int $owner, string $id, bool $save=false): never
    {
        $record=$appraisals->find($id,$owner);
        $key=is_string($_POST['component_scope'] ?? null)?$_POST['component_scope']:'';
        MethodologyWorkflow::validateKey($key,MethodologyWorkflow::components($record,$appraisals->units($id,$owner)));
        $file=$_FILES['excel'] ?? [];
        if (($file['error'] ?? -1)!==UPLOAD_ERR_OK || ($file['size'] ?? 0)>5000000 || !is_uploaded_file($file['tmp_name'] ?? '')) throw new HttpException(422,'Selecciona un Excel .xlsx de hasta 5 MB.');
        $all=$comparables->forAppraisal($id,$owner);
        $scoped=MethodologyComparableScope::rows($all,$key);
        $version=(int)$record['comparables_version'];
        $rows=ComparableExcelInput::read($file['tmp_name'],$id,$key,$version,$scoped);
        if (!$save) Http::json(['ok'=>true,'rows'=>$rows,'version'=>$version]);
        $changes=array_column($rows,null,'id');
        foreach ($scoped as &$row) $row=array_replace($row,$changes[$row['id']] ?? []);
        unset($row);
        $name=mb_substr(preg_replace('/[\x00-\x1f]/u','',basename(str_replace('\\','/',(string)($file['name'] ?? 'comparables.xlsx')))) ?? 'comparables.xlsx',0,200);
        $stamp=['scope'=>$key,'filename'=>$name,'saved_at'=>gmdate('c'),'rows'=>count($rows)];
        $next=$comparables->saveAll($id,$owner,MethodologyComparableScope::merge($all,$scoped,$key),$version,$stamp);
        $stamp['version']=$next;
        Http::json(['ok'=>true,'version'=>$next,'saved_at'=>$stamp['saved_at'],'excel_updated'=>ComparableExcelHistory::display($stamp)]);
    }
}
