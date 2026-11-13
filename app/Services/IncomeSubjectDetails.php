<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalFunctionalVariableCatalog;

final class IncomeSubjectDetails
{
    public static function physical(array $unit,array $record): array
    {
        $rows=[];
        foreach (['construction_year'=>'Año de construcción','construction_age_years'=>'Edad adoptada (años)',
            'construction_apparent_age_years'=>'Edad aparente (años)','construction_floors'=>'Pisos',
            'construction_state'=>'Estado de obra','conservation_approved_text'=>'Conservación: texto adoptado'] as $key=>$label) {
            $value=trim((string)($unit[$key] ?? ''));
            if ($value==='') continue;
            if ($key==='construction_state') $value=['completa'=>'Obra completa','en_construccion'=>'En construcción',
                'desmantelamiento'=>'En desmantelamiento','inconclusa'=>'Sin avance / obra inconclusa'][$value] ?? $value;
            $rows[]=compact('label','value')+['source'=>'3.3 · Dato registrado; consultar soporte en origen','support'=>''];
        }
        foreach (AppraisalFunctionalVariableCatalog::definitions() as $key=>$definition) {
            $value=trim((string)($unit[$key] ?? ''));
            if ($value==='') continue;
            $rows[]=['label'=>$definition['label'],'value'=>$definition['options'][$value] ?? $value,
                'source'=>'3.3 · Característica registrada','support'=>$unit['functional_notes'] ?? ''];
        }
        $catalog=SubjectFactorCapture::catalog($unit,$record);
        foreach (SubjectFactorCapture::decode($unit) as $key=>$item) {
            if (!is_array($item) || trim((string)($item['value'] ?? ''))==='') continue;
            $rows[]=['label'=>$catalog[$key]['label'] ?? 'Atributo registrado: '.$key,'value'=>(string)$item['value'],
                'source'=>'3.4 · Observación conservada; no es un porcentaje','support'=>(string)($item['support'] ?? '')];
        }
        return $rows;
    }

    public static function economic(array $record,array $ph,array $subject,string $base): array
    {
        $rows=[];
        foreach (['income_producing'=>'¿Produce renta?','rent_amount'=>'Canon informado (COP)',
            'rent_period'=>'Periodicidad','ph_admin_fee_amount'=>'Administración informada (COP)',
            'rent_charges_vat'=>'IVA informado','income_notes'=>'Observaciones del canon'] as $key=>$label) {
            $value=(string)($record[$key] ?? '');
            $value=['si'=>'Sí','no'=>'No','no_aplica'=>'No aplica','pendiente'=>'Por verificar',
                'mensual'=>'Mensual','trimestral'=>'Trimestral','anual'=>'Anual','otro'=>'Otro'][$value] ?? $value;
            $rows[]=compact('label','value')+['source'=>'Encargo · Contexto del expediente','support'=>'','url'=>$base.'/expediente'];
        }
        $rows[]=['label'=>'Soporte de predial','value'=>$subject['predial_bill_source'] ?? '',
            'source'=>'3.1 · No equivale al gasto efectivamente pagado','support'=>'','url'=>$base.'/bien-sujeto'];
        foreach (['monthly_fee'=>'Cuota de administración PH','fee_status'=>'Expensas de PH',
            'reserve_fund'=>'Fondo o expensas extraordinarias','insurance_status'=>'Seguros comunes'] as $key=>$label) {
            $value=$ph['technical'][$key] ?? $ph[$key] ?? '';
            $rows[]=compact('label','value')+['source'=>'3.5 · Contexto de la copropiedad; comprobar alcance','support'=>'',
                'url'=>$base.'/bien-sujeto#ph'];
        }
        return $rows;
    }
}
