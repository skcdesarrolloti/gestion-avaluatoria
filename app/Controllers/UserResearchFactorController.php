<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,HttpException,Session};
use App\Models\{AppraisalRepository,UserResearchFactorRepository};
final class UserResearchFactorController
{
    public function __construct(private AppraisalRepository $appraisals,private UserResearchFactorRepository $factors,private array $user) {}
    public function save(string $id): never
    {
        $this->appraisals->find($id,$this->user['id']);
        try { $this->factors->save($this->user['id'],$_POST); }
        catch (HttpException $error) {
            if (!in_array($error->status,[422,409],true)) throw $error;
            $values=[];
            foreach (['label','why','unit','kind','categories','group','factor_key','version'] as $field) if (is_string($_POST[$field] ?? null)) $values[$field]=mb_substr($_POST[$field],0,1200);
            $values['types']=array_slice(array_values(array_filter(is_array($_POST['types'] ?? null)?$_POST['types']:[],static fn($v)=>is_string($v) && strlen($v)<40)),0,15);
            Session::flash('factor_editor',json_encode(['error'=>$error->getMessage(),'values'=>$values],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
        }
        Http::redirect('avaluos/'.$id.'/metodologia-valuatoria?stage=plan&factor_catalog=1');
    }
}
