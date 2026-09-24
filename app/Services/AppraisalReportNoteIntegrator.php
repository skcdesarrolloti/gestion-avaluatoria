<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalReportNoteIntegrator
{
    public function apply(array $chapterReport, array $notes, array $sectionLabels = []): array
    {
        $sections = is_array($chapterReport['sections'] ?? null) ? $chapterReport['sections'] : [];
        foreach ($sections as &$section) {
            $code = $this->sectionCode((string) ($section[0] ?? ''));
            $extra = $this->forSection($notes, $code);
            if ($extra !== '') $section[1] = rtrim((string) ($section[1] ?? '')) . "\n" . $extra;
        }
        unset($section);
        foreach ($this->withoutSection($notes, array_map(fn (array $s): string => $this->sectionCode((string) ($s[0] ?? '')), $sections)) as $note) {
            $code = (string) $note['section_code'];
            $title = isset($sectionLabels[$code]) ? $code . ' ' . $sectionLabels[$code] : 'Ampliación ' . $code;
            $sections[] = [$title, $this->noteText($note)];
        }
        return ['sections' => $sections, 'text' => $this->plainText($sections)];
    }

    private function forSection(array $notes, string $code): string
    {
        $items = array_values(array_filter($notes, fn (array $n): bool => (string) ($n['section_code'] ?? '') === $code));
        return $items ? "\nAmpliaciones del analista:\n" . implode("\n", array_map([$this, 'noteText'], $items)) : '';
    }

    private function withoutSection(array $notes, array $codes): array
    { return array_values(array_filter($notes, fn (array $n): bool => !in_array((string) ($n['section_code'] ?? ''), $codes, true))); }
    private function noteText(array $note): string
    {
        $title = trim((string) ($note['title'] ?? '')); $body = trim((string) ($note['body'] ?? ''));
        $source = trim((string) ($note['source_note'] ?? ''));
        return '- ' . ($title !== '' ? $title . ': ' : '') . $body . ($source !== '' ? ' Fuente/soporte: ' . $source . '.' : '');
    }
    private function sectionCode(string $title): string { return preg_match('/^(\d+(?:\.\d+){0,3})\b/u', trim($title), $m) ? $m[1] : ''; }
    private function plainText(array $sections): string { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
}
