<?php
declare(strict_types=1);
namespace App\Support;

final class UrbanResidentialNormCatalog
{
    public static function modalities(): array
    {
        return [
            'unifamiliar_1' => 'Unifamiliar 1 piso',
            'unifamiliar_2' => 'Unifamiliar 2 pisos',
            'bifamiliar' => 'Bifamiliar',
            'multifamiliar' => 'Multifamiliar',
        ];
    }

    public static function standards(): array
    {
        $rows = [
            'res-a' => ['label' => 'Residencial tipo A', 'height' => '2 pisos', 'data' => [
                'unifamiliar_1' => [120, 8, 0.6, 'De acuerdo con aislamientos', '1 por cada 10 viviendas'],
                'unifamiliar_2' => [90, 6, 1.0, '1 m2 libre por cada 0.80 m2 de área construida', '1 por cada 10 viviendas'],
                'bifamiliar' => [200, 10, 1.1, '1 m2 libre por cada 0.80 m2 de área construida', '1 por cada 10 viviendas'],
            ]],
            'res-b' => ['label' => 'Residencial tipo B', 'height' => '4 pisos', 'data' => [
                'unifamiliar_1' => [200, 8, 0.6, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'unifamiliar_2' => [160, 8, 1.0, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'bifamiliar' => [250, 10, 1.1, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'multifamiliar' => [480, 16, 1.2, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 70 m2 y visitantes 1 por cada 210 m2'],
            ]],
            'res-c' => ['label' => 'Residencial tipo C', 'height' => 'Según área libre e índice de construcción', 'data' => [
                'unifamiliar_1' => [250, 10, 0.6, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'unifamiliar_2' => [200, 8, 1.0, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'bifamiliar' => [300, 10, 1.2, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'multifamiliar' => [600, 20, 2.4, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 y visitantes 1 por cada 400 m2'],
            ]],
            'res-d' => ['label' => 'Residencial tipo D', 'height' => 'Según área libre e índice de construcción', 'data' => [
                'unifamiliar_1' => [360, 12, 0.6, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'unifamiliar_2' => [200, 10, 1.0, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'bifamiliar' => [300, 12, 1.2, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 de área construida'],
                'multifamiliar' => [750, 25, 2.4, '1 m2 libre por cada 0.80 m2 de área construida', '1 cupo por cada 100 m2 y visitantes 1 por cada 400 m2'],
            ]],
        ];
        foreach ($rows as &$type) foreach ($type['data'] as $key => &$r) $r = [
            'label' => self::modalities()[$key], 'min_area_m2' => $r[0], 'min_front_m' => $r[1],
            'construction_index' => $r[2], 'free_area' => $r[3], 'parking' => $r[4], 'height' => $type['height'],
            'floor_level' => 'Lotes sin inclinación: 0.30 m de la rasante en el eje de la vía.',
        ];
        return $rows;
    }
}
