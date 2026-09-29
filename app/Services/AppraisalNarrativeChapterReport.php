<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalNarrativeChapterReport
{
    public function build(array $sections, array $data): array
    {
        $rows = [];
        foreach ($sections as $section) {
            $code = (string) ($section['code'] ?? '');
            $title = trim($code . ' ' . (string) ($section['title'] ?? ''));
            $text = $this->sectionText($section, $data);
            $rows[] = [$title, $text !== '' ? $text : 'Pendiente de completar por el analista.'];
        }
        return ['sections' => $rows, 'text' => $this->plainText($rows)];
    }

    private function sectionText(array $section, array $data): string
    {
        $parts = [];
        foreach ($section['fields'] ?? [] as $field) {
            if (($field['report'] ?? true) === false) continue;
            $value = trim((string) ($data[(string) ($field['key'] ?? '')] ?? ''));
            if ($value === '') continue;
            $label = trim((string) ($field['label'] ?? ''));
            $parts[] = (($field['type'] ?? '') === 'select' && $label !== '')
                ? $label . ': ' . $this->optionLabel($field, $value) . '.'
                : $this->end($value);
        }
        return implode(' ', $parts);
    }

    private function optionLabel(array $field, string $value): string
    {
        return (string) (($field['options'] ?? [])[$value] ?? str_replace('_', ' ', $value));
    }

    private function end(string $text): string { return rtrim($text, " \t\n\r\0\x0B.") . '.'; }
    private function plainText(array $sections): string
    { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
}
