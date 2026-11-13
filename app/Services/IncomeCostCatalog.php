<?php
declare(strict_types=1);
namespace App\Services;

/** Rubros del art. 26, sin atribuir porcentajes de mercado a la norma. */
final class IncomeCostCatalog
{
    public static function all(): array
    {
        return [
            'tax'=>['label'=>'Impuesto predial','help'=>'Liquidación vigente. Su tarifa fiscal tiene otra base; no es un porcentaje del arriendo.'],
            'insurance'=>['label'=>'Seguros','help'=>'Póliza o cotización para el uso, coberturas y valor asegurado del inmueble.'],
            'maintenance'=>['label'=>'Mantenimiento y reparaciones menores','help'=>'Pagos históricos y presupuesto según estado y uso; separar reposiciones de capital.'],
            'administration'=>['label'=>'Gastos de administración / PH','help'=>'Cuotas y gastos a cargo del propietario. No duplicar cobros al arrendatario ni gastos cubiertos por PH.'],
            'utilities'=>['label'=>'Servicios públicos','help'=>'Solo consumos y cargos asumidos por el propietario, incluidos períodos de vacancia.'],
            'contingencies'=>['label'=>'Imprevistos y otros gastos operativos','help'=>'Identificar el riesgo o concepto, su evidencia y por qué no está cubierto en otro rubro.'],
            'commission'=>['label'=>'Comisión por administración inmobiliaria','help'=>'Contrato o cotización del administrador, con base y periodicidad explícitas.'],
        ];
    }
}
