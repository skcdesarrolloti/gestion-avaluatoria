<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class MethodologyComparableScope
{
    public static function rows(array $rows, string $key): array
    {
        return array_values(array_filter($rows, static fn ($row) => ($row['component_key'] ?? '') === $key));
    }

    public static function merge(array $existing, array $incoming, string $key): array
    {
        $other = array_values(array_filter($existing, static fn ($row) => ($row['component_key'] ?? '') !== $key));
        $protected = array_column($other, 'id');
        $ids = [];
        foreach ($incoming as &$row) {
            $id = (string) ($row['id'] ?? '');
            if ($id !== '' && (in_array($id, $protected, true) || isset($ids[$id]))) throw new HttpException(422, 'Una muestra está duplicada o pertenece a otro componente. No se guardaron cambios.');
            $ids[$id] = true;
            $row['component_key'] = $key;
        }
        unset($row);
        return [...$other, ...$incoming];
    }
}
