<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class SubjectUnitsRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,int $owner,array $units,?int $version): int
    {
        $repo=new AppraisalRepository($this->db);$record=$repo->find($id,$owner);
        $version ??= (int)$record['version'];
        $allowed=array_column($repo->units($id,$owner),'id');
        foreach ($units as $unit) if (!in_array($unit['id'],$allowed,true)) throw new HttpException(404,'No se encontró la unidad del expediente.');
        $this->db->beginTransaction();
        try {
            $lock=$this->db->prepare('UPDATE appraisals SET version=version+1 WHERE id=? AND owner_id=? AND version=?');
            $lock->execute([$id,$owner,$version]);
            if ($lock->rowCount()!==1) throw new HttpException(409,'La descripción o configuración cambió en otra pestaña. Conserva tu borrador y recarga para revisar.');
            $query=$this->db->prepare('UPDATE appraisal_units SET label=?,property_type=?,igac_category=?,igac_typology_hint=?,notes=?,updated_at=? WHERE id=? AND appraisal_id=? AND owner_id=?');
            foreach ($units as $unit) $query->execute([$unit['label'],$unit['property_type'],$unit['igac_category'],$unit['igac_typology_hint'],
                $unit['notes'],gmdate('Y-m-d H:i:s'),$unit['id'],$id,$owner]);
            $this->db->commit();return $version+1;
        } catch (\Throwable $error) { if ($this->db->inTransaction()) $this->db->rollBack();throw $error; }
    }
}
