<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

/** Pagos transcritos y revisión humana. No genera cánones, saldos, IPC ni totales. */
final class LeaseMonthlyHistory
{
    public static function fields(): array
    {
        $f=static fn($label,$type,$help,$options=[])=>compact('label','type','help','options');
        return [
            'rent_paid'=>$f('Arriendo recibido imputado a este mes (COP)','money','Según recibo. Un pago que cubra varios meses necesita distribución documentada.'),
            'admin_billed'=>$f('Cuota ordinaria de administración de este mes (COP)','money','Según cuenta de cobro de PH. No equivale a lo pagado.'),
            'admin_paid'=>$f('Administración ordinaria pagada para este mes (COP)','money','Transcribe el pago soportado; no se presume a partir de la cuota.'),
            'admin_extra_paid'=>$f('Expensas extraordinarias pagadas para este mes (COP)','money','Separadas de la cuota ordinaria y del arriendo; según comprobante.'),
            'admin_payer'=>$f('¿Quién pagó la administración?','select','Identifica el pagador; pagar no demuestra quién soporta finalmente el gasto.',
                ['owner'=>'Propietario','tenant'=>'Arrendatario','shared'=>'Compartido; detallar','other'=>'Otro; detallar','pending'=>'Por verificar']),
            'review'=>$f('Validación manual del mes','select','Marca revisado solo después de contrastar importes y soportes.',
                ['pending'=>'Por verificar','verified'=>'Revisado con soportes']),
            'support'=>$f('Soportes de los importes y fechas de pago','textarea','Identifica recibos, cuenta de PH, fechas y páginas. Si se pagó en otro mes, indícalo.'),
            'notes'=>$f('Diferencias, distribución y otros gastos por documentar','textarea','Ej. Pago parcial; reembolso pendiente; predial o seguro documentado aparte. No sumar conceptos aquí.'),
        ];
    }

    public static function input(mixed $posted): array
    {
        if (!is_array($posted) || count($posted)>1200) throw new HttpException(422,'Revisa el historial mensual.');
        $out=$errors=[]; $fields=self::fields();
        foreach ($posted as $month=>$row) {
            if (!is_string($month) || !preg_match('/^(19|20|21)\d{2}-(0[1-9]|1[0-2])$/D',$month)
                || !is_array($row) || array_diff(array_keys($row),array_keys($fields)))
                throw new HttpException(422,'Mes o campos del historial no admitidos.');
            foreach ($row as $key=>$value) {
                $name='lease[monthly_history]['.$month.']['.$key.']';
                if (!is_string($value) || mb_strlen($value)>1500) { $errors[$name]='Usa texto de hasta 1500 caracteres.'; continue; }
                $value=trim($value);$field=$fields[$key];
                if ($value!=='' && $field['type']==='money') {
                    $value=LeaseInput::money($value);
                    if ($value===null) { $errors[$name]='Usa un importe no negativo, hasta 12 enteros y 2 decimales.'; continue; }
                }
                if ($value!=='' && $field['type']==='select' && !isset($field['options'][$value])) {
                    $errors[$name]='Selecciona una opción de la lista.'; continue;
                }
                $out[$month][$key]=$value;
            }
        }
        if ($errors) throw new HttpException(422,'Revisa los datos mensuales señalados.',$errors);
        return $out;
    }

    public static function merge(array $previous,array $changes): array
    {
        foreach ($changes as $month=>$row) {
            $merged=array_replace($previous[$month] ?? [],$row);
            if (!array_filter($merged,static fn($value)=>$value!=='')) { unset($previous[$month]); continue; }
            // A changed reviewed record must be explicitly reviewed again, or is marked pending.
            if (($previous[$month]['review'] ?? '')==='verified' && ($row['review'] ?? '')!=='verified'
                && array_diff_assoc($merged,$previous[$month])) $merged['review']='pending';
            if (($merged['review'] ?? '')==='verified') {
                $amounts=array_intersect_key($merged,array_flip(['rent_paid','admin_billed','admin_paid','admin_extra_paid']));
                if (trim($merged['support'] ?? '')==='' || !array_filter($amounts,static fn($value)=>$value!==''))
                    throw new HttpException(422,'Para marcar un mes revisado, registra un importe y su soporte.',
                        ['lease[monthly_history]['.$month.'][review]'=>'Falta un importe documentado o su soporte.']);
            }
            $previous[$month]=$merged;
        }
        if (count($previous)>1200) throw new HttpException(422,'El historial admite hasta 1200 meses por contrato.');
        ksort($previous); return $previous;
    }

    public static function year(mixed $requested,array $lease): int
    {
        if (is_string($requested) && preg_match('/^(19|20|21)\d{2}$/D',$requested)) return (int)$requested;
        $start=substr((string)($lease['start_date'] ?? ''),0,4);
        return preg_match('/^(19|20|21)\d{2}$/D',$start)?(int)$start:(int)date('Y');
    }
}
