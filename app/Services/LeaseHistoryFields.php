<?php
declare(strict_types=1);
namespace App\Services;

final class LeaseHistoryFields
{
    public static function all(): array
    {
        $f=static fn($label,$type,$help,$options=[])=>compact('label','type','help','options');
        $fields=[
            'occupancy'=>$f('Ocupación documentada','select','Un impago o un mes sin información no demuestra vacancia.',
                ['occupied'=>'Ocupado todo el mes','vacant'=>'Vacante todo el mes','partial'=>'Ocupación parcial del mes','grace'=>'Ocupado con gracia','unknown'=>'Sin información']),
            'occupied_days'=>$f('Días ocupados','days','Para ocupación parcial: días documentados, entre 0 y los días del mes. No prorratea por sí solo el canon.'),
            'rent_billed'=>$f('Canon facturado del mes (COP)','money','Según factura o cuenta; puede diferir de la reconstrucción contractual.'),
            'vat_rate'=>$f('IVA aplicable al canon (%)','percent','Según soporte del mes. 0 si no aplica. No se deduce la tarifa por el tipo de inmueble.'),
            'vat_billed'=>$f('IVA facturado (COP)','money','Importe documentado; vacío permite estimarlo solo si se documentaron canon facturado y tarifa.'),
            'vat_paid'=>$f('IVA recibido (COP)','money','Recaudo separado del canon; no se considera renta del propietario.'),
            'admin_received'=>$f('Administración recibida del arrendatario (COP)','money','Solo dinero recibido por el propietario. No confundir con lo pagado a la PH.'),
        ];
        foreach (IncomeCostCatalog::all() as $key=>$row) $fields['cost_'.$key]=$f($row['label'].' · costo del propietario (COP)','money',
            'Pago neto de reembolsos imputado a este mes. 0 exige revisión; vacío queda pendiente. '.$row['help']);
        return $fields;
    }
}
