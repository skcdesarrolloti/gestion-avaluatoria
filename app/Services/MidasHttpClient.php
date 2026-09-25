<?php
declare(strict_types=1);
namespace App\Services;

final class MidasHttpClient
{
    private string $lastTransportError = '';

    public function postJson(string $endpoint, array $payload): ?array
    {
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $last = null;
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            foreach ([$this->streamPost(...), $this->curlPost(...)] as $transport) {
                $json = $this->decode($transport($endpoint, $body));
                if (is_array($json) && !$this->isRetryableError($json)) return $json;
                if (is_array($json)) $last = $json;
            }
            if ($attempt < 4) usleep(250000 * $attempt);
        }
        if (is_array($last)) return $last;
        if ($this->lastTransportError !== '') {
            return ['estado' => 'error', 'mensaje' => 'Sin respuesta HTTP de MIDAS desde el servidor: ' . $this->lastTransportError];
        }
        return null;
    }

    private function decode(?string $response): ?array
    {
        if (!is_string($response) || trim($response) === '') return null;
        $json = json_decode($response, true);
        if (is_array($json)) return $json;
        $sample = mb_substr(trim(strip_tags($response)), 0, 140);
        $this->lastTransportError = $sample !== '' ? 'MIDAS devolvió una respuesta no JSON: ' . $sample : 'MIDAS devolvió una respuesta no JSON.';
        return null;
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
        if (is_string($response) && trim($response) !== '') return $response;
        $error = error_get_last();
        $this->lastTransportError = is_array($error) ? (string) ($error['message'] ?? '') : 'Sin respuesta por stream.';
        return null;
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
            $this->lastTransportError = 'cURL status=' . $status . ' error=' . curl_error($curl);
            error_log('Gestion avaluatoria MIDAS HTTP sin respuesta ' . $this->lastTransportError);
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
