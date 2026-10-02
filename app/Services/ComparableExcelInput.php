<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ComparableExcelInput
{
    public static function read(string $path, string $appraisalId, string $scope, int $version, array $existing): array
    {
        $archive = new ComparableExcelArchive($path); $sheets = $archive->sheets();
        if (!isset($sheets['_SuCasa'])) throw new HttpException(422, 'Este Excel no tiene identificación de muestras. Exporta nuevamente desde el módulo y conserva los encabezados e ID.');
        $metaRows = $archive->rows($sheets['_SuCasa']);
        try { $meta = json_decode($metaRows[0][0]['value'] ?? '', true, 12, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new HttpException(422, 'Identificación del Excel inválida. Exporta nuevamente.'); }
        if (($meta['format'] ?? '') !== 'sucasa-comparables-2' || ($meta['appraisal'] ?? '') !== $appraisalId
            || ($meta['scope'] ?? null) !== $scope) throw new HttpException(422, 'El Excel pertenece a otro expediente o unidad/banco.');
        if (($meta['version'] ?? -1) !== $version) throw new HttpException(409, 'La matriz cambió desde que exportaste. Exporta la versión actual y aplica allí tus ajustes.');
        if (!isset($sheets[$meta['sheet'] ?? ''])) throw new HttpException(422, 'No se encuentra la hoja de comparables exportada.');
        return self::rows($archive->rows($sheets[$meta['sheet']]), $meta['columns'] ?? [], $existing);
    }
    public static function rows(array $sheet, array $columns, array $existing): array
    {
        $headers = array_shift($sheet) ?? []; $map = []; $labels = [];
        foreach ($columns as $column) {
            if (!is_array($column) || !is_string($column['key'] ?? null) || !is_string($column['label'] ?? null)) throw new HttpException(422, 'Columnas Excel inválidas.');
            $labels[$column['label']] = $column;
        }
        foreach ($headers as $index => $header) {
            $label = trim($header['value']);
            if (!isset($labels[$label])) throw new HttpException(422, 'Conserva los encabezados originales del Excel.');
            $column = $labels[$label];
            if (in_array($column['key'], array_column($map, 'key'), true)) throw new HttpException(422, 'Excel tiene columnas duplicadas.');
            $map[$index] = $column;
        }
        if (!in_array('id', array_column($map, 'key'), true)) throw new HttpException(422, 'Conserva la columna ID de muestra.');
        $known = array_column($existing, null, 'id'); $seen = []; $incoming = [];
        foreach ($sheet as $cells) {
            $row = [];
            foreach ($map as $index => $column) {
                $key = $column['key'];
                if (in_array($key, ['capture_pending','negotiated_amount','negotiation_percent','component_key'], true)) continue;
                $cell = $cells[$index] ?? ['value'=>'', 'formula'=>false, 'type'=>'inlineStr'];
                if ($cell['formula']) throw new HttpException(422, 'Sólo se admiten fórmulas en las columnas calculadas de negociación. Pega valores en los campos editables.');
                $value = trim($cell['value']);
                if (isset($column['options']) && $value !== '') {
                    $option = array_search($value, $column['options'], true);
                    if ($option !== false) $value = (string) $option;
                }
                if (in_array($key, ['consulted_at','listing_date'], true) && $value !== '' && is_numeric($value))
                    $value = gmdate('Y-m-d', strtotime('1899-12-30 UTC') + (int) ((float) $value * 86400));
                $row[$key] = $value;
            }
            if (implode('', $row) === '') continue;
            $id = $row['id'] ?? '';
            if (!isset($known[$id]) || isset($seen[$id])) throw new HttpException(422, 'ID de muestra ausente, repetido o ajeno a esta unidad/banco. No se importó nada.');
            $seen[$id] = true;
            $candidate = $row + $known[$id];
            ComparableCaptureDetail::normalize($candidate);
            // Whitelist base/detail fields; do not erase columns omitted from the export.
            $allowed = AppraisalComparableInput::rows(['comparables'=>[$candidate]])[0] ?? null;
            if (!$allowed) throw new HttpException(422, 'No vacíes todos los datos de una muestra desde Excel.');
            $incoming[] = array_intersect_key($allowed, $row);
        }
        if ($incoming === []) throw new HttpException(422, 'El Excel no contiene muestras para actualizar.');
        return $incoming;
    }
}
