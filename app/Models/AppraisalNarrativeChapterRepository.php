<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class AppraisalNarrativeChapterRepository
{
    public function __construct(private PDO $db) {}

    public function profile(string $appraisalId, int $owner, string $chapter, array $defaults): array
    {
        $query = $this->db->prepare('SELECT data_json, updated_at FROM appraisal_narrative_chapters
            WHERE appraisal_id = ? AND owner_id = ? AND chapter_code = ?');
        $query->execute([$appraisalId, $owner, $chapter]);
        $row = $query->fetch();
        if (!$row) return $defaults + ['updated_at' => null];
        $data = json_decode((string) ($row['data_json'] ?? ''), true);
        return array_replace($defaults, is_array($data) ? $data : []) + ['updated_at' => $row['updated_at'] ?? null];
    }

    public function save(string $appraisalId, int $owner, string $chapter, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $exists = $this->exists($appraisalId, $owner, $chapter);
        if ($exists) {
            $this->db->prepare('UPDATE appraisal_narrative_chapters
                SET data_json = ?, updated_at = ? WHERE appraisal_id = ? AND owner_id = ? AND chapter_code = ?')
                ->execute([$json, $now, $appraisalId, $owner, $chapter]);
            return;
        }
        $this->db->prepare('INSERT INTO appraisal_narrative_chapters
            (appraisal_id, owner_id, chapter_code, data_json, updated_at) VALUES (?, ?, ?, ?, ?)')
            ->execute([$appraisalId, $owner, $chapter, $json, $now]);
    }

    private function exists(string $appraisalId, int $owner, string $chapter): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_narrative_chapters
            WHERE appraisal_id = ? AND owner_id = ? AND chapter_code = ?');
        $query->execute([$appraisalId, $owner, $chapter]);
        return (int) $query->fetchColumn() === 1;
    }
}
