<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateTextExtractor
{
    public function extract(string $path, string $extension): string
    {
        $text = match ($extension) {
            'txt' => (string) @file_get_contents($path),
            'docx' => $this->docx($path),
            'pdf' => $this->pdf($path),
            default => '',
        };
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/', "\n\n", $text) ?? $text;
        return trim($text);
    }

    private function docx(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) return '';
        $zip = new \ZipArchive();
        if (@$zip->open($path) !== true) return '';
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if (!is_string($xml) || $xml === '') return '';
        $xml = preg_replace('/<\/w:p>|<\/w:tr>/i', "\n", $xml) ?? $xml;
        $xml = preg_replace('/<w:(tab|br)[^>]*\/>/i', "\n", $xml) ?? $xml;
        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function pdf(string $path): string
    {
        $raw = @file_get_contents($path, false, null, 0, 8 * 1024 * 1024);
        if (!is_string($raw) || $raw === '') return '';
        $parts = [];
        foreach ($this->pdfStreams($raw) as $chunk) {
            if (preg_match_all('/\((.*?)\)\s*Tj/s', $chunk, $matches)) {
                foreach ($matches[1] as $item) $parts[] = $this->decodePdfString((string) $item);
            }
            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $chunk, $arrays)) {
                foreach ($arrays[1] as $arrayChunk) {
                    preg_match_all('/\((.*?)\)/s', (string) $arrayChunk, $strings);
                    foreach ($strings[1] as $item) $parts[] = $this->decodePdfString((string) $item);
                    preg_match_all('/<([0-9A-Fa-f]+)>/s', (string) $arrayChunk, $hexes);
                    foreach ($hexes[1] as $hex) $parts[] = $this->decodePdfHex((string) $hex);
                }
            }
        }
        $text = trim(implode("\n", array_filter($parts, static fn (string $v): bool => trim($v) !== '')));
        if ($text !== '') return $text;
        $fallback = preg_replace('/[^\PC\s]/u', ' ', $raw);
        return is_string($fallback) ? trim($fallback) : '';
    }

    private function pdfStreams(string $raw): array
    {
        $blocks = [];
        $offset = 0;
        while (($start = strpos($raw, 'stream', $offset)) !== false && count($blocks) < 300) {
            $dataStart = $start + 6;
            while (isset($raw[$dataStart]) && ($raw[$dataStart] === "\r" || $raw[$dataStart] === "\n")) $dataStart++;
            $end = strpos($raw, 'endstream', $dataStart);
            if ($end === false) break;
            $stream = substr($raw, $dataStart, $end - $dataStart);
            $blocks[] = $this->inflate($stream) ?: $stream;
            $offset = $end + 9;
        }
        return $blocks ?: [$raw];
    }

    private function inflate(string $stream): string
    {
        foreach ([$stream, trim($stream), ltrim($stream, "\r\n"), rtrim($stream, "\r\n")] as $candidate) {
            $decoded = @gzuncompress($candidate);
            if (is_string($decoded) && $decoded !== '') return $decoded;
            $decoded = @gzdecode($candidate);
            if (is_string($decoded) && $decoded !== '') return $decoded;
        }
        return '';
    }

    private function decodePdfString(string $text): string
    {
        $text = preg_replace('/\\\\([nrtbf()\\\\])/', ' ', $text) ?? $text;
        return trim(str_replace(['\(', '\)'], ['(', ')'], $text));
    }

    private function decodePdfHex(string $hex): string
    {
        $bin = @hex2bin(strlen($hex) % 2 === 0 ? $hex : $hex . '0');
        if (!is_string($bin)) return '';
        $utf16 = @mb_convert_encoding($bin, 'UTF-8', 'UTF-16BE');
        return trim(is_string($utf16) && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/u', $utf16) ? $utf16 : $bin);
    }
}
