<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
use App\Support\AppraisalSpecialAttributeCatalog;

final class AppraisalAttributeInput
{
    public static function unitAttributeData(): array
    {
        $posted = $_POST['unit_attributes'] ?? [];
        if (!is_array($posted)) throw new HttpException(422, 'No se recibieron atributos válidos.');
        $allowed = array_flip(AppraisalSpecialAttributeCatalog::flatKeys());
        $rows = [];
        foreach ($posted as $id => $unit) {
            if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/', $id) || !is_array($unit)) continue;
            $rows[] = ['id' => $id, 'special_attributes_json' => self::attributesJson($unit['items'] ?? [], $allowed),
                'special_attributes_report_text' => mb_substr(trim((string) ($unit['report_text'] ?? '')), 0, 1500)];
        }
        return $rows;
    }

    private static function attributesJson(mixed $items, array $allowed): string
    {
        if (!is_array($items)) return '{}';
        $clean = [];
        foreach ($items as $key => $item) {
            if (!is_string($key) || !isset($allowed[$key]) || !is_array($item)) continue;
            $row = self::row($item);
            if (implode('', $row) !== '') $clean[$key] = $row;
        }
        return json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private static function row(array $item): array
    {
        return [
            'value' => self::short($item['value'] ?? '', 80),
            'state' => self::short($item['state'] ?? '', 40),
            'impact' => self::short($item['impact'] ?? '', 40),
            'evidence' => self::short($item['evidence'] ?? '', 40),
            'rating' => self::allowed($item['rating'] ?? '', ['', '1', '2', '3', '4', '5']),
            'weight' => self::allowed($item['weight'] ?? '', ['', '1', '2', '3']),
            'use_in_comparables' => self::allowed($item['use_in_comparables'] ?? '', ['', 'si', 'no']),
            'notes' => self::short($item['notes'] ?? '', 220),
        ];
    }

    private static function short(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }

    private static function allowed(mixed $value, array $allowed): string
    {
        $value = trim((string) $value);
        return in_array($value, $allowed, true) ? $value : '';
    }
}
