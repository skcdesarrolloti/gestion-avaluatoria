<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
use App\Support\AppraisalCatalog;

final class AppraisalChapterZeroInput
{
    public static function chapterZeroData(int $version, array $igacCodes, array $appraiserIds): array
    {
        if ($version < 1) throw new HttpException(422, 'La versión del borrador no es válida.');
        $input = ['version' => $version];
        foreach (AppraisalCatalog::fieldKeys() as $field) $input[$field] = (string) ($_POST[$field] ?? '');
        $data = AppraisalValidator::validate($input);
        $extra = [];
        foreach (['appraiser_id', 'igac_category', 'igac_typology_hint', 'inspection_notes'] as $field) {
            $extra[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $extra['igac_property_units_count'] = self::boundedCount('igac_property_units_count');
        $extra['igac_annex_units_count'] = self::boundedCount('igac_annex_units_count');
        $extra['configuration_status'] = 'borrador';
        if ($extra['appraiser_id'] !== '' && !in_array($extra['appraiser_id'], $appraiserIds, true)) {
            throw new HttpException(422, 'Selecciona un perito válido.');
        }
        self::assertIgacCategory($extra['igac_category'], $igacCodes);
        $extra['igac_typology_hint'] = mb_substr($extra['igac_typology_hint'], 0, 190);
        $extra['inspection_notes'] = mb_substr($extra['inspection_notes'], 0, 2000);
        return $data + $extra;
    }

    public static function preclassificationData(array $igacCodes): array
    {
        $category = trim((string) ($_POST['igac_category'] ?? ''));
        self::assertIgacCategory($category, $igacCodes);
        return [
            'igac_category' => $category,
            'igac_typology_hint' => mb_substr(trim((string) ($_POST['igac_typology_hint'] ?? '')), 0, 190),
            'igac_property_units_count' => self::boundedCount('igac_property_units_count'),
            'igac_annex_units_count' => self::boundedCount('igac_annex_units_count'),
        ];
    }

    public static function unitData(array $igacCodes, array $typologiesByCategory): array
    {
        $posted = $_POST['units'] ?? [];
        if (!is_array($posted)) throw new HttpException(422, 'No se recibieron unidades válidas.');
        $rows = [];
        foreach ($posted as $id => $unit) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/', $id) || !is_array($unit)) continue;
            $category = trim((string) ($unit['igac_category'] ?? ''));
            self::assertIgacCategory($category, $igacCodes, 'Selecciona categorías IGAC válidas por unidad.');
            $hint = mb_substr(trim((string) ($unit['igac_typology_hint'] ?? '')), 0, 190);
            self::assertIgacTypology($category, $hint, $typologiesByCategory);
            $rows[] = [
                'id' => $id,
                'label' => mb_substr(trim((string) ($unit['label'] ?? '')), 0, 120),
                'igac_category' => $category,
                'igac_typology_hint' => $hint,
                'notes' => mb_substr(trim((string) ($unit['notes'] ?? '')), 0, 2000),
            ];
        }
        return $rows;
    }

    public static function unitSurfaceData(): array
    {
        $posted = $_POST['unit_surfaces'] ?? [];
        if (!is_array($posted)) throw new HttpException(422, 'No se recibieron superficies válidas.');
        $rows = [];
        foreach ($posted as $id => $unit) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/', $id) || !is_array($unit)) continue;
            $rows[] = [
                'id' => $id,
                'area_land_m2' => self::decimalOrNull($unit['area_land_m2'] ?? null),
                'area_built_m2' => self::decimalOrNull($unit['area_built_m2'] ?? null),
                'area_private_m2' => self::decimalOrNull($unit['area_private_m2'] ?? null),
                'area_common_m2' => self::decimalOrNull($unit['area_common_m2'] ?? null),
                'front_length_m' => self::decimalOrNull($unit['front_length_m'] ?? null),
                'depth_length_m' => self::decimalOrNull($unit['depth_length_m'] ?? null),
                'surface_source' => mb_substr(trim((string) ($unit['surface_source'] ?? '')), 0, 120),
                'surface_notes' => mb_substr(trim((string) ($unit['surface_notes'] ?? '')), 0, 1000),
            ];
        }
        return $rows;
    }

    private static function assertIgacCategory(string $category, array $codes, string $message = 'Selecciona una categoría IGAC válida.'): void
    {
        if ($category !== '' && !in_array($category, $codes, true)) throw new HttpException(422, $message);
    }

    private static function assertIgacTypology(string $category, string $hint, array $options): void
    {
        if ($hint === '') return;
        $valid = array_column($options[$category] ?? [], 'value');
        if ($category === '' || !in_array($hint, $valid, true)) {
            throw new HttpException(422, 'La tipología preliminar no pertenece a la categoría IGAC seleccionada.');
        }
    }

    private static function boundedCount(string $field): int
    {
        $value = filter_var($_POST[$field] ?? 0, FILTER_VALIDATE_INT);
        if ($value === false || $value < 0 || $value > 50) {
            throw new HttpException(422, 'Los conteos de unidades deben estar entre 0 y 50.');
        }
        return $value;
    }

    private static function decimalOrNull(mixed $value): ?string
    {
        $normalized = str_replace(',', '.', trim((string) $value));
        if ($normalized === '') return null;
        if (!preg_match('/^\d{1,9}(\.\d{1,2})?$/', $normalized)) {
            throw new HttpException(422, 'Las superficies y medidas deben ser números positivos con máximo dos decimales.');
        }
        return number_format((float) $normalized, 2, '.', '');
    }
}
