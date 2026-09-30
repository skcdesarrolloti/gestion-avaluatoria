<?php
declare(strict_types=1);
namespace App\Services;

final class FincaraizListingReader
{
    public static function canonicalUrl(string $url): string
    {
        $parts = parse_url(trim($url));
        if (!$parts || strlen($url) > 2048 || ($parts['scheme'] ?? '') !== 'https'
            || !in_array(strtolower($parts['host'] ?? ''), ['www.fincaraiz.com.co', 'fincaraiz.com.co'], true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])
            || !preg_match('~^/[a-z0-9-]+-en-(venta|arriendo)-en-[a-z0-9-]+/[0-9]+/?$~D', $parts['path'] ?? '')) {
            throw new \InvalidArgumentException('Pega el enlace de un inmueble de FincaRaíz, no el listado de resultados. Otros portales usan por ahora el pegado de texto.');
        }
        return 'https://www.fincaraiz.com.co' . rtrim($parts['path'], '/');
    }

    public function read(string $url): array
    {
        $url = self::canonicalUrl($url);
        if (!extension_loaded('curl')) throw new \RuntimeException('El servidor necesita la extensión PHP cURL para leer avisos.');
        $addresses = gethostbynamel('www.fincaraiz.com.co') ?: [];
        $ip = $addresses[0] ?? '';
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new \RuntimeException('No se pudo conectar con el portal. Intenta de nuevo o pega el texto del aviso.');
        }
        $html = '';
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_PROXY => '', CURLOPT_RESOLVE => ['www.fincaraiz.com.co:443:' . $ip],
            CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 20, CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => 'SuCasa-ComparableReader/1.0', CURLOPT_HTTPHEADER => ['Accept: text/html'],
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$html): int {
                if (strlen($html) + strlen($chunk) > 2097152) return 0;
                $html .= $chunk;
                return strlen($chunk);
            }]);
        $success = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($success === false || $status !== 200) {
            throw new \RuntimeException('El portal no permitió leer este aviso o ya no está disponible. Puedes copiar su texto en la captura manual.');
        }
        return (new FincaraizListingParser())->parse($html, $url);
    }
}
