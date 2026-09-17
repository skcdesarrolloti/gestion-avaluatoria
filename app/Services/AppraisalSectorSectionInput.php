<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalSectorAdvancedCatalog;

final class AppraisalSectorSectionInput
{
    public static function data(array $input): array
    {
        $raw = is_array($input['sector_sections'] ?? null) ? $input['sector_sections'] : [];
        $clean = [];
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [, $fields]) {
            $code = (string) $code;
            $section = is_array($raw[$code] ?? null) ? $raw[$code] : [];
            foreach ($fields as $field) {
                [$name,, $type] = $field;
                $options = isset($field[3]) ? AppraisalSectorAdvancedCatalog::options((string) $field[3]) : [];
                $clean[$code][$name] = self::value($section[$name] ?? null, (string) $type, $options);
            }
        }
        return $clean;
    }

    private static function value(mixed $value, string $type, array $options): mixed
    {
        if ($type === 'multiselect') {
            $items = is_array($value) ? $value : [];
            $allowed = array_keys($options);
            return array_values(array_intersect(array_map('strval', $items), $allowed));
        }
        $text = trim(is_scalar($value) ? (string) $value : '');
        if ($type === 'select') {
            return array_key_exists($text, $options) ? $text : '';
        }
        return mb_substr($text, 0, $type === 'textarea' ? 4000 : 240);
    }
}
