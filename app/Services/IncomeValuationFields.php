<?php
declare(strict_types=1);
namespace App\Services;

/** Definitions shared by the forms and server validation. No assumed market rates. */
final class IncomeValuationFields
{
    public static function group(string $group): array
    {
        $f=static fn($label,$type='text',$help='',$options=[])=>compact('label','type','help','options');
        return match($group) {
            'projection'=>[
                'date'=>$f('Fecha provisional del escenario','date','Solo si falta fecha de valoración en el encargo. Al completarla allí se consulta en origen. Primer mes prorrateado por días restantes.'),
                'months'=>$f('Horizonte explícito (meses)','integer','Ej. 60. Justifica el plazo; máximo técnico 600 meses.'),
                'income_source'=>$f('Renta de partida','select','Consulta el contrato o documenta una estimación de mercado.', ['lease'=>'Canon del contrato vinculado','market'=>'Canon de mercado sustentado']),
                'lease_id'=>$f('Contrato de la etapa 1','lease','El canon se consulta en origen, sin copiarlo. Los anexos incluidos no se suman otra vez.'),
                'market_rent'=>$f('Canon de mercado mensual (COP)','money','Ej. 3500000. Sin administración ni IVA; también se usa después del contrato si eliges recolocación.'),
                'income_support'=>$f('Soporte del canon y alcance','textarea','Fuente, fecha, áreas y anexos incluidos; justifica el canon representativo.'),
                'growth'=>$f('Incremento anual proyectado (%)','rate','Ej. 5,2. Escribe 0 para canon constante; no se descarga ni presume el IPC.'),
                'next_increase'=>$f('Primera fecha de incremento proyectado','date','Según contrato o hipótesis de mercado documentada. Luego cada aniversario.'),
                'after_contract'=>$f('Al finalizar el contrato','select','Revisa vencimiento y renovación en la etapa 1.', ['stop'=>'Sin ingresos después del vencimiento','market'=>'Recolocar con el canon de mercado indicado']),
                'relet_date'=>$f('Fecha prevista de recolocación','date','Después del contrato. Los días entre ambos quedan sin renta.'),
                'vacancy'=>$f('Vacancia esperada adicional (%)','percent','0 si ya representaste toda la desocupación mediante fechas o eventos. Base: renta potencial.'),
                'arrears'=>$f('Pérdida esperada por no pago (%)','percent','Base: renta después de vacancia. Mora recuperable no equivale a pérdida.'),
                'loss_support'=>$f('Soporte de vacancia y pérdidas','textarea','Historial, segmento, ciudad, periodo y fuente. Explica qué riesgo ya está en el calendario.'),
                'projection_support'=>$f('Soporte del horizonte y crecimiento','textarea','Cláusula e índice, o escenario de mercado; fechas, supuestos de renovación y limitaciones.'),
                'cost_review'=>$f('Revisión de costos del propietario','select','Confirma solo después de registrar todos los aplicables y evitar duplicaciones con PH.', ['confirmed'=>'Costos aplicables revisados con soporte']),
                'cost_support'=>$f('Alcance de la revisión de costos','textarea','Explica costos no aplicables, cubiertos por PH o asumidos por el arrendatario; respalda también importes cero.'),
            ],
            'costs'=>[
                'name'=>$f('Concepto','text','Ej. Predial, PH, seguros, mantenimiento o gestión inmobiliaria'),
                'payer'=>$f('Responsable / aplicabilidad','select','Solo se deduce el costo a cargo del propietario.', ['owner'=>'Propietario','tenant'=>'Arrendatario','na'=>'No aplica']),
                'amount'=>$f('Importe o porcentaje','money','Escribe 0 si el costo es cero y explica por qué.'),
                'basis'=>$f('Base del importe','select','Selecciona la unidad del monto anterior.', ['monthly'=>'COP por mes','annual'=>'COP por año','potential'=>'% de renta potencial','effective'=>'% de ingreso efectivo']),
                'growth'=>$f('Incremento anual del costo (%)','rate','0 para costo constante. No se iguala automáticamente al incremento del canon.'),
                'source'=>$f('Soporte, fecha y alcance','textarea','Recibo, póliza, cotización o muestra local; indica si PH ya cubre este gasto.'),
            ],
            'events'=>[
                'month'=>$f('Mes del evento','month','Dentro del horizonte de proyección.'),
                'rent'=>$f('Canon mensual de reemplazo (COP)','money','Opcional. 0 representa un mes sin canon; vacío conserva la proyección.'),
                'extra'=>$f('Gasto extraordinario del propietario (COP)','money','Opcional. Importe total en ese mes, no se prorratea.'),
                'capex'=>$f('Inversión / reposición (COP)','money','Opcional. Se deduce del flujo después del ingreso operativo neto.'),
                'source'=>$f('Soporte del evento','textarea','Fecha, presupuesto, gracia o modificación contractual; no repitas un gasto ya incluido.'),
            ],
            'adoption'=>[
                'terminal_mode'=>$f('Al terminar el horizonte','select','No confundir fin de proyección con fin de la capacidad de generar renta.', ['capitalized'=>'Capitalizar ingreso estabilizado del año siguiente','none'=>'Sin valor remanente · requiere justificación']),
                'direct_id'=>$f('Tasa para capitalización directa','candidate','Elige una candidata de capitalización compatible con el ingreso bruto o neto.'),
                'discount_id'=>$f('Tasa de descuento del FCD','candidate','Flujos nominales en COP antes de financiación e impuesto de renta.'),
                'exit_id'=>$f('Tasa para el valor de salida','candidate','Capitalización de NOI o perpetuidad de flujo libre, según la candidata.'),
                'direct_reason'=>$f('Justificación de la tasa directa','textarea','Comparabilidad, fuentes y razón de elección. No se promedian alternativas automáticamente.'),
                'discount_reason'=>$f('Justificación del descuento','textarea','Plazo, moneda, riesgos y coherencia con el flujo del inmueble.'),
                'exit_reason'=>$f('Justificación de la salida','textarea','Condición esperada, crecimiento sostenible y evidencia al final del horizonte.'),
                'terminal_growth'=>$f('Crecimiento anual después del horizonte (%)','rate','0 para estabilidad. Se aplica al NOI anualizado del último mes; en flujo libre primero se descuenta la reposición anual.'),
                'terminal_capex'=>$f('Reposición anual estabilizada (COP)','money','Requerida para perpetuidad de flujo libre. 0 debe estar sustentado; no duplicar reservas.'),
                'selling_cost'=>$f('Gastos de venta a la salida (%)','percent','0 si no corresponden según la base de valor; justificar en la salida.'),
                'conclusion'=>$f('Comparación y limitaciones de ambos métodos','textarea','Explica diferencias y pendientes. Los resultados no se suman ni se adoptan como avalúo automáticamente.'),
            ],
            default=>[],
        };
    }
    public static function rates(): array
    {
        $f=static fn($label,$type='rate',$help='',$options=[])=>compact('label','type','help','options');
        return [
            'id'=>$f('Identificador','id'), 'name'=>$f('Nombre de la alternativa','text','Ej. Oficinas del mismo edificio · contrato y venta verificados'),
            'purpose'=>$f('Aplicación','select','Cada tasa tiene una función.', ['direct'=>'Capitalización directa','discount'=>'Descuento FCD','exit'=>'Capitalización terminal']),
            'method'=>$f('Forma de obtenerla','select','Consulta las ayudas antes de escoger.', ['market'=>'Relación renta / precio','build'=>'Referencia más prima sustentada','capm'=>'CAPM del activo · avanzado','wacc'=>'Costo ponderado antes de impuesto de renta · avanzado','manual'=>'Tasa documentada','gordon'=>'Perpetuidad: descuento menos crecimiento']),
            'basis'=>$f('Ingreso al que corresponde','select','Evita aplicar tasa bruta a ingreso neto.', ['gross'=>'Renta bruta potencial','net'=>'Ingreso neto operativo','cash'=>'Flujo del activo después de reposiciones']),
            'income'=>$f('Ingreso anual comparable (COP)','money','Renta anual del mismo alcance y nivel bruto/neto que el precio; cita ambos soportes.'),
            'price'=>$f('Precio comparable (COP)','money','Venta u oferta identificada y verificada; no cruces anuncios sin comprobar comparabilidad.'),
            'reference'=>$f('Referencia financiera anual (%)','rate','Nominal en COP y plazo compatible; cita dato, fecha y fuente.'),
            'premium'=>$f('Prima sustentada (%)','rate','Puntos porcentuales adicionales; evita duplicar riesgos del flujo o de la referencia.'),
            'beta'=>$f('Beta desapalancado del activo','number','Sin unidad. No usar beta + R² ni trasladar promotoras a oficinas sin soporte.'),
            'equity'=>$f('Costo del patrimonio anual (%)','rate','Nominal COP antes de impuesto de renta; compatible con los flujos definidos.'),
            'debt'=>$f('Costo de deuda anual (%)','rate','Nominal COP antes de impuesto de renta; sin escudo fiscal en esta convención.'),
            'debt_weight'=>$f('Participación de deuda (%)','percent','El resto es patrimonio. Debe representar participantes del mercado para ese encargo.'),
            'rate'=>$f('Tasa documentada anual (%)','rate','Ej. 8,5. En capitalización es un cociente anual; en descuento, efectiva anual.'),
            'date'=>$f('Fecha de la evidencia','date','Identifica la fecha a la que corresponde la alternativa.'),
            'source'=>$f('Fuentes, datos y limitaciones','textarea','Documentos o enlaces de cada entrada, moneda, plazo, bruto/neto y comparabilidad. Los datos de otros países necesitan adaptación.'),
        ];
    }
}
