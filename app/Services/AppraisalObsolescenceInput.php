<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalObsolescenceCatalog;

final class AppraisalObsolescenceInput
{
    public static function data(array $post): array
    {
        $rows = [];
        $posted = is_array($post['obsolescence']['factors'] ?? null) ? $post['obsolescence']['factors'] : [];
        foreach (AppraisalObsolescenceCatalog::groups() as $groupKey => [, , , $metaOptions, $factors]) {
            $group = is_array($posted[$groupKey] ?? null) ? $posted[$groupKey] : [];
            $meta = (string) ($group['meta'] ?? '');
            $rows[$groupKey] = ['meta' => array_key_exists($meta, $metaOptions) ? $meta : '', 'items' => []];
            foreach ($factors as $key => $_label) {
                $item = is_array($group['items'][$key] ?? null) ? $group['items'][$key] : [];
                $score = (string) ($item['score'] ?? '');
                if (!array_key_exists($score, AppraisalObsolescenceCatalog::scores())) $score = '';
                $rows[$groupKey]['items'][$key] = ['score' => $score,
                    'evidence' => mb_substr(trim((string) ($item['evidence'] ?? '')), 0, 1200)];
            }
        }
        return ['summary_text' => self::text($post['summary_text'] ?? '', 2200),
            'diagnosis_text' => self::text($post['diagnosis_text'] ?? '', 1800),
            'quantification_text' => self::text($post['quantification_text'] ?? '', 1800),
            'normative_text' => self::text($post['normative_text'] ?? '', 1800), 'factors' => $rows];
    }
    private static function text(mixed $value, int $limit): string { return mb_substr(trim((string) $value), 0, $limit); }
}
