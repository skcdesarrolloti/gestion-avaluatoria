<?php
declare(strict_types=1);
namespace App\Support;

final class IgacTypologyFilter
{
    public static function rules(): array
    {
        return [
            'parqueo' => ['aliases' => ['garaje', 'garage', 'parqueo', 'parqueadero', 'estacionamiento', 'celda de parqueo'],
                'fields' => ['label', 'description'],
                'terms' => ['garaje', 'garage', 'parqueo', 'parqueadero', 'estacionamiento']],
            'deposito' => ['aliases' => ['deposito', 'cuarto util', 'trastero'],
                'fields' => ['label'], 'terms' => ['deposito', 'cuarto util', 'trastero']],
        ];
    }

    public static function normalize(string $text): string
    {
        $text = strtr(mb_strtolower($text), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        return trim(preg_replace('/[^a-z0-9]+/', ' ', $text) ?? '');
    }

    public static function options(array $options, string $type, string $selected = ''): array
    {
        $rule = self::rules()[$type] ?? [];
        $terms = $rule['terms'] ?? [];
        if ($terms === []) return $options;
        return array_values(array_filter($options, static function (array $item) use ($rule, $terms, $selected): bool {
            if ($selected !== '' && $item['value'] === $selected) return true;
            $text = self::normalize(implode(' ', array_map(static fn ($field) => $item[$field] ?? '', $rule['fields'])));
            foreach ($terms as $term) if (str_contains($text, $term)) return true;
            return false;
        }));
    }
}
