<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class MethodologyWorkflow
{
    public const METHODS = ['mercado' => 'Mercado', 'costo' => 'Costo', 'renta' => 'Renta', 'residual' => 'Residual'];
    public const PREFIXES = ['mercado' => 'M', 'costo' => 'C', 'renta' => 'R', 'residual' => 'Re'];
    public const STAGES = ['1' => 'Academia', '2' => 'Selección del método', '3' => 'Insumos', '4' => 'Análisis', '5' => 'Entregable'];

    public static function saved(array $record): array
    {
        return json_decode($record['methodology_workflow'] ?? '{}', true, 16, JSON_THROW_ON_ERROR) ?: [];
    }

    public static function components(array $record, array $units): array
    {
        $out = [];
        foreach ($units as $unit) {
            if (($unit['unit_kind'] ?? '') === 'common') continue;
            $fallback = (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0);
            $out[(string) $unit['id']] = ['label' => trim($unit['label'] ?? '') ?: $fallback, 'unit' => $unit];
        }
        return $out;
    }

    public static function validateKey(string $key, array $components): void
    {
        if ($key !== '' && !isset($components[$key])) throw new HttpException(422, 'El componente ya no está activo. Revisa la composición del predio.');
    }

    public static function input(array $post): array
    {
        $out = [];
        foreach (['method' => 20, 'treatment' => 20, 'reason' => 2000, 'coverage' => 2000,
            'analysis' => 6000, 'conclusion' => 6000] as $field => $max) {
            if (!array_key_exists($field, $post)) continue;
            if (!is_string($post[$field]) || mb_strlen($post[$field]) > $max) throw new HttpException(422, "Revisa $field: máximo $max caracteres.");
            $out[$field] = trim($post[$field]);
        }
        if (isset($out['method']) && $out['method'] !== '' && !isset(self::METHODS[$out['method']])) throw new HttpException(422, 'Método inválido.');
        if (isset($out['treatment']) && !in_array($out['treatment'], ['', 'separado', 'integrado', 'descriptivo'], true)) throw new HttpException(422, 'Tratamiento inválido.');
        return $out;
    }

    public static function fingerprint(array $rows): string
    {
        foreach ($rows as &$row) unset($row['created_at'], $row['updated_at'], $row['sample_index']);
        unset($row);
        usort($rows, static fn ($a, $b) => strcmp($a['id'] ?? '', $b['id'] ?? ''));
        return hash('sha256', json_encode($rows, JSON_THROW_ON_ERROR));
    }
}
