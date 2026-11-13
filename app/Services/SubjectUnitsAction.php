<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException,Session};
use App\Models\{AppraisalRepository,IgacTypologyRepository};

final class SubjectUnitsAction
{
    public static function save(AppraisalRepository $repo,int $owner,string $id): never
    {
        $repo->find($id,$owner);
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Recarga el capítulo 3 para usar la versión actual del formulario.');
        $typologies=new IgacTypologyRepository();
        $target='avaluos/'.$id.'/bien-sujeto?section=tipologias#unidades-capitulo-3';
        try {
            $data=AppraisalChapterZeroInput::unitData(array_column($typologies->categories(),'code'),$typologies->optionsByCategory());
            $next=$repo->saveUnits($id,$owner,$data,$version);
        } catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[409,422],true)) throw $error;
            $old=[];
            foreach (is_array($_POST['units'] ?? null)?$_POST['units']:[] as $key=>$fields)
                if (is_array($fields)) $old[$key]=array_filter($fields,'is_string');
            Session::flash('subject_units_'.$id,json_encode(['units'=>$old,'version'=>$version,'message'=>$error->getMessage()],JSON_THROW_ON_ERROR));
            Http::redirect($target);
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect($target);
    }
}
