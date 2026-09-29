<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMidasMapSupport
{
    public function select(array $documents, array $subject, array $sector): array
    {
        $locality = $this->first($subject['locality_name'] ?? '', $sector['sector_locality'] ?? '');
        $localityDocs = array_values(array_filter($documents, fn (array $doc): bool =>
            (string) ($doc['layer_group'] ?? '') === 'Localidades'));
        return [
            'locality_label' => $locality,
            'general' => $this->general($localityDocs),
            'specific' => $this->specific($localityDocs, $locality),
        ];
    }

    private function general(array $docs): ?array
    {
        foreach ($docs as $doc) {
            $hay = $this->haystack($doc);
            if ($this->isGeneralMap($hay)) return $doc;
        }
        return null;
    }

    private function specific(array $docs, string $locality): array
    {
        $terms = $this->terms($locality);
        if ($terms === []) return [];
        $scored = [];
        foreach ($docs as $doc) {
            $hay = $this->haystack($doc);
            if ($this->isGeneralMap($hay)) continue;
            $score = 0;
            foreach ($terms as $term) if (str_contains($hay, $term)) $score++;
            if ($score > 0) $scored[] = ['score' => $score, 'doc' => $doc];
        }
        usort($scored, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        return array_slice(array_column($scored, 'doc'), 0, 2);
    }

    private function terms(string $locality): array
    {
        $text = $this->fold($locality);
        if ($text === '') return [];
        if (str_contains($text, 'historica')) return ['historica'];
        if (str_contains($text, 'virgen')) return ['virgen'];
        if (str_contains($text, 'industrial') || str_contains($text, 'bahia')) return ['industrial', 'bahia'];
        $tokens = preg_split('/[^a-z0-9]+/', $text) ?: [];
        return array_values(array_filter($tokens, static fn (string $token): bool =>
            strlen($token) > 3 && !in_array($token, ['localidad', 'norte', 'rural', 'urbana'], true)));
    }

    private function haystack(array $doc): string
    {
        return $this->fold(implode(' ', [
            $doc['document_code'] ?? '',
            $doc['title'] ?? '',
            $doc['source_filename'] ?? '',
        ]));
    }

    private function isGeneralMap(string $haystack): bool
    {
        return str_contains($haystack, 'localidades_localidades')
            || preg_match('/(^|_)localidades(_|$)/', $haystack) === 1
                && !preg_match('/localidad_(historica|virgen|industrial)/', $haystack);
    }

    private function first(mixed ...$values): string
    {
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text !== '') return $text;
        }
        return '';
    }

    private function fold(string $text): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($text)) ?: mb_strtolower($text);
        return str_replace(['-', ' '], '_', preg_replace('/[^a-z0-9]+/', '_', $ascii) ?? '');
    }
}
