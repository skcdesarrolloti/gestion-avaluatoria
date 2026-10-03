<?php
declare(strict_types=1);
namespace App\Services;

final class ComparableIntake
{
    public static function groups(array $rows, bool $selectedOnly = false): array
    {
        $groups = [];
        foreach ($rows as $index => $row) {
            $key = ($row['property_group'] ?? '') ?: ($row['id'] ?? 'legacy-' . $index);
            $groups[$key][] = $row + ['capture_index'=>$index];
        }
        if (!$selectedOnly) return $groups;
        return array_filter($groups, static function (array $group): bool {
            foreach ($group as $row) {
                $state = $row['intake_state'] ?? '';
                if (!in_array($state, ['selected','selected_pending'], true)
                    && !($state === '' && ($row['status'] ?? '') === 'usada')) return false;
            }
            return true;
        });
    }
}
