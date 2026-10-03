<?php
declare(strict_types=1);
namespace App\Services;

final class MethodologySelectionHelp
{
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
