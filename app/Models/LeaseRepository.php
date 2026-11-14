<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{LeaseFields,LeaseInput,LeaseRegister,LeaseMonthlyHistory};
use PDO;

final class LeaseRepository
{
    public function __construct(private PDO $db) {}

    public function save(string $id,int $owner,int $version,string $leaseId,mixed $unitIds,mixed $posted): int
    {
        $repo=new AppraisalRepository($this->db); $record=$repo->find($id,$owner);
        if ($version<0 || !preg_match('/^[a-f0-9]{32}$/D',$leaseId)) throw new HttpException(422,'Identificación o versión inválida.');
        $all=LeaseRegister::read($record); $previous=$all[$leaseId] ?? [];
        $active=array_column(array_filter($repo->units($id,$owner),static fn($u)=>$u['unit_kind']!=='common'),'id');
        $allowed=array_unique([...$active,...($previous['unit_ids'] ?? [])]);
        if (!is_array($unitIds) || !$unitIds || array_filter($unitIds,static fn($u)=>!is_string($u) || !in_array($u,$allowed,true)))
            throw new HttpException(422,'Relaciona las unidades del contrato.',['lease_units'=>'Selecciona unidades de este expediente.']);
        $changes=LeaseInput::fields($posted);
        if (isset($changes['monthly_history'])) $changes['monthly_history']=LeaseMonthlyHistory::merge($previous['monthly_history'] ?? [],$changes['monthly_history']);
        foreach (['rent_adjustments','admin_adjustments'] as $group)
            if (isset($changes[$group])) $changes[$group]=\App\Services\LeaseAdjustments::merge($previous[$group] ?? [],$changes[$group]);
        if (isset($changes['vacancy_periods'])) $changes['vacancy_periods']=\App\Services\LeaseVacancies::merge($previous['vacancy_periods'] ?? [],$changes['vacancy_periods']);
        $data=array_replace($previous,$changes,['unit_ids'=>array_values(array_unique($unitIds))]);
        if (!empty($data['analysis_area_reference']) && !in_array(explode(':',$data['analysis_area_reference'])[0],$data['unit_ids'],true))
            throw new HttpException(422,'El área debe pertenecer a una unidad relacionada.',
                ['lease[analysis_area_reference]'=>'Revisa las unidades incluidas en el contrato.']);
        LeaseInput::validate($data);
        if (($data['economic_source'] ?? '')==='assignment') foreach ($all as $key=>$lease)
            if ($key!==$leaseId && ($lease['economic_source'] ?? '')==='assignment')
                throw new HttpException(422,'El canon del encargo ya está relacionado con otro contrato. Consulta esa ficha.',
                    ['lease[economic_source]'=>'Usa el mismo contrato y relaciona sus unidades; no dupliques el canon.']);
        $all[$leaseId]=$data;
        $q=$this->db->prepare('UPDATE appraisals SET lease_register=?, lease_version=lease_version+1 WHERE id=? AND owner_id=? AND lease_version=?');
        $q->execute([json_encode($all,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$id,$owner,$version]);
        if ($q->rowCount()!==1) throw new HttpException(409,'La ficha de arriendos cambió en otra pestaña. Conserva tu borrador y recarga para revisar.');
        return $version+1;
    }

    public function saveAntecedent(string $id,int $owner,int $version,mixed $post): int
    {
        $record=(new AppraisalRepository($this->db))->find($id,$owner);
        $changes=LeaseInput::fields($post,'antecedent',LeaseFields::ECONOMIC);
        $data=array_replace($record,$changes);
        $q=$this->db->prepare('UPDATE appraisals SET rent_amount=?,rent_period=?,ph_admin_fee_amount=?,rent_charges_vat=?,income_notes=?,version=version+1 WHERE id=? AND owner_id=? AND version=?');
        $q->execute([$data['rent_amount']===''?null:$data['rent_amount'],$data['rent_period'],
            $data['ph_admin_fee_amount']===''?null:$data['ph_admin_fee_amount'],$data['rent_charges_vat'],$data['income_notes'],$id,$owner,$version]);
        if ($q->rowCount()!==1) throw new HttpException(409,'El antecedente del encargo cambió. Conserva tu borrador y recarga para revisar.');
        return $version+1;
    }
}
