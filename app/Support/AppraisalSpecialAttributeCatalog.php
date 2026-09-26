<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeCatalog
{
    public static function __callStatic(string $name, array $arguments): array
    {
        return AppraisalSpecialAttributeOptions::$name(...$arguments);
    }

    public static function groups(string $propertyType = ''): array
    {
        $groups = self::allGroups();
        $specific = match (self::normalizedType($propertyType)) {
            'vivienda' => ['vivienda'],
            'local_comercial' => ['local_comercial'],
            'oficina_consultorio' => ['oficina_consultorio'],
            'consultorio_salud' => ['oficina_consultorio', 'consultorio_salud'],
            'bodega_industrial' => ['bodega_industrial'],
            'lote' => ['lote'],
            'edificio' => ['edificio_integral', 'local_comercial', 'oficina_consultorio'],
            'finca_rural' => ['lote', 'vivienda', 'finca_rural'],
            'hotel_hospedaje' => ['hotel_hospedaje'],
            'parqueadero' => ['parqueadero'],
            default => [],
        };
        return array_intersect_key($groups, array_flip(array_merge(['comun'], $specific)));
    }

    private static function normalizedType(string $propertyType): string
    {
        $text = mb_strtolower(trim($propertyType));
        $text = strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
        if ($text === '') return '';
        if (str_contains($text, 'consultorio')) return 'consultorio_salud';
        if (str_contains($text, 'parqueadero') || str_contains($text, 'garaje')) return 'parqueadero';
        if (str_contains($text, 'hotel') || str_contains($text, 'hospedaje')) return 'hotel_hospedaje';
        if (str_contains($text, 'finca') || str_contains($text, 'rural')) return 'finca_rural';
        if (str_contains($text, 'lote') || str_contains($text, 'terreno')) return 'lote';
        if (str_contains($text, 'bodega') || str_contains($text, 'industrial') || str_contains($text, 'logistic')) return 'bodega_industrial';
        if (str_contains($text, 'oficina')) return 'oficina_consultorio';
        if (str_contains($text, 'local') || str_contains($text, 'comerc')) return 'local_comercial';
        if (str_contains($text, 'edificio')) return 'edificio';
        if (str_contains($text, 'apart') || str_contains($text, 'casa') || str_contains($text, 'vivienda') || str_contains($text, 'hotel')) return 'vivienda';
        return $text;
    }

    public static function typologyTabs(): array
    {
        return [
            'casa' => 'Casa', 'apartamento' => 'Apartamento', 'lote' => 'Lote',
            'local' => 'Local', 'oficina' => 'Oficina', 'bodega' => 'Bodega',
            'consultorio' => 'Consultorio', 'edificio' => 'Edificio', 'finca' => 'Finca',
            'hotel' => 'Hotel / hospedaje', 'parqueadero' => 'Parqueadero',
        ];
    }

    public static function catalogTypeKey(string $propertyType): string
    {
        $text = mb_strtolower(trim($propertyType));
        $text = strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
        foreach (array_keys(self::typologyTabs()) as $key) if ($text === $key || str_contains($text, $key)) return $key;
        return match (self::normalizedType($propertyType)) {
            'vivienda' => 'apartamento', 'local_comercial' => 'local',
            'oficina_consultorio' => str_contains($text, 'consultorio') ? 'consultorio' : 'oficina',
            'bodega_industrial' => 'bodega', 'lote' => 'lote',
            'edificio' => 'edificio', 'parqueadero' => 'parqueadero',
            default => 'apartamento',
        };
    }

    public static function allGroups(): array
    {
        return AppraisalSpecialAttributeGroups::all();
    }

    public static function flatKeys(): array
    {
        $keys = [];
        foreach (self::allGroups() as $group) $keys = array_merge($keys, array_keys($group[1]));
        return $keys;
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::allGroups() as $group) foreach ($group[1] as $key => $attribute) $labels[$key] = (string) $attribute[0];
        return $labels;
    }

    public static function selectOptions(): array
    {
        return [
            'state' => ['' => 'No verificado', 'bueno' => 'Bueno', 'regular' => 'Regular', 'malo' => 'Malo', 'no_aplica' => 'No aplica'],
            'impact' => ['' => 'No definido', 'positivo_alto' => 'Positivo alto', 'positivo_medio' => 'Positivo medio',
                'neutro' => 'Neutro', 'negativo_medio' => 'Negativo medio', 'negativo_alto' => 'Negativo alto'],
            'evidence' => ['' => 'No verificado', 'foto' => 'Foto', 'visita' => 'Visita', 'documento' => 'Documento',
                'anuncio' => 'Anuncio', 'declaracion' => 'Declaración'],
            'rating' => ['' => 'Sin calificar', '1' => '1 Muy desfavorable', '2' => '2 Desfavorable',
                '3' => '3 Normal', '4' => '4 Favorable', '5' => '5 Muy favorable'],
            'weight' => ['' => 'Sin peso', '1' => 'Bajo', '2' => 'Medio', '3' => 'Alto'],
        ];
    }

}
