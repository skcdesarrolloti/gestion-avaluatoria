<?php
declare(strict_types=1);
namespace App\Services;

/** Classification is distinct from an economic effect; historical scales stay readable. */
final class ResearchScalePolicy
{
    public static function valid(string $key,array $factor): bool
    {
        $base=ResearchFactorCatalog::all()[$key] ?? null;
        if (!$base || $factor['kind']!==$base['kind']) return false;
        return ($base['kind']!=='ordinal' && $key!=='view') || ($factor['categories'] ?? '')===$base['categories'];
    }
    public static function help(string $key): string
    {
        return match($key) {
            'view'=>'Cuatro clases: Sin vista; Interior; Exterior: calles y avenidas; Exterior: paisajística. Se aplica la misma clasificación al sujeto y a los comparables. Esquinera no es una vista. No publicado queda pendiente; el orden de la lista no asigna incrementos de precio.',
            'access'=>'Peatonal, vehicular y mixto describen modalidades. Cargue y restricciones son condiciones adicionales: califícalas por separado; restringido no es un nivel superior.',
            'finishes'=>'El catálogo anterior mezcla calidad y estado de ejecución. Usa Calidad de acabados terminados para la jerarquía; obra gris no recibe un grado de acabado terminado.',
            'service'=>'Alcoba y baño de servicio son dotaciones distintas; una no es automáticamente superior a la otra.',
            'stratum'=>'Clasificación socioeconómica: el número identifica el estrato; no supone diferencias iguales de precio.',
            default=>'',
        };
    }
}
