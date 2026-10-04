<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,HttpException};
use App\Models\SubjectFactorRepository;
final class SubjectFactorController
{
    public function __construct(private SubjectFactorRepository $factors,private array $user) {}
    public function save(string $id,string $unitId): never
    {
        $version=filter_var($_POST['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null) throw new HttpException(422,'Falta la versión de los factores del sujeto.');
        $next=$this->factors->save($id,$this->user['id'],$unitId,$version,$_POST['factors'] ?? null);
        if (Http::wantsJson()) Http::json(['ok'=>true,'version'=>$next,'saved_at'=>gmdate('c')]);
        Http::redirect('avaluos/'.$id.'/bien-sujeto#factores');
    }
}
