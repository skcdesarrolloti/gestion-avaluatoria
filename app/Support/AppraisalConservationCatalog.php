<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalConservationCatalog
{
    private static ?array $data = null;

    public static function all(): array
    {
        if (self::$data !== null) return self::$data;
        $path = BASE_PATH . '/resources/data/conservacion_igac_941_catalogo.json';
        $json = is_file($path) ? file_get_contents($path) : '{}';
        $data = json_decode((string) $json, true);
        self::$data = is_array($data) ? $data : [];
        return self::$data;
    }

    public static function groups(): array { return self::all()['groups'] ?? []; }
    public static function states(): array { return self::all()['states'] ?? []; }
    public static function interventions(): array { return self::all()['interventions'] ?? []; }
    public static function version(): string { return (string) (self::all()['version'] ?? ''); }
    public static function algorithmVersion(): string { return (string) (self::all()['algorithm_version'] ?? ''); }
    public static function technicalReference(): string { return (string) (self::all()['technical_reference'] ?? ''); }
    public static function sources(): array { return self::all()['sources'] ?? []; }

    public static function materialsFor(string $subcomponent): array
    {
        return self::all()['materials'][$subcomponent] ?? [];
    }

    public static function findingsFor(string $subcomponent): array
    {
        return self::all()['findings'][$subcomponent] ?? [];
    }

    public static function stateLabel(string $value): string
    {
        foreach (self::states() as $state) {
            if ((string) ($state['value'] ?? '') === $value) {
                return trim($value . ' ' . (string) ($state['label'] ?? ''));
            }
        }
        return $value;
    }

    public static function subcomponentIndex(): array
    {
        $index = [];
        foreach (self::groups() as $group) {
            foreach (($group['subcomponents'] ?? []) as $subcomponent) {
                $index[(string) $subcomponent['id']] = [$group, $subcomponent];
            }
        }
        return $index;
    }
}
