<?php
declare(strict_types=1);
namespace App\Services;

final class LeaseReferenceFields
{
    public static function all(): array
    {
        $f=static fn($label,$type,$help,$options=[])=>compact('label','type','help','options');
        return [
            'history_ipc_points'=>$f('Puntos adicionales al IPC','points','Ej. 2 para IPC + 2 puntos; 0 para solo IPC.'),
            'history_calendar'=>$f('¿Cuándo aumenta el canon?','select','Enero es la referencia inicial solicitada; ajusta la fecha a lo pactado.',
                ['january'=>'Cada enero','anniversary'=>'En el aniversario del inicio','custom'=>'Otra fecha anual']),
            'history_admin_amount'=>$f('Administración inicial de referencia (COP/mes)','money','Cuota de PH al inicio informado; 0 si no aplica. No se toma la cuota actual como histórica.'),
            'history_admin_date'=>$f('¿Desde cuándo conoces esa cuota PH?','date','Opcional: vacío usa el inicio del arriendo. Los meses anteriores quedan pendientes.'),
            'history_admin_rule'=>$f('Aumento de administración PH','select','Referencia editable; la cuota aprobada por la copropiedad puede ser distinta.',
                ['ipc'=>'IPC cada enero','constant'=>'Cuota constante']),
            'history_vat_rate'=>$f('IVA de referencia sobre el canon (%)','percent','Tarifa aplicable según soporte; 0 si no aplica. No incluye la administración PH.'),
            'admin_adjustments'=>['type'=>'adjustments'],
            'vacancy_periods'=>['type'=>'vacancies'],
        ];
    }
    public static function ipc(): array
    {
        // DANE, anexo diciembre 2025, hoja 1, A10:D19, variación anual nacional.
        return ['rates'=>[2016=>5.75,2017=>4.09,2018=>3.18,2019=>3.80,2020=>1.61,2021=>5.62,
            2022=>13.12,2023=>9.28,2024=>5.20,2025=>5.10], 'updated'=>'2026-01-08',
            'source'=>'https://www.dane.gov.co/files/operaciones/IPC/dic2025/anex-IPC-dic2025.xlsx'];
    }
}
