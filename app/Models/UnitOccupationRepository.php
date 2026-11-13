<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\UnitOccupation;
use PDO;

final class UnitOccupationRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,int $owner,string $unit,int $version,array $data): int
    {
        $repo=new AppraisalRepository($this->db);$repo->find($id,$owner);
        $units=array_column($repo->units($id,$owner),null,'id');
        if (!isset($units[$unit]) || $units[$unit]['unit_kind']==='common') throw new HttpException(404,'No se encontró la unidad.');
        $data=array_replace(UnitOccupation::read($units[$unit]),UnitOccupation::input($data));
        $q=$this->db->prepare('UPDATE appraisal_units SET occupation_details=?,occupation_version=occupation_version+1 WHERE id=? AND appraisal_id=? AND owner_id=? AND occupation_version=?');
        $q->execute([json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$unit,$id,$owner,$version]);
        if ($q->rowCount()!==1) throw new HttpException(409,'La ocupación cambió en otra pestaña. Conserva tu borrador y recarga para revisar.');
        return $version+1;
    }
}
