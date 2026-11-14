<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{MethodologyWorkflow,MethodologyValuationPlan,ComparableStudy,IncomeValuationInput,IncomeValuationContext,IncomeValuationCalculator};
use PDO;

final class IncomeValuationRepository
{
    public function __construct(private PDO $db) {}
    public function save(string $id,array $actor,string $key,array $post): array
    {
        $input=$post['data'] ?? [];
        if (($post['payload'] ?? '')==='' && ($post['form_complete'] ?? '')!=='1') throw new HttpException(422,'El envío está incompleto. No se reemplazó el escenario guardado.');
        if (isset($post['payload']) && $post['payload']!=='') {
            if (!is_string($post['payload']) || strlen($post['payload'])>1500000) throw new HttpException(422,'El borrador excede el tamaño permitido.');
            try { $input=json_decode($post['payload'],true,12,JSON_THROW_ON_ERROR); }
            catch (\JsonException) { throw new HttpException(422,'No se pudo leer el borrador.'); }
        }
        if (!is_array($input) || array_diff(array_keys($input),['projection','costs','events','rates','adoption'])) throw new HttpException(422,'Datos de valoración inválidos.');
        $clean=[];$issues=[];
        foreach (['projection','costs','events','rates','adoption'] as $section) {
            if (!array_key_exists($section,$input) && !in_array($section,['costs','events','rates'],true)) throw new HttpException(422,'El formulario está incompleto. Conserva los datos y reintenta.');
            try { $clean[$section]=IncomeValuationInput::section($section,$input[$section] ?? []); }
            catch (HttpException $e) {
                if (!$e->errors) throw $e;
                foreach ($e->errors as $field=>$message) $issues[str_replace('data','data['.$section.']',$field)]=$message;
            }
        }
        if ($issues) throw new HttpException(422,'Revisa los campos señalados.',$issues);
        $operation=$post['operation'] ?? 'save';
        if (!in_array($operation,['save','calculate','preview','add_costs','add_events','add_rates'],true)) throw new HttpException(422,'Acción inválida.');
        if (str_starts_with($operation,'add_')) {
            $group=substr($operation,4);$fields=$group==='rates'?\App\Services\IncomeValuationFields::rates():\App\Services\IncomeValuationFields::group($group);
            $row=array_fill_keys(array_keys($fields),'');if ($group==='rates') $row['id']=bin2hex(random_bytes(8));
            $clean[$group][]=$row;$clean[$group]=IncomeValuationInput::section($group,$clean[$group]);
        }
        $owner=(int)$actor['id'];$this->db->beginTransaction();
        try {
            $q=$this->db->prepare('SELECT * FROM appraisals WHERE id=? AND owner_id=? FOR UPDATE');$q->execute([$id,$owner]);$record=$q->fetch();
            if (!$record) throw new HttpException(404,'No se encontró el expediente.');
            $units=(new AppraisalRepository($this->db,$actor))->units($id,$owner);$components=MethodologyWorkflow::components($record,$units);
            MethodologyWorkflow::validateKey($key,$components);MethodologyValuationPlan::validateWorkKey($key,$components);
            if ($key==='' || ComparableStudy::assertRequest($record,$key,$post)['token']!=='renta:arriendo') throw new HttpException(422,'Abre el recorrido activo de Renta.');
            $version=filter_var($post['version'] ?? null,FILTER_VALIDATE_INT);
            if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
            if ($version!==(int)$record['methodology_version']) throw new HttpException(409,'La valoración cambió en otra pestaña. Conserva tus datos y actualiza antes de continuar.');
            $context=IncomeValuationContext::build($record,$components[$key]);
            if (!is_string($post['context_hash'] ?? null) || !hash_equals($context['hash'],$post['context_hash'])) throw new HttpException(409,'Cambió el inmueble o el contrato de origen. Guarda allí y vuelve a abrir Ingresos y gastos para revisar el nuevo contexto.');
            $old=MethodologyWorkflow::saved($record)[$key]['income_valuation'] ?? [];
            $state=array_replace($old,$clean);
            if ($operation==='preview') $state['flow_preview']=['fingerprint'=>IncomeValuationContext::fingerprint($clean,$context),
                'at'=>gmdate('c'),'actor'=>$owner,'inputs'=>$clean,'context'=>$context,
                'result'=>\App\Services\IncomeFlowPreview::calculate($clean,$context)];
            if ($operation==='calculate') {
                $state['calculation']=['fingerprint'=>IncomeValuationContext::fingerprint($clean,$context),'at'=>gmdate('c'),'actor'=>$owner,'engine'=>'renta-1',
                    'inputs'=>$clean,'context'=>$context,'result'=>IncomeValuationCalculator::calculate($clean,$context)];
            }
            $next=(new MethodologyWorkflowRepository($this->db))->save($id,$owner,$version,$key,['income_valuation'=>$state]);
            $this->db->commit();return ['version'=>$next];
        } catch (\Throwable $e) { $this->db->rollBack();throw $e; }
    }
}
