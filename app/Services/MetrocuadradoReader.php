<?php
declare(strict_types=1);
namespace App\Services;

final class MetrocuadradoReader
{
    public function fetch(string $url): string
    {
        if (!preg_match('~^https://www\.metrocuadrado\.com/oficinas/venta/cartagena-de-indias/(?:[a-z0-9-]+/)?$~D', $url)) {
            throw new \InvalidArgumentException('Dirección de búsqueda de Metrocuadrado inválida.');
        }
        if (!extension_loaded('curl')) throw new \RuntimeException('El servidor necesita PHP cURL.');
        $ip = (gethostbynamel('www.metrocuadrado.com') ?: [])[0] ?? '';
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new \RuntimeException('No se pudo resolver el portal de forma segura.');
        }
        $html = '';
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_PROXY => '', CURLOPT_RESOLVE => ['www.metrocuadrado.com:443:' . $ip],
            CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 18, CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => 'SuCasa-ComparableReader/1.0', CURLOPT_HTTPHEADER => ['Accept: text/html'],
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$html): int {
                if (strlen($html) + strlen($chunk) > 4194304) return 0;
                $html .= $chunk;
                return strlen($chunk);
            }]);
        $success = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($success === false || $status !== 200) throw new \RuntimeException('Metrocuadrado no permitió leer los resultados. Reintenta o usa enlace y texto; no se agregó ninguna muestra.');
        return $html;
    }
}
