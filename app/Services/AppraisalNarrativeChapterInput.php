<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalNarrativeChapterInput
{
    public static function data(array $sections, array $posted): array
    {
        $out = [];
        foreach ($sections as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $key = (string) ($field['key'] ?? '');
                if ($key === '') continue;
                $out[$key] = self::value($posted[$key] ?? '', (int) ($field['max'] ?? 2200));
            }
        }
        return $out;
    }

    public static function defaults(array $sections): array
    {
        $out = [];
        foreach ($sections as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $key = (string) ($field['key'] ?? '');
                if ($key !== '') $out[$key] = '';
            }
        }
        return $out;
    }

    private static function value(mixed $value, int $max): string
    {
        $lines = preg_split('/\R/u', (string) $value) ?: [];
        $text = trim(implode("\n", array_map(
            static fn (string $line): string => mb_substr(trim($line), 0, 1000),
            $lines
        )));
        return mb_substr($text, 0, max(1, $max));
    }
}
