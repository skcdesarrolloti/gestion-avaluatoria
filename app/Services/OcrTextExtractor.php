<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class OcrTextExtractor
{
    private const IMAGE_MAX_BYTES = 8388608;
    private const PDF_MAX_BYTES = 52428800;
    private const PDF_MAX_PAGES = 6;

    public function diagnostics(): array
    {
        $shell = function_exists('shell_exec');
        $exec = function_exists('exec');
        $tesseract = $this->command('TESSERACT_BINARY', 'tesseract') !== '';
        $pdftoppm = $this->command('PDFTOPPM_BINARY', 'pdftoppm') !== '';
        return ['shell_exec' => $shell, 'exec' => $exec, 'tesseract' => $tesseract,
            'pdftoppm' => $pdftoppm, 'pdf_ocr' => $shell && $exec && $tesseract && $pdftoppm];
    }

    public function image(string $path): string
    {
        if ((int) @filesize($path) > self::IMAGE_MAX_BYTES) return '';
        return $this->ocrImage($path);
    }

    public function pdf(string $path): string
    {
        if (!function_exists('exec') || !is_file($path) || (int) filesize($path) > self::PDF_MAX_BYTES) return '';
        if ($this->command('TESSERACT_BINARY', 'tesseract') === '') return '';
        $renderer = $this->command('PDFTOPPM_BINARY', 'pdftoppm');
        if ($renderer === '') return '';
        $dir = sys_get_temp_dir() . '/ga_pdf_ocr_' . bin2hex(random_bytes(4));
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) return '';
        try {
            $prefix = $dir . '/page';
            $cmd = $renderer . ' -f 1 -l ' . self::PDF_MAX_PAGES . ' -r 160 -png '
                . escapeshellarg($path) . ' ' . escapeshellarg($prefix);
            $output = []; $code = 1; exec($cmd . ' 2>&1', $output, $code);
            if ($code !== 0) return '';
            $parts = [];
            foreach (glob($prefix . '-*.png') ?: [] as $image) {
                $text = $this->ocrImage($image, false);
                if ($text !== '') $parts[] = $text;
            }
            return trim(implode("\n\n", $parts));
        } finally {
            foreach (glob($dir . '/*') ?: [] as $file) @unlink($file);
            @rmdir($dir);
        }
    }

    private function ocrImage(string $path, bool $checkSize = true): string
    {
        if (!function_exists('shell_exec') || !is_file($path)) return '';
        if ($checkSize && (int) filesize($path) > self::IMAGE_MAX_BYTES) return '';
        $binary = $this->command('TESSERACT_BINARY', 'tesseract');
        if ($binary === '') return '';
        foreach (['spa+eng', 'spa', 'eng', ''] as $language) {
            $command = $binary . ' ' . escapeshellarg($path) . ' stdout'
                . ($language !== '' ? ' -l ' . escapeshellarg($language) : '')
                . ' --psm 6 2>&1';
            $text = trim(preg_replace('/[ \t]+/', ' ', (string) @shell_exec($command)) ?? '');
            if (mb_strlen($text) < 30 || preg_match('/error opening data file|failed loading language|could not initialize/iu', $text)) {
                continue;
            }
            if ($this->looksRelevant($text)) return $text;
        }
        return '';
    }

    private function command(string $envKey, string $name): string
    {
        $configured = trim(Env::get($envKey));
        if ($configured !== '') return escapeshellarg($configured);
        if (!function_exists('shell_exec')) return '';
        $locator = PHP_OS_FAMILY === 'Windows' ? 'where ' . $name . ' 2>NUL' : 'command -v ' . $name . ' 2>/dev/null';
        $binary = preg_split('/\R/', trim((string) @shell_exec($locator)))[0] ?? '';
        return $binary !== '' ? escapeshellarg($binary) : '';
    }

    private function looksRelevant(string $text): bool
    {
        return (bool) preg_match('/matr|anotaci|folio|certificado|registro|supernotariado|departamento|municipio|propiedad horizontal|copropiedad|reglamento|coeficiente|unidades privadas/iu', $text);
    }
}
