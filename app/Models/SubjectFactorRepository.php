<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\SubjectFactorCapture;
use PDO;

final class SubjectFactorRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,int $owner,string $unitId,int $version,mixed $posted): int
    {
        $repo=new AppraisalRepository($this->db); $record=$repo->find($id,$owner);
        $units=array_column($repo->units($id,$owner),null,'id');
        $record['factor_principal_count']=count(array_filter($units,static fn($item)=>$item['unit_kind']==='property'));
        $unit=$units[$unitId] ?? null;
        if (!$unit || $unit['unit_kind']==='common') throw new HttpException(404,'No se encontró una unidad activa del expediente.');
        if ($version<0 || $version!==(int)$unit['subject_factors_version']) throw new HttpException(409,'Los factores del sujeto cambiaron en otra pestaña. Conserva tus cambios y recarga.');
        $scales=(new ResearchFactorScaleRepository($this->db))->all($owner);
        $data=SubjectFactorCapture::input($posted,SubjectFactorCapture::catalog($unit,$record,$scales),SubjectFactorCapture::decode($unit));
        $query=$this->db->prepare('UPDATE appraisal_units SET subject_factors_json=?,subject_factors_version=subject_factors_version+1,updated_at=UTC_TIMESTAMP() WHERE id=? AND appraisal_id=? AND owner_id=? AND subject_factors_version=?');
        $query->execute([json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$unitId,$id,$owner,$version]);
        if ($query->rowCount()!==1) throw new HttpException(409,'Los factores del sujeto cambiaron en otra pestaña. Conserva tus cambios y recarga.');
        return $version+1;
    }
}
