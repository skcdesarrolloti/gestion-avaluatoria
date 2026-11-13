<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{ComparableAreaInput,ComparableStudy,MethodologyComparableScope};
use PDO;

final class ComparableAreaRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,int $owner,string $key,array $post): int
    {
        $version=filter_var($post['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Falta la versión de las muestras.');
        $changes=ComparableAreaInput::read($post);
        $this->db->beginTransaction();
        try {
            $query=$this->db->prepare('SELECT * FROM appraisals WHERE id=? AND owner_id=? FOR UPDATE');
            $query->execute([$id,$owner]);$record=$query->fetch();
            if (!$record) throw new HttpException(404,'Expediente no encontrado.');
            if ((int)$record['comparables_version']!==$version) throw new HttpException(409,'Las muestras cambiaron. Conserva lo escrito y vuelve a abrir las áreas antes de continuar.');
            ComparableStudy::assertRequest($record,$key,$post);
            $rows=MethodologyComparableScope::rows((new AppraisalComparableRepository($this->db))->forAppraisal($id,$owner),$key,$record);
            $known=array_column($rows,null,'id');
            if (array_diff(array_keys($changes),array_keys($known))) throw new HttpException(422,'Una muestra no pertenece a esta investigación.');
            $update=$this->db->prepare('UPDATE appraisal_comparables SET area_m2=?,capture_details=?,updated_at=UTC_TIMESTAMP() WHERE id=? AND appraisal_id=? AND owner_id=?');
            $changed=false;
            foreach ($changes as $sample=>$fields) {
                $row=$known[$sample];$details=json_decode($row['capture_details'] ?: '{}',true,8,JSON_THROW_ON_ERROR);
                $before=['area_m2'=>(string)($row['area_m2'] ?? ''),'area_basis'=>(string)($details['area_basis'] ?? ''),'areas_source'=>(string)($details['areas_source'] ?? '')];
                if ($fields===$before) continue;
                if ($fields['area_m2']!=='' && $fields['area_m2']!==$before['area_m2'] && ($fields['area_basis']==='' || $fields['areas_source']===''))
                    throw new HttpException(422,'Indica qué área publica la fuente y su soporte.',["areas[$sample][areas_source]"=>'Documenta la fuente y el significado del área.']);
                $details['area_basis']=$fields['area_basis'];$details['areas_source']=$fields['areas_source'];
                $update->execute([$fields['area_m2']===''?null:$fields['area_m2'],json_encode($details,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$sample,$id,$owner]);
                $changed=true;
            }
            if ($changed) $this->db->prepare('UPDATE appraisals SET comparables_version=comparables_version+1 WHERE id=? AND owner_id=?')->execute([$id,$owner]);
            $this->db->commit();return $version+(int)$changed;
        } catch (\Throwable $error) { $this->db->rollBack();throw $error; }
    }
}
