<?php
declare(strict_types=1);
namespace App\Services;

final class LeaseRegister
{
    public static function read(array $record): array
    { return json_decode($record['lease_register'] ?? '{}',true,16,JSON_THROW_ON_ERROR) ?: []; }

    public static function forUnit(array $record,string $unit): array
    { return array_filter(self::read($record),static fn($lease)=>in_array($unit,$lease['unit_ids'] ?? [],true)); }

    public static function economic(array $lease,array $record): array
    {
        return array_intersect_key(($lease['economic_source'] ?? '')==='assignment'?$record:$lease,
            array_flip(LeaseFields::ECONOMIC));
    }

    public static function legacy(array $record,string $unit): array
    {
        $out=[];
        foreach (MethodologyWorkflow::saved($record) as $key=>$state) {
            if ($key!==$unit && !str_starts_with($key,$unit.':')) continue;
            $values=$state['income_concept'] ?? $state['method_studies']['renta']['income_concept'] ?? [];
            if ($values) $out[$key]=$values;
        }
        return $out;
    }

    public static function title(array $lease): string
    { return trim($lease['reference'] ?? '') ?: 'Contrato pendiente de identificar'; }
}
