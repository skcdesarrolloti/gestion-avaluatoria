<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Env;

final class AppraisalExternalOcrClient
{
    public function __construct(private mixed $transport = null) {}

    public function diagnostics(): array
    {
        if ($this->provider() === 'minimax') {
            $diagnostics = (new MiniMaxOcrClient())->diagnostics();
            return ['configured' => $diagnostics['configured'], 'curl' => $diagnostics['curl'],
                'provider' => 'minimax', 'pdf_render' => $diagnostics['pdf_render']];
        }
        return ['configured' => $this->endpoint() !== '', 'curl' => function_exists('curl_init'), 'provider' => 'generic'];
    }

    public function extract(string $path, string $filename, string $mime): string
    {
        if ($this->transport) return $this->normalizeText(($this->transport)($path, $filename, $mime));
        if ($this->provider() === 'minimax') return (new MiniMaxOcrClient())->extract($path, $filename, $mime);
        $endpoint = $this->endpoint();
        if ($endpoint === '') throw new \RuntimeException('Configura PH_EXTERNAL_OCR_ENDPOINT para usar IA/OCR externo.');
        if (!function_exists('curl_init')) throw new \RuntimeException('El OCR externo requiere la extensión cURL de PHP.');
        if (!is_file($path)) throw new \RuntimeException('No se encontró el archivo PH para OCR externo.');
        $curl = curl_init($endpoint);
        $headers = ['Accept: application/json'];
        $token = trim(Env::get('PH_EXTERNAL_OCR_TOKEN'));
        if ($token !== '') $headers[] = 'Authorization: Bearer ' . $token;
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => max(10, min(300, (int) Env::get('PH_EXTERNAL_OCR_TIMEOUT_SECONDS', '120'))),
            CURLOPT_POSTFIELDS => [
                Env::get('PH_EXTERNAL_OCR_FILE_FIELD', 'file') => curl_file_create($path, $mime, $filename),
                'filename' => $filename, 'mime_type' => $mime,
            ],
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if (!is_string($body) || $body === '' || $status < 200 || $status >= 300) {
            throw new \RuntimeException('El proveedor OCR externo no respondió correctamente' . ($error ? ': ' . $error : '.'));
        }
        $data = json_decode($body, true);
        if (!is_array($data)) throw new \RuntimeException('El proveedor OCR externo no devolvió JSON válido.');
        return $this->normalizeText($this->valueByPath($data, Env::get('PH_EXTERNAL_OCR_TEXT_KEY', 'text')));
    }

    private function endpoint(): string
    {
        $endpoint = trim(Env::get('PH_EXTERNAL_OCR_ENDPOINT'));
        return preg_match('#^https?://#i', $endpoint) ? $endpoint : '';
    }

    private function provider(): string
    {
        return mb_strtolower(trim(Env::get('PH_OCR_PROVIDER', 'generic')));
    }

    private function valueByPath(array $data, string $path): mixed
    {
        $value = $data;
        foreach (array_filter(explode('.', $path)) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) return '';
            $value = $value[$key];
        }
        return $value;
    }

    private function normalizeText(mixed $value): string
    {
        $text = trim(is_scalar($value) ? (string) $value : '');
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\R{3,}/', "\n\n", $text) ?? $text;
        if (mb_strlen($text) < 30) throw new \RuntimeException('El OCR externo no devolvió texto útil.');
        return $text;
    }
}
