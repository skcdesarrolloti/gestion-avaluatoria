<?php
declare(strict_types=1);
namespace App\Services;

/** One academic reading per active method; unit evidence remains independent. */
final class MethodologyAcademy
{
    public static function groups(array $components, array $flow): array
    {
        $groups=[];
        foreach (MethodologyValuationPlan::working($components) as $key=>$component) {
            $method=$flow[$key]['method'] ?? '';
            if (!isset(MethodologyWorkflow::METHODS[$method])) continue;
            $groups[$method][$key]=$component;
        }
        return array_replace(array_intersect_key(MethodologyWorkflow::METHODS, $groups), $groups);
    }
}
