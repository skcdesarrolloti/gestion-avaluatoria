<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class AppraisalReportNoteRepository
{
    public function __construct(private PDO $db) {}

    public function byAppraisal(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_report_notes
            WHERE appraisal_id = ? AND owner_id = ? AND include_in_report = 1
            ORDER BY chapter_code, section_code, sort_order, created_at, id');
        $query->execute([$appraisalId, $owner]);
        return $query->fetchAll();
    }

    public function byChapter(string $appraisalId, int $owner, string $chapter): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_report_notes
            WHERE appraisal_id = ? AND owner_id = ? AND chapter_code = ?
            ORDER BY section_code, sort_order, created_at, id');
        $query->execute([$appraisalId, $owner, $chapter]);
        return $query->fetchAll();
    }

    public function saveRows(string $appraisalId, int $owner, string $chapter, array $rows): void
    {
        foreach ($rows as $index => $row) {
            if (!is_array($row)) continue;
            $id = $this->id((string) ($row['id'] ?? ''));
            if ($id !== '' && (string) ($row['delete'] ?? '') === '1') { $this->delete($id, $appraisalId, $owner); continue; }
            $body = $this->body($row['body'] ?? '', 4000); $title = $this->limit($row['title'] ?? '', 180);
            $source = $this->limit($row['source_note'] ?? '', 600); $section = $this->section($row['section_code'] ?? '', $chapter);
            if ($body === '' && $title === '' && $source === '') continue;
            if ($body === '') continue;
            $order = max(0, min(999, (int) ($row['sort_order'] ?? $index)));
            $id === '' ? $this->insert($appraisalId, $owner, $chapter, $section, $title, $body, $source, $order)
                : $this->update($id, $appraisalId, $owner, $section, $title, $body, $source, $order);
        }
    }

    private function insert(string $appraisalId, int $owner, string $chapter, string $section, string $title, string $body, string $source, int $order): void
    {
        $now = gmdate('Y-m-d H:i:s'); $id = bin2hex(random_bytes(16));
        $query = $this->db->prepare('INSERT INTO appraisal_report_notes
            (id, appraisal_id, owner_id, chapter_code, section_code, title, body, source_note, sort_order, include_in_report, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)');
        $query->execute([$id, $appraisalId, $owner, $chapter, $section, $title, $body, $source, $order, $now, $now]);
    }

    private function update(string $id, string $appraisalId, int $owner, string $section, string $title, string $body, string $source, int $order): void
    {
        $query = $this->db->prepare('UPDATE appraisal_report_notes SET section_code = ?, title = ?, body = ?,
            source_note = ?, sort_order = ?, updated_at = ? WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$section, $title, $body, $source, $order, gmdate('Y-m-d H:i:s'), $id, $appraisalId, $owner]);
    }

    private function delete(string $id, string $appraisalId, int $owner): void
    { $this->db->prepare('DELETE FROM appraisal_report_notes WHERE id = ? AND appraisal_id = ? AND owner_id = ?')->execute([$id, $appraisalId, $owner]); }
    private function id(string $value): string { return preg_match('/^[a-f0-9]{32}$/', $value) ? $value : ''; }
    private function section(mixed $value, string $chapter): string
    { $text = $this->limit($value, 20); return preg_match('/^' . preg_quote($chapter, '/') . '(?:\.\d+){0,3}$/', $text) ? $text : $chapter; }
    private function limit(mixed $value, int $max): string
    { return mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''), 0, $max); }
    private function body(mixed $value, int $max): string
    {
        $lines = preg_split('/\R/u', (string) $value) ?: [];
        $text = trim(implode("\n", array_map(fn (string $line): string => $this->limit($line, 1000), $lines)));
        return mb_substr($text, 0, $max);
    }
}
