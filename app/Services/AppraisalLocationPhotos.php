<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalRepository;

final class AppraisalLocationPhotos
{
    public static function items(AppraisalRepository $repo, string $id, int $owner): array
    {
        $photos = array_filter($repo->photos($id, $owner), static fn (array $photo): bool => $photo['caption'] === 'location:chapter-one');
        return array_values(array_map(static fn (array $photo): array => [
            'id' => $photo['id'], 'name' => $photo['display_name'] ?: $photo['source_filename'],
            'url' => url('avaluos/' . $id . '/fotos/' . $photo['id']),
        ], $photos));
    }
}
