<?php
declare(strict_types=1);
namespace App\Services;

final class MethodologySelectionHelp
{
    public static function example(array $record, ?array $component): array
    {
        if ($component===null) return ['Ejemplo genérico: un bien y varios métodos',
            'Un mismo bien puede estudiarse con Mercado y, si tiene ingresos sustentables, con Renta. Sus resultados se contrastan, no se suman. Al elegir una unidad, el ejemplo se adapta a su alcance.'];
        $unit=$component['unit']; $label=$component['label'];
        if (($unit['unit_kind'] ?? '')==='annex') return ['Ejemplo para '.$label.' · anexo',
            'Si este anexo está incluido en el valor de la unidad principal, documenta su inclusión sin sumarlo otra vez. Si corresponde valorarlo por separado, verifica sus derechos y el soporte del método elegido. Mercado requiere evidencia comparable; Costo requiere costos y depreciación sustentados cuando correspondan. El nombre del anexo no determina su naturaleza jurídica.'];
        if (($record['regimen_ph'] ?? '')==='si') return ['Ejemplo para '.$label.' · PH',
            'Puedes estudiar esta unidad por Mercado si cuentas con ofertas o transacciones comparables, y contrastarla por Renta si sus ingresos pueden sustentarse. Son estimaciones del mismo alcance y no se suman. Precisa las áreas privadas y derechos registrados, y si el precio o el canon incluyen garajes, depósitos u otros anexos.'];
        if (($unit['method_structure'] ?? '')==='solo_terreno' || ($unit['property_type'] ?? '')==='lote') return ['Ejemplo para '.$label.' · terreno',
            'Si existen ofertas o transacciones de terrenos comparables, puedes estudiar Mercado. Otra posibilidad es Residual, cuando exista un proyecto viable y sus supuestos puedan sustentarse. El analista elige la técnica según la evidencia; no se agrega una construcción que no forme parte del alcance.'];
        if (($unit['method_structure'] ?? '')==='solo_construccion') return ['Ejemplo para '.$label.' · construcción',
            'Para estudiar la construcción por Costo, sustenta el costo a nuevo, los costos directos e indirectos y la depreciación que corresponda. Identifica el terreno o las partidas consideradas en otros estudios para no incluirlas otra vez.'];
        if (($record['regimen_ph'] ?? '')==='no' && (($unit['method_structure'] ?? '')==='lote_construccion'
            || in_array($unit['property_type'] ?? $record['tipo_inmueble'] ?? '',['casa','finca'],true))) return ['Ejemplo para '.$label.' · terreno y construcción',
            'Puedes estudiar el inmueble completo o separar terreno y construcción cuando corresponda. Por ejemplo, Mercado para el terreno y Costo para la construcción, si cada estimación tiene soporte. Sus coberturas deben ser distintas antes de integrarlas. Costo no obtiene automáticamente el terreno por diferencia cuando faltan ofertas de lotes.'];
        return ['Ejemplo genérico para '.$label,
            'Elige el método según los derechos, el alcance y la evidencia disponible. Si estudias el mismo alcance con más de un método, contrasta los resultados sin sumarlos. Este ejemplo es general porque no se ha identificado un caso específico con los datos registrados.'];
    }

    public static function methods(string $label): array
    {
        $subject='Se estudia '.$label;
        return [
            'mercado'=>['concept'=>'Estima el valor a partir de ofertas o transacciones de inmuebles comparables, verificadas y analizadas.',
                'reason'=>'Se propone Mercado para '.$label.'. Se investigarán ofertas o transacciones comparables y se verificará su semejanza, fuente, fecha, áreas y componentes incluidos. La adopción del valor queda sujeta a la calidad y suficiencia de la evidencia.',
                'coverage'=>$subject.' con sus áreas y derechos registrados. Se precisará si el precio incluye garajes, depósitos u otros anexos y cómo se tratarán. No se sumarán componentes que ya estén incluidos en el valor principal.'],
            'costo'=>['concept'=>'Estudia el costo a nuevo de construcciones y anexos y su depreciación; el terreno requiere una estimación sustentada por separado.',
                'reason'=>'Se propone Costo para estudiar las construcciones o anexos de '.$label.'. Se sustentará el costo a nuevo, sus fuentes, los costos directos e indirectos y, cuando corresponda, la depreciación Ross–Heideck con edad, vida útil y conservación verificadas.',
                'coverage'=>$subject.' según el alcance constructivo documentado. Se identificarán las partidas incluidas, los elementos comunes y el terreno considerado por otro estudio, para evitar duplicaciones. Un presupuesto de retiro se distinguirá del valor de la construcción.'],
            'renta'=>['concept'=>'Estima el valor por la capacidad de generar ingresos, mediante capitalización directa o flujos de caja descontados.',
                'reason'=>'Se propone Renta para '.$label.'. Se verificarán los ingresos observados o potenciales, la ocupación, los gastos y las tasas aplicables. La técnica se justificará con información suficiente y fuentes identificadas.',
                'coverage'=>$subject.' a partir de los derechos y componentes que generan los ingresos analizados. Se precisará si garajes, depósitos u otros anexos están incluidos en el canon. Si existe otro método para el mismo alcance, se contrastarán los resultados sin sumarlos.'],
            'residual'=>['concept'=>'Estima normalmente el terreno a partir de un proyecto viable, sus ventas, costos, tiempos y demás supuestos sustentados.',
                'reason'=>'Se propone Residual para '.$label.'. Se estudiará un proyecto compatible con la normativa urbana y el mercado, sustentando ventas, costos, tiempos y riesgos. La selección de la técnica estática o dinámica dependerá de la información disponible.',
                'coverage'=>$subject.' conforme al proyecto y los derechos documentados. Se identificarán las áreas aprovechables, las obligaciones y los costos considerados. No se confundirá este estudio con una resta automática entre el precio del inmueble y el costo de la construcción.'],
        ];
    }

    public static function treatments(): array
    {
        return ['separado'=>'Esta unidad o parte tendrá un valor identificado por separado. Antes de integrar resultados, comprueba que no esté incluido en otra partida. Los métodos alternativos del mismo alcance se contrastan, no se suman.',
            'integrado'=>'Su incidencia económica se considera dentro del valor de otra unidad principal. Identifica cuál la contiene y documenta su inclusión; no se añade nuevamente como valor independiente.',
            'descriptivo'=>'La unidad o anexo se describe y documenta, pero no se adopta un valor monetario separado en este estudio. Explica la razón y su relación con el inmueble.'];
    }
}
