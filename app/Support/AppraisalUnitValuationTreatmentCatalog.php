<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalUnitValuationTreatmentCatalog
{
    public static function options(): array
    {
        return [
            'principal' => 'Unidad principal del avalúo',
            'integrado' => 'Integrado al inmueble principal',
            'separado_mercado' => 'Valorar separado por mercado',
            'reposicion' => 'Valorar por reposición / costo',
            'descriptivo' => 'Solo descriptivo, sin valor separado',
        ];
    }

    public static function allowed(): array
    {
        return array_keys(self::options());
    }

    public static function defaultFor(string $kind, string $constructionType): string
    {
        if ($kind === 'property') return 'principal';
        if (in_array($constructionType, ['piscina', 'kiosco', 'ramada', 'cerramiento', 'muro', 'porton', 'placa', 'cubierta', 'otro'], true)) return 'reposicion';
        return 'integrado';
    }
}
