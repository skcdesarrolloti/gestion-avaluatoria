<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http,HttpException,Session};
use App\Models\AppraisalRepository;

final class UnitOccupation
{
    public static function read(array $unit): array
    { return json_decode($unit['occupation_details'] ?? '{}',true,8,JSON_THROW_ON_ERROR) ?: []; }
    public static function input(mixed $posted): array
    {
        if (!is_array($posted)) throw new HttpException(422,'Revisa la ocupación de la unidad.');
        $out=$errors=[];
        foreach (['occupancy','date','source'] as $key) {
            if (!array_key_exists($key,$posted)) continue;
            $value=$posted[$key];
            if (!is_string($value) || mb_strlen($value)>1000) $errors['occupation['.$key.']']='Usa como máximo 1000 caracteres.';
            elseif ($key==='occupancy' && $value!=='' && !isset(IncomeConcept::questions()['occupancy']['values'][$value]))
                $errors['occupation[occupancy]']='Selecciona una opción válida.';
            elseif ($key==='date' && $value!=='' && (!($date=\DateTimeImmutable::createFromFormat('!Y-m-d',$value)) || $date->format('Y-m-d')!==$value))
                $errors['occupation[date]']='Indica una fecha válida.';
            else $out[$key]=trim($value);
        }
        if ($errors) throw new HttpException(422,'Revisa la ocupación registrada.',$errors);
        return $out;
    }
    public static function save(AppraisalRepository $repo,int $owner,string $id,string $unit): never
    {
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
        $target='avaluos/'.$id.'/bien-sujeto?unit='.$unit.'&section=tipologias#ocupacion-'.$unit;
        try { $next=$repo->saveUnitOccupation($id,$owner,$unit,$version,self::input($_POST['occupation'] ?? null)); }
        catch (HttpException $error) {
            if (Http::wantsJson() || !in_array($error->status,[409,422],true)) throw $error;
            $old=is_array($_POST['occupation'] ?? null)?array_filter($_POST['occupation'],'is_string'):[];
            Session::flash('occupation_'.$unit,json_encode(['values'=>$old,'errors'=>$error->errors,'message'=>$error->getMessage(),'version'=>$version],JSON_THROW_ON_ERROR));
            Http::redirect($target);
        }
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect($target);
    }
}
