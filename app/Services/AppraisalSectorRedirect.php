<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Http;

final class AppraisalSectorRedirect
{
    public static function afterSave(string $id, array $post): never
    {
        $targetValue = (string) ($post['target_sector'] ?? '');
        if ($targetValue === 'bien-sujeto' || (string) ($post['after_sector_save'] ?? '') === 'bien-sujeto') {
            Http::redirect('avaluos/' . $id . '/bien-sujeto');
        }

        $target = self::sectorAnchor($targetValue);
        $active = self::sectorAnchor((string) ($post['active_sector'] ?? ''));
        $hash = $target !== '' ? '#' . $target : ($active !== '' ? '#' . $active : '');
        Http::redirect('avaluos/' . $id . '/sector' . $hash);
    }

    private static function sectorAnchor(string $value): string
    {
        if (preg_match('/^banco-(\d{1,2})$/', $value, $match)) {
            return 'banco-' . str_pad($match[1], 2, '0', STR_PAD_LEFT);
        }
        return '';
    }
}
