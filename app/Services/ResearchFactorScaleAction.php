<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException};
final class ResearchFactorScaleAction
{
    public static function save(\App\Models\AppraisalRepository $appraisals,\App\Models\ResearchFactorScaleRepository $scales,int $owner,string $id): never
    {
        $appraisals->find($id,$owner);
        $scale=ResearchFactorScaleInput::input($_POST);
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión de escala inválida.');
        $next=$scales->save($owner,$_POST['factor_key'],$version,$scale);
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect('avaluos/'.$id.'/metodologia-valuatoria?stage=plan&factor_catalog=1');
    }
}
