<?php
declare(strict_types=1);
namespace App\Services;

final class MidasHttpClient
{
    public function postJson(string $endpoint, array $payload): ?array
    {
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $last = null;
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $response = $this->streamPost($endpoint, $body) ?? $this->curlPost($endpoint, $body);
            $json = is_string($response) ? json_decode($response, true) : null;
            if (is_array($json) && !$this->isRetryableError($json)) return $json;
            $last = $json;
            if ($attempt < 4) usleep(250000 * $attempt);
        }
        return is_array($last) ? $last : null;
    }

    private function isRetryableError(array $json): bool
    {
        $state = mb_strtolower((string) ($json['estado'] ?? ''));
        $message = mb_strtolower((string) ($json['mensaje'] ?? ''));
        return $state === 'error' || str_contains($message, 'exception while reading');
    }

    private function streamPost(string $endpoint, string $body): ?string
    {
        $context = stream_context_create(['http' => ['method' => 'POST', 'header' => $this->headerString(),
            'content' => $body, 'timeout' => 15, 'ignore_errors' => true]]);
        $response = @file_get_contents($endpoint, false, $context);
        return is_string($response) && trim($response) !== '' ? $response : null;
    }

    private function curlPost(string $endpoint, string $body): ?string
    {
        if (!function_exists('curl_init')) return null;
        $curl = curl_init($endpoint);
        if ($curl === false) return null;
        curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $this->headers(), CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 20, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_FOLLOWLOCATION => false]);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if (!is_string($response) || trim($response) === '' || $status >= 500 || $status === 0) {
            error_log('Gestion avaluatoria MIDAS HTTP sin respuesta status=' . $status . ' error=' . curl_error($curl));
            $response = null;
        }
        curl_close($curl);
        return $response;
    }

    private function headerString(): string
    {
        return implode("\r\n", $this->headers()) . "\r\n";
    }

    private function headers(): array
    {
        return ['Content-Type: application/json', 'Accept: application/json, text/plain, */*',
            'Origin: https://midas.cartagena.gov.co', 'Referer: https://midas.cartagena.gov.co/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) GestionAvaluatoria/1.0'];
    }
}
