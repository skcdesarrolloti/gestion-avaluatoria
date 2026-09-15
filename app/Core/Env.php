<?php
declare(strict_types=1);
namespace App\Core;

final class Env
{
    private static array $values = [];

    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!preg_match('/^([A-Z][A-Z0-9_]*)=(.*)$/', $line, $parts)) {
                throw new \RuntimeException('Formato .env invalido.');
            }
            $value = trim($parts[2]);
            if (strlen($value) >= 2 && in_array($value[0], ['"', "'"], true) && $value[0] === substr($value, -1)) {
                $value = substr($value, 1, -1);
            }
            self::$values[$parts[1]] = $value;
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        $value = getenv($key);
        return $value !== false ? $value : (self::$values[$key] ?? $default);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return filter_var(self::get($key, $default ? 'true' : 'false'), FILTER_VALIDATE_BOOL);
    }
}
