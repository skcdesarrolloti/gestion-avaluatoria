<?php
declare(strict_types=1);
namespace App\Services;

final class LeasePreparationFields
{
    public static function all(): array
    {
        $f=static fn($label,$type,$help,$options=[])=>compact('label','type','help','options');
        return [
            'initial_rent_amount'=>$f('Canon inicial documentado (COP)','money','Importe al inicio del contrato. No reemplaza el canon vigente ni se calcula hacia atrás.'),
            'initial_rent_period'=>$f('Periodicidad del canon inicial','select','Período al que corresponde el importe inicial.',
                ['mensual'=>'Mensual','trimestral'=>'Trimestral','anual'=>'Anual','otro'=>'Otra; explicar en la regla contractual']),
            'first_adjustment_date'=>$f('Primer incremento documentado','date','Fecha prevista en el contrato; no se supone que sea en enero.'),
            'adjustment_index_reference'=>$f('Índice y período de referencia del incremento','textarea','Ej. IPC nacional, variación anual de diciembre del año anterior, según cláusula 5. Transcribe lo pactado; no se descarga ni aplica una tasa.'),
            'analysis_area_reference'=>$f('Área de referencia del capítulo 3','area_reference','Selecciona el área documentada de una unidad comprendida. Se consulta su valor en origen; no se copia ni se divide el canon.'),
            'analysis_area_scope'=>$f('¿El canon comprende ese mismo espacio?','select','Revisa anexos y arriendos parciales antes de comparar por m².',
                ['same'=>'Sí, corresponde al espacio identificado','different'=>'Incluye otros espacios o solo una parte','pending'=>'Por verificar']),
            'analysis_area_notes'=>$f('Soporte del área y tratamiento de los espacios incluidos','textarea','Ej. El canon incluye garaje; falta documentar su tratamiento. Si el área es parcial, identificarla y respaldarla en el capítulo 3.'),
            'monthly_history'=>['type'=>'history'],
        ];
    }
}
