<?php
declare(strict_types=1);
namespace App\Services;

final class LeaseFields
{
    public const ECONOMIC=['rent_amount','rent_period','ph_admin_fee_amount','rent_charges_vat','income_notes'];
    public static function all(): array
    {
        $f=static fn($label,$type,$help,$options=[])=>compact('label','type','help','options');
        return [
            'reference'=>$f('Identificación del contrato','text','Ej. Contrato oficina y garaje · firmado en 2025'),
            'status'=>$f('Situación documentada del contrato','select','Selecciona según los documentos; las fechas no cambian este estado automáticamente.',
                ['draft'=>'Por verificar','current'=>'Vigente según soporte','past'=>'Anterior / terminado']),
            'scope'=>$f('Espacios o partes comprendidos','textarea','Ej. Oficina completa y garaje 12; el depósito tiene otro contrato.'),
            'economic_source'=>$f('Procedencia del canon','select','Usa el antecedente del encargo solo si corresponde a este contrato. Se consulta sin copiarlo.',
                ['own'=>'Canon propio de este contrato','assignment'=>'Canon ya registrado en el encargo']),
            'rent_amount'=>$f('Canon pactado vigente (COP)','money','Importe del período indicado. Ej. 3500000,00; sin sumar administración o IVA.'),
            'rent_period'=>$f('Periodicidad del canon','select','Selecciona el período del importe informado.',
                ['mensual'=>'Mensual','trimestral'=>'Trimestral','anual'=>'Anual','otro'=>'Otra; explicar en observaciones']),
            'ph_admin_fee_amount'=>$f('Administración informada (COP)','money','Importe informado; precisa periodicidad y si está incluido en el canon.'),
            'rent_charges_vat'=>$f('IVA del canon informado','select','Registra lo documentado; no calcula impuestos.',
                ['si'=>'Sí','no'=>'No','no_aplica'=>'No aplica','pendiente'=>'Por verificar']),
            'income_notes'=>$f('Observaciones sobre canon, administración e IVA','textarea','Explica conceptos, periodicidad y fuente del importe.'),
            'effective_date'=>$f('Fecha de referencia del canon vigente','date','Fecha a la que corresponde el canon informado.'),
            'received_differs'=>$f('¿Lo recibido difiere de lo pactado?','select','Distingue el pago recibido del canon contractual.',
                ['no'=>'No, según soporte','yes'=>'Sí; detallar importe y período','pending'=>'Por verificar']),
            'received_amount'=>$f('Importe efectivamente recibido (COP)','money','Registra solo lo acreditado por el soporte; vacío significa desconocido.'),
            'received_period'=>$f('Período y soporte del pago recibido','textarea','Ej. Pago de abril de 2026 según recibo; explica cualquier diferencia.'),
            'start_date'=>$f('Fecha de inicio del contrato','date','Transcribe la fecha de inicio documentada.'),
            'end_date'=>$f('Fecha de terminación vigente','date','Incluye la prórroga documentada. Si no se conoce, déjala pendiente y explica.'),
            'term'=>$f('Plazo pactado (años o meses)','text','Ej. 3 años según cláusula 4. No se calcula a partir de las fechas.'),
            'renewals'=>$f('Prórrogas y condiciones de terminación','textarea','Indica las modificaciones, fechas y cláusulas que soportan la vigencia actual.'),
            'adjustment_rule'=>$f('Regla contractual de incremento','textarea','Transcribe índice, porcentaje o condición pactada y cláusula. No adopta un incremento por defecto.'),
            'adjustment_frequency'=>$f('Periodicidad del incremento','text','Ej. Anual, en cada aniversario del contrato.'),
            'adjustment_next_date'=>$f('Fecha del próximo incremento documentado','date','Registra la fecha conocida; no se proyecta automáticamente.'),
            'inclusions'=>$f('Conceptos incluidos y cobros separados','textarea','Identifica administración, IVA, parqueadero, depósito y servicios, según contrato.'),
            'grace'=>$f('Períodos de gracia y condiciones especiales','textarea','Identifica fechas, adecuaciones y acuerdos especiales con su soporte.'),
            'expenses'=>$f('Gastos y responsabilidades de las partes','textarea','Precisa quién asume administración, predial, seguros, mantenimiento y servicios; cita cláusulas.'),
            'support'=>$f('Documento, fecha y fuente de la información','textarea','Ej. Contrato y otrosí archivados en el expediente; identifica páginas o cláusulas revisadas.'),
            'notes'=>$f('Pendientes por confirmar','textarea','Anota información faltante y documento o persona a consultar.'),
        ];
    }
}
