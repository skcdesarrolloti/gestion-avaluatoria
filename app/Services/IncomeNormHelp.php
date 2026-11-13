<?php
declare(strict_types=1);
namespace App\Services;
final class IncomeNormHelp
{
    public static function get(string $key): array
    {
        return match($key) {
            'rent'=>['24','Investigar contratos vigentes y arriendos comparables permite contrastar el canon contractual con el mercado. La elección del canon capitalizable necesita sustento; estar arrendado no basta para adoptar ese canon.'],
            'basis'=>['23','Renta y tasa de capitalización deben usar la misma base, bruta o neta. En la renta neta se deducen los gastos que corresponden al propietario; los anexos ya incluidos en el canon no se suman nuevamente.'],
            'costs'=>['26.1','Revisar predial, seguros, mantenimiento y reparaciones menores, administración, servicios cuando apliquen, imprevistos y otros gastos operativos, y comisión inmobiliaria. La norma no asigna porcentajes universales por tipo.'],
            'history'=>['26.2','El flujo organiza entradas y salidas en el tiempo y admite detalle mensual. Aquí distinguimos historia documentada y proyección: reconstruir el canon no demuestra que se haya pagado. Esta distinción es una ayuda de aplicación.'],
            'rates'=>['23 y 26.3','Preferir evidencia comparable de renta y venta para la capitalización; una tasa construida requiere soporte técnico. La tasa de descuento considera alternativas de inversión y riesgos del inmueble. No son tasas intercambiables.'],
            'terminal'=>['25','El FCD combina beneficios futuros descontados y valor continuo al finalizar el horizonte. Distingue tasa de descuento y tasa terminal de capitalización; documenta su coherencia con los ingresos y gastos.'],
            default=>['26','Identifica ingresos, gastos, fechas y soporte técnico de los supuestos del flujo.'],
        };
    }
    public static function field(string $group,string $field): ?string
    {
        if ($group==='costs') return in_array($field,['amount','basis','source'],true)?'costs':null;
        return match($field) {
            'income_source','lease_id','market_rent','income_support'=>'rent',
            'vacancy','arrears','cost_review'=>'costs',
            'growth','months','next_increase','after_contract'=>'history',
            'direct_id','discount_id','method'=>'rates',
            'terminal_mode','exit_id','terminal_growth'=>'terminal',
            'basis'=>'basis',default=>null,
        };
    }
}
