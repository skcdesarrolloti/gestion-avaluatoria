<?php
declare(strict_types=1);
namespace App\Services;

/** Preguntas conceptuales aprobadas; sin tasas, porcentajes ni inferencias económicas. */
final class IncomePreparationQuestions
{
    public static function all(): array
    {
        $out = [
            'rental_scope'=>self::question('¿El arriendo comprende la misma unidad y los mismos anexos del estudio?',
                ['same'=>'Sí, coincide','different'=>'No, comprende otros espacios','pending'=>'Por verificar'],
                'Identifica los espacios que respaldan el ingreso; esta respuesta no cambia el alcance configurado.',
                'Relacionar el arriendo con la unidad sin duplicar anexos ni mezclar otros inmuebles.',
                'El apartamento y el parqueadero pueden tener un solo contrato o acuerdos distintos.',
                'Contrato, escritura e identificación de unidades y anexos del capítulo 3.', 'subject'),
            'readiness'=>self::question('¿Hay condiciones pendientes para poder arrendarlo?',
                ['none'=>'No se identifican según los soportes revisados','works'=>'Requiere adecuaciones o reparaciones',
                    'restriction'=>'Existe una restricción o impedimento','pending'=>'Por verificar'],
                'Consulta primero construcción, propiedad horizontal y obsolescencias.',
                'Reconocer qué condiciona la disponibilidad real del inmueble.',
                'En un apartamento revisa acceso y ascensor; en una bodega, acceso de carga y servicios necesarios.',
                'Visita, fotografías, capítulos 3.3–3.6 y documentos de la unidad.', 'subject'),
            'canon_scope'=>self::question('¿Está documentado qué comprende el canon?',
                ['yes'=>'Sí, está identificado','partial'=>'Solo parcialmente','unknown'=>'No se conoce','na'=>'No existe canon actual'],
                'Consulta el canon informado, periodicidad, administración e IVA del encargo.',
                'Distinguir el arriendo del inmueble de otros cobros y comprobar a qué unidad corresponde.',
                'El canon puede incluir administración y parqueadero, o cobrarlos por separado.',
                'Contrato, modificaciones y contexto de renta del encargo.', 'lease'),
        ];
        $payers=['owner'=>'Propietario','tenant'=>'Arrendatario','shared'=>'Responsabilidad repartida',
            'na'=>'No aplica, con explicación','pending'=>'Por verificar'];
        foreach (['ph'=>'Administración de PH','tax'=>'Impuesto predial','insurance'=>'Seguros',
            'maintenance'=>'Mantenimiento y reparaciones','utilities'=>'Servicios públicos',
            'management'=>'Administración inmobiliaria'] as $key=>$label) {
            $out['cost_'.$key]=self::question('¿Quién asume finalmente: '.$label.'?', $payers,
                'Consultar primero contrato, encargo y PH. No se piden importes ni porcentajes.',
                'Distinguir quién paga inicialmente de quién soporta finalmente el gasto, sin descontarlo dos veces.',
                'Un pago puede recuperarse del arrendatario según el acuerdo documentado.',
                'Cláusula, factura o liquidación; indicar reparto y recuperaciones en la nota de gastos.', 'evidence');
        }
        $availability=['yes'=>'Sí, con soportes','verbal'=>'Solo información verbal','none'=>'No disponible','pending'=>'Por verificar'];
        foreach (['occupancy'=>'Ocupación y períodos sin arrendar','payment'=>'Cobros y falta de pago del arriendo',
            'cost'=>'Gastos, reparaciones y adecuaciones'] as $key=>$label) {
            $out['history_'.$key]=self::question('¿Qué antecedentes tenemos de: '.$label.'?', $availability,
                'Identifica la disponibilidad del registro y su período; no estima tasas.',
                'Preparar evidencia para investigar el comportamiento del inmueble y su mercado.',
                'Estar sin arrendar y tener un arrendatario que no paga son situaciones distintas.',
                'Contratos anteriores, actas de entrega, registros de cobro y facturas fechadas.', 'evidence');
        }
        return $out;
    }

    private static function question(string $label,array $values,string $meaning,string $why,string $example,string $support,string $group): array
    {
        $references=$group==='evidence'?['IGAC 941 de 2026, arts. 23 y 26']:['IGAC 941 de 2026, arts. 22–24'];
        return compact('label','values','meaning','why','example','support','group','references');
    }

    public static function group(string $name): string
    {
        return ['scope'=>'subject','occupancy'=>'lease','support'=>'lease'][$name]
            ?? (self::all()[$name]['group'] ?? 'summary');
    }
}
