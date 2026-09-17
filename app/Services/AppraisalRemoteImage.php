<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalRemoteImage
{
    private const MAX_BYTES = 6291456;
    private const MIME_EXT = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public static function fetch(string $url): array
    {
        $url = trim($url);
        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = (string) ($parts['host'] ?? '');
        if (!in_array($scheme, ['https', 'http'], true) || $host === '') {
            throw new \InvalidArgumentException('Pega una URL pública de imagen JPG, PNG o WEBP.');
        }
        self::assertPublicHost($host);
        $tmp = tempnam(sys_get_temp_dir(), 'ga-img-');
        if ($tmp === false) throw new \RuntimeException('No se pudo preparar la descarga de imagen.');
        try {
            self::download($url, $tmp);
            $info = @getimagesize($tmp);
            $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
            if (!isset(self::MIME_EXT[$mime])) {
                throw new \InvalidArgumentException('La URL no entrega una imagen JPG, PNG o WEBP.');
            }
            return ['path' => $tmp, 'name' => self::filename($parts['path'] ?? '', self::MIME_EXT[$mime])];
        } catch (\Throwable $error) {
            @unlink($tmp);
            throw $error;
        }
    }

    private static function download(string $url, string $destination): void
    {
        $in = @fopen($url, 'rb', false, stream_context_create(['http' => [
            'timeout' => 12, 'follow_location' => 0,
            'header' => "User-Agent: GestionAvaluatoria/1.0\r\n",
        ]]));
        if (!is_resource($in)) throw new \RuntimeException('No se pudo leer la imagen desde la URL.');
        $out = fopen($destination, 'wb');
        if (!is_resource($out)) {
            fclose($in);
            throw new \RuntimeException('No se pudo guardar temporalmente la imagen.');
        }
        try {
            $bytes = 0;
            while (!feof($in)) {
                $chunk = fread($in, 8192);
                if (!is_string($chunk)) break;
                $bytes += strlen($chunk);
                if ($bytes > self::MAX_BYTES) throw new \InvalidArgumentException('La imagen supera 6 MB.');
                fwrite($out, $chunk);
            }
        } finally {
            fclose($in);
            fclose($out);
        }
    }

    private static function assertPublicHost(string $host): void
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $records = [['ip' => $host]];
        } else {
            $records = dns_get_record($host, DNS_A | DNS_AAAA);
        }
        if (!$records) throw new \InvalidArgumentException('No se pudo resolver la URL de imagen.');
        foreach ($records as $record) {
            $ip = (string) ($record['ip'] ?? $record['ipv6'] ?? '');
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new \InvalidArgumentException('La URL debe apuntar a una dirección pública.');
            }
        }
    }

    private static function filename(string $path, string $extension): string
    {
        $base = preg_replace('/[^A-Za-z0-9._-]+/', '-', basename($path) ?: 'imagen-remota');
        $base = trim((string) $base, '.-') ?: 'imagen-remota';
        return preg_replace('/\.[A-Za-z0-9]+$/', '', $base) . '.' . $extension;
    }
}
