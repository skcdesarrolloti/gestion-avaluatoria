<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{MethodologyWorkflow,MethodologyValuationPlan,ComparableStudy,IncomeSampleSelection};
use PDO;

final class IncomeSampleSelectionRepository
{
    public function __construct(private PDO $db) {}

    public function save(string $id,array $actor,string $key,array $post): int
    {
        $owner=(int)$actor['id'];$input=IncomeSampleSelection::input($post);
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare('SELECT * FROM appraisals WHERE id=? AND owner_id=? FOR UPDATE');
            $q->execute([$id,$owner]);$record=$q->fetch();
            if (!$record) throw new HttpException(404,'No se encontró el expediente.');
            $components=MethodologyWorkflow::components($record,(new AppraisalRepository($this->db,$actor))->units($id,$owner));
            MethodologyWorkflow::validateKey($key,$components);MethodologyValuationPlan::validateWorkKey($key,$components);
            $context=ComparableStudy::assertRequest($record,$key,$post);
            if ($key==='' || $context['token']!=='renta:arriendo') throw new HttpException(422,'Esta decisión pertenece a un estudio activo de Renta.');
            foreach (['version'=>'methodology_version','samples_version'=>'comparables_version'] as $field=>$column) {
                $version=filter_var($post[$field] ?? null,FILTER_VALIDATE_INT);
                if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
                if ($version!==(int)$record[$column]) throw new HttpException(409,'Cambió la metodología o la matriz. Conserva tu motivo y actualiza antes de decidir.');
            }
            $source=ComparableStudy::rows((new AppraisalComparableRepository($this->db))->forAppraisal($id,$owner),$key,$record);
            $rows=IncomeSampleSelection::rows($source);$flow=MethodologyWorkflow::saved($record);
            $state=IncomeSampleSelection::change($flow[$key]['income_selection'] ?? [],$rows,$input,$actor,gmdate('c'));
            $next=(new MethodologyWorkflowRepository($this->db))->save($id,$owner,(int)$post['version'],$key,['income_selection'=>$state]);
            $this->db->commit();return $next;
        } catch (\Throwable $e) { $this->db->rollBack();throw $e; }
    }
}
