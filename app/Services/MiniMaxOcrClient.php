<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class MiniMaxOcrClient
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const DEFAULT_ENDPOINT = 'https://api.minimax.io/v1/chat/completions';
    public function diagnostics(): array
    {
        return [
            'configured' => $this->apiKey() !== '',
            'curl' => function_exists('curl_init'),
            'pdf_render' => $this->pdfRenderer() !== '',
        ];
    }
    public function extract(string $path, string $filename, string $mime): string
    {
        if ($this->apiKey() === '') {
            throw new \RuntimeException('Configura MINIMAX_API_KEY para usar MiniMax IA/OCR.');
        }
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('MiniMax IA/OCR requiere la extensión cURL de PHP.');
        }
        if (!is_file($path)) throw new \RuntimeException('No se encontró el archivo PH para MiniMax IA/OCR.');
        $parts = $this->imageParts($path, $filename, $mime);
        if (!$parts) throw new \RuntimeException('MiniMax IA/OCR no encontró imágenes para leer en este soporte.');
        $texts = [];
        foreach ($parts as $part) $texts[] = $this->readImage($part['path'], $part['name'], $part['mime']);
        return $this->normalizeText(implode("\n\n", array_filter($texts)));
    }
    private function imageParts(string $path, string $filename, string $mime): array
    {
        $extension = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($this->isSupportedImage($extension, $mime)) {
            return [['path' => $path, 'name' => $filename, 'mime' => $this->imageMime($extension, $mime), 'temporary' => false]];
        }
        if ($extension === 'pdf' || $mime === 'application/pdf') return $this->pdfImages($path);
        if ($extension === 'zip' || str_contains($mime, 'zip')) return $this->zipImages($path);
        throw new \RuntimeException('MiniMax IA/OCR acepta imágenes JPG, PNG, WEBP o GIF; para PDF se requiere convertir páginas a imagen.');
    }
    private function zipImages(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) throw new \RuntimeException('No se puede abrir ZIP porque ZipArchive no está disponible.');
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) throw new \RuntimeException('El soporte ZIP no se pudo abrir para MiniMax IA/OCR.');
        $images = []; $max = $this->maxImages();
        $dir = sys_get_temp_dir() . '/ga_minimax_zip_' . bin2hex(random_bytes(4));
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) throw new \RuntimeException('No se pudo preparar lectura ZIP para MiniMax.');
        try {
            for ($i = 0; $i < $zip->numFiles && count($images) < $max; $i++) {
                $entry = $zip->getNameIndex($i);
                if (!is_string($entry) || str_ends_with($entry, '/')) continue;
                $extension = mb_strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                if (!in_array($extension, self::IMAGE_EXTENSIONS, true)) continue;
                $stream = $zip->getStream($entry);
                if (!$stream) continue;
                $target = $dir . '/' . bin2hex(random_bytes(5)) . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
                $out = fopen($target, 'wb');
                if (!$out) { fclose($stream); continue; }
                stream_copy_to_stream($stream, $out, $this->imageMaxBytes() + 1);
                fclose($out); fclose($stream);
                if ((int) @filesize($target) <= $this->imageMaxBytes()) {
                    $images[] = ['path' => $target, 'name' => basename($entry),
                        'mime' => $this->imageMime($extension, ''), 'temporary' => true];
                }
            }
        } finally {
            $zip->close();
        }
        if (!$images) {
            $this->clearTemporaryDir($dir);
            throw new \RuntimeException('El ZIP no contiene imágenes JPG, PNG, WEBP o GIF legibles por MiniMax. Si contiene PDFs escaneados, súbelos como PDF o habilita PDFTOPPM_BINARY.');
        }
        return $images;
    }
    private function pdfImages(string $path): array
    {
        if (!function_exists('exec')) {
            throw new \RuntimeException('Para enviar PDF escaneado a MiniMax se requiere habilitar exec y PDFTOPPM_BINARY para convertir páginas a imagen.');
        }
        $renderer = $this->pdfRenderer();
        if ($renderer === '') {
            throw new \RuntimeException('Para enviar PDF escaneado a MiniMax configura PDFTOPPM_BINARY o instala pdftoppm en el servidor.');
        }
        $dir = sys_get_temp_dir() . '/ga_minimax_pdf_' . bin2hex(random_bytes(4));
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) throw new \RuntimeException('No se pudo preparar lectura PDF para MiniMax.');
        $prefix = $dir . '/page';
        $cmd = $renderer . ' -f 1 -l ' . $this->maxImages() . ' -r 180 -png '
            . escapeshellarg($path) . ' ' . escapeshellarg($prefix);
        $output = []; $code = 1; exec($cmd . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $this->clearTemporaryDir($dir);
            throw new \RuntimeException('No se pudo convertir el PDF a imágenes para MiniMax.');
        }
        $images = [];
        foreach (glob($prefix . '-*.png') ?: [] as $image) {
            if ((int) @filesize($image) <= $this->imageMaxBytes()) {
                $images[] = ['path' => $image, 'name' => basename($image), 'mime' => 'image/png', 'temporary' => true];
            }
        }
        if (!$images) {
            $this->clearTemporaryDir($dir);
            throw new \RuntimeException('No se generaron imágenes del PDF para MiniMax.');
        }
        return $images;
    }
    private function readImage(string $path, string $name, string $mime): string
    {
        try {
            $data = file_get_contents($path);
            if (!is_string($data) || $data === '') throw new \RuntimeException('No se pudo leer la imagen para MiniMax.');
            if (strlen($data) > $this->imageMaxBytes()) throw new \RuntimeException($name . ' supera el límite de imagen para MiniMax.');
            $payload = [
                'model' => Env::get('MINIMAX_MODEL', 'MiniMax-M3'),
                'thinking' => ['type' => 'disabled'],
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $this->prompt($name)],
                        ['type' => 'image_url', 'image_url' => [
                            'url' => 'data:' . $mime . ';base64,' . base64_encode($data),
                            'detail' => Env::get('MINIMAX_IMAGE_DETAIL', 'high'),
                            'max_long_side_pixel' => max(336, min(2016, (int) Env::get('MINIMAX_MAX_LONG_SIDE_PIXEL', '1600'))),
                        ]],
                    ],
                ]],
                'max_completion_tokens' => max(256, min(16000, (int) Env::get('MINIMAX_MAX_COMPLETION_TOKENS', '6000'))),
                'temperature' => 0,
            ];
            return $this->content($this->request($payload));
        } finally {
            if (str_starts_with($path, sys_get_temp_dir() . '/ga_minimax_')) {
                @unlink($path);
                @rmdir(dirname($path));
            }
        }
    }
    private function request(array $payload): array
    {
        $curl = curl_init($this->endpoint());
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->apiKey(), 'Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_TIMEOUT => max(20, min(300, (int) Env::get('MINIMAX_TIMEOUT_SECONDS', '120'))),
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if (!is_string($body) || $body === '' || $status < 200 || $status >= 300) {
            throw new \RuntimeException('MiniMax IA/OCR no respondió correctamente' . ($error ? ': ' . $error : '.'));
        }
        $data = json_decode($body, true);
        if (!is_array($data)) throw new \RuntimeException('MiniMax IA/OCR no devolvió JSON válido.');
        return $data;
    }
    private function content(array $data): string
    {
        $content = $data['choices'][0]['message']['content'] ?? '';
        $text = is_scalar($content) ? (string) $content : '';
        $text = preg_replace('#<think>.*?</think>#isu', '', $text) ?? $text;
        return $this->normalizeText($text);
    }
    private function prompt(string $name): string
    {
        return 'Extrae con OCR todo el texto legible de esta imagen de soporte de propiedad horizontal. '
            . 'Conserva matrículas, direcciones, nombres de copropiedad, coeficientes, unidades privadas, zonas comunes, administración, restricciones y fechas. '
            . 'No resumas, no expliques y no inventes datos. Devuelve solo texto plano. Archivo: ' . $name;
    }
    private function normalizeText(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/', "\n\n", $text) ?? $text;
        if (mb_strlen($text) < 30) throw new \RuntimeException('MiniMax IA/OCR no devolvió texto útil.');
        return $text;
    }

    private function apiKey(): string { return trim(Env::get('MINIMAX_API_KEY')); }
    private function endpoint(): string { return trim(Env::get('MINIMAX_API_ENDPOINT', self::DEFAULT_ENDPOINT)); }
    private function maxImages(): int { return max(1, min(20, (int) Env::get('MINIMAX_MAX_IMAGES', '6'))); }
    private function imageMaxBytes(): int { return max(1048576, min(10485760, (int) Env::get('MINIMAX_IMAGE_MAX_BYTES', '10485760'))); }
    private function isSupportedImage(string $extension, string $mime): bool
    {
        return in_array($extension, self::IMAGE_EXTENSIONS, true)
            || in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);
    }
    private function imageMime(string $extension, string $mime): string
    {
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) return $mime;
        return match ($extension) {
            'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif', default => 'image/jpeg',
        };
    }
    private function pdfRenderer(): string
    {
        $configured = trim(Env::get('PDFTOPPM_BINARY'));
        if ($configured !== '') return escapeshellarg($configured);
        if (!function_exists('shell_exec')) return '';
        $locator = PHP_OS_FAMILY === 'Windows' ? 'where pdftoppm 2>NUL' : 'command -v pdftoppm 2>/dev/null';
        $binary = preg_split('/\R/', trim((string) @shell_exec($locator)))[0] ?? '';
        return $binary !== '' ? escapeshellarg($binary) : '';
    }
    private function clearTemporaryDir(string $dir): void
    {
        foreach (glob($dir . '/*') ?: [] as $file) @unlink($file);
        @rmdir($dir);
    }
}
