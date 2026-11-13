<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{MethodologyWorkflow,ComparableStudy,IncomeValuationContext,IncomeValuationInput,UnitOccupation};
use App\Support\AppraisalCatalog;
use PDO;

final class IncomeOriginRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,int $owner,string $key,array $post): array
    {
        $input=$post['origin'] ?? null;$errors=[];
        $columns=['label','property_type','area_private_m2','area_adopted_m2','built_area_adopted_m2','surface_source'];
        if (!is_array($input) || array_diff(array_keys($input),[...$columns,'value_date'])) throw new HttpException(422,'Campos de origen inválidos.');
        foreach ($input as $field=>&$value) {
            if (!is_string($value) || mb_strlen($value)>($field==='surface_source' || $field==='label'?120:40)) {$errors['origin['.$field.']']='Formato o longitud inválida.';continue;}
            $value=trim($value);
            if ($field==='value_date' && $value!=='' && !IncomeValuationInput::date($value)) $errors['origin[value_date]']='Fecha inválida.';
            elseif ($field==='label' && $value==='') $errors['origin[label]']='Identifica la unidad.';
            elseif ($field==='property_type' && $value!=='' && !in_array($value,AppraisalCatalog::allowedValues('tipo_inmueble'),true)) $errors['origin[property_type]']='Tipo no admitido.';
            elseif (str_ends_with($field,'_m2')) {
                $value=str_replace(',','.',$value);
                if ($value!=='' && (!preg_match('/^\d+(\.\d{1,2})?$/D',$value) || (float)$value>9999999999.99)) $errors['origin['.$field.']']='Área no negativa, hasta dos decimales, sin miles.';
                elseif ($value==='') $value=null;
            }
        }
        unset($value);
        $occupation=UnitOccupation::input($post['occupation'] ?? []);
        if ($errors) throw new HttpException(422,'Revisa los campos señalados.',$errors);
        $version=filter_var($post['version'] ?? null,FILTER_VALIDATE_INT);
        $occVersion=filter_var($post['occupation_version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0 || $occVersion===false || $occVersion===null || $occVersion<0) throw new HttpException(422,'Versión de origen inválida.');
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare('SELECT * FROM appraisals WHERE id=? AND owner_id=? FOR UPDATE');$q->execute([$id,$owner]);$record=$q->fetch();
            if (!$record) throw new HttpException(404,'No se encontró el expediente.');
            $repo=new AppraisalRepository($this->db);$components=MethodologyWorkflow::components($record,$repo->units($id,$owner));
            MethodologyWorkflow::validateKey($key,$components);
            if ($key==='' || ComparableStudy::context($record,$key)['token']!=='renta:arriendo') throw new HttpException(422,'Abre un recorrido de Renta.');
            $unit=$components[$key]['unit'];
            if ((int)$record['version']!==$version || (int)($unit['occupation_version'] ?? 0)!==$occVersion) throw new HttpException(409,'El origen cambió en otra pestaña. Conserva tu borrador y revisa antes de actualizar.');
            $q=$this->db->prepare('UPDATE appraisals SET value_date=?,version=version+1,updated_at=UTC_TIMESTAMP() WHERE id=? AND owner_id=? AND version=?');
            $date=$input['value_date'] ?? $record['value_date'];$q->execute([$date===''?null:$date,$id,$owner,$version]);
            if ($q->rowCount()!==1) throw new HttpException(409,'El encargo cambió.');
            $next=array_replace($unit,array_intersect_key($input,array_flip($columns)));
            $occupancy=array_replace(UnitOccupation::read($unit),$occupation);
            $q=$this->db->prepare('UPDATE appraisal_units SET label=?,property_type=?,area_private_m2=?,area_adopted_m2=?,built_area_adopted_m2=?,surface_source=?,occupation_details=?,occupation_version=occupation_version+1,updated_at=UTC_TIMESTAMP() WHERE id=? AND appraisal_id=? AND owner_id=? AND occupation_version=?');
            $q->execute([...array_map(static fn($f)=>$next[$f] ?? null,$columns),json_encode($occupancy,JSON_THROW_ON_ERROR),$unit['id'],$id,$owner,$occVersion]);
            if ($q->rowCount()!==1) throw new HttpException(409,'La ocupación cambió.');
            $record=$repo->find($id,$owner);$components=MethodologyWorkflow::components($record,$repo->units($id,$owner));
            $this->db->commit();
            return ['ok'=>true,'version'=>$version+1,'occupation_version'=>$occVersion+1,'saved_at'=>gmdate('c'),
                'income_context'=>IncomeValuationContext::build($record,$components[$key])];
        } catch (\Throwable $e) {if ($this->db->inTransaction()) $this->db->rollBack();throw $e;}
    }
}
