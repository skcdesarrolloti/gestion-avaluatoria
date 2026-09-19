<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateTextExtractor
{
    private const EXTERNAL_PDF_MAX_BYTES = 25165824;
    private const PDF_RAW_READ_BYTES = 25165824;

    public function extract(string $path, string $extension): string
    {
        $text = match ($extension) {
            'txt' => (string) @file_get_contents($path),
            'docx' => $this->docx($path),
            'pdf' => $this->pdf($path),
            'jpg', 'jpeg', 'png', 'webp', 'tif', 'tiff' => $this->image($path),
            default => '',
        };
        $text = $this->scrub($text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/', "\n\n", $text) ?? $text;
        return trim($text);
    }

    private function scrub(string $text): string
    {
        if ($text === '') return '';
        if (function_exists('mb_scrub')) return mb_scrub($text, 'UTF-8');
        $converted = @mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        return is_string($converted) ? $converted : $text;
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
        $external = $this->externalPdfText($path);
        if ($external !== '') return $external;
        $raw = @file_get_contents($path, false, null, 0, self::PDF_RAW_READ_BYTES);
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
        $ocr = (new OcrTextExtractor())->pdf($path);
        if ($ocr !== '') return $ocr;
        $fallback = preg_replace('/[^\PC\s]/u', ' ', $raw);
        return is_string($fallback) ? trim($fallback) : '';
    }

    private function image(string $path): string
    {
        return (new OcrTextExtractor())->image($path);
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
        $text = preg_replace_callback('/\\\\([0-7]{1,3})/', static fn (array $m): string => chr(octdec($m[1])), $text) ?? $text;
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

    private function externalPdfText(string $path): string
    {
        if (!function_exists('shell_exec') || !is_file($path)) return '';
        if ((int) filesize($path) > self::EXTERNAL_PDF_MAX_BYTES) return '';
        $locator = PHP_OS_FAMILY === 'Windows' ? 'where pdftotext 2>NUL' : 'command -v pdftotext 2>/dev/null';
        $binary = trim((string) @shell_exec($locator));
        if ($binary === '') return '';
        $binary = preg_split('/\R/', $binary)[0] ?? '';
        if ($binary === '') return '';
        $command = escapeshellarg($binary) . ' -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>&1';
        $text = (string) @shell_exec($command);
        $text = trim(preg_replace('/[ \t]+/', ' ', $text) ?? $text);
        if (mb_strlen($text) < 30) return '';
        return preg_match('/matr|anotaci|folio|certificado|referencia|departamento|municipio|propiedad horizontal|copropiedad|reglamento|coeficiente|unidades privadas/iu', $text) ? $text : '';
    }

}
