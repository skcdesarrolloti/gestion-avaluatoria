<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Http;

final class AppraisalSectorRedirect
{
    public static function afterSave(string $id, array $post): never
    {
        $target = (string) ($post['target_sector'] ?? '');
        if ($target === 'bien-sujeto' || (string) ($post['after_sector_save'] ?? '') === 'bien-sujeto') {
            Http::redirect('avaluos/' . $id . '/bien-sujeto');
        }

        $anchor = $target !== '' ? $target : (string) ($post['active_sector'] ?? '');
        $hash = preg_match('/^(?:[a-z_]+|banco-\d{2})$/', $anchor) ? '#' . $anchor : '';
        Http::redirect('avaluos/' . $id . '/sector' . $hash);
    }
}
