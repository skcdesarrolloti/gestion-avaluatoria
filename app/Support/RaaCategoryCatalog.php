<?php
declare(strict_types=1);
namespace App\Support;

final class RaaCategoryCatalog
{
    public static function all(): array
    {
        return [
            '1' => 'Inmuebles urbanos',
            '2' => 'Inmuebles rurales',
            '3' => 'Recursos naturales y suelos de protección',
            '4' => 'Obras de infraestructura',
            '5' => 'Edificaciones de conservación arqueológica y monumentos históricos',
            '6' => 'Inmuebles especiales',
            '7' => 'Maquinaria fija, equipos y maquinaria móvil',
            '8' => 'Maquinaria y equipos especiales',
            '9' => 'Obras de arte, orfebrería, patrimoniales y similares',
            '10' => 'Semovientes y animales',
            '11' => 'Activos operacionales y establecimientos de comercio',
            '12' => 'Intangibles',
            '13' => 'Intangibles especiales',
        ];
    }

    public static function labels(string $encoded): array
    {
        $codes = json_decode($encoded, true);
        if (!is_array($codes)) {
            return $encoded !== '' ? [$encoded] : [];
        }
        return array_values(array_filter(array_map(fn ($code) => self::all()[(string) $code] ?? '', $codes)));
    }
}
