<?php
declare(strict_types=1);
namespace App\Services;

/** Fetch only known public property sources, pinned to a public IPv4 address. */
final class ComparableDetailReader
{
    public static function canonicalUrl(string $url): string
    {
        $parts = parse_url(trim($url));
        $catalog = (new AppraisalComparableSourceSearchBuilder())->build([], ['city_name'=>'Cartagena'], 'oficina', 'Oficina', 'Venta');
        $domains = array_map(static fn ($source)=>$source['domain'] ?? preg_replace('/^www\./','',parse_url($source['url'],PHP_URL_HOST)),array_merge($catalog['portal_sources'],$catalog['agency_sources']));
        $host = strtolower($parts['host'] ?? '');
        $base = preg_replace('/^www\./', '', $host);
        if ($host==='inmueble.mercadolibre.com.co') $base='mercadolibre.com.co';
        if (!$parts || strlen($url)>2048 || ($parts['scheme'] ?? '')!=='https'
            || !in_array($base,$domains,true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])
            || !preg_match('~(?:inmueble|propiedad|property|apartamento|oficina|casa|local|bodega|lote|venta|arriendo|alquiler)[^?#]*[\d-]~i',$parts['path'] ?? '')) {
            throw new \InvalidArgumentException('Usa el enlace HTTPS de una ficha de los portales o inmobiliarias del catálogo.');
        }
        parse_str($parts['query'] ?? '',$query);
        foreach (array_keys($query) as $key) if (preg_match('/^(?:utm_|gclid$|fbclid$)/i',(string)$key)) unset($query[$key]);
        return 'https://'.$host.($parts['path'] ?? '/').($query?'?'.http_build_query($query,'','&',PHP_QUERY_RFC3986):'');
    }

    public function read(string $url): array
    {
        $url = self::canonicalUrl($url);
        if (preg_replace('/^www\./','',parse_url($url,PHP_URL_HOST))==='fincaraiz.com.co') return (new FincaraizListingReader())->read($url);
        if (!extension_loaded('curl')) throw new \RuntimeException('Lectura no disponible: copia el texto de la ficha.');
        $host = parse_url($url,PHP_URL_HOST);
        $addresses = gethostbynamel($host) ?: [];
        foreach ($addresses as $ip) if (!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4|FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) {
            throw new \RuntimeException('El destino no es una dirección pública admitida.');
        }
        if (!$addresses) throw new \RuntimeException('No se pudo resolver la fuente. Copia el texto de la ficha.');
        $html = ''; $curl = curl_init($url);
        curl_setopt_array($curl,[CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,
            CURLOPT_PROXY=>'',CURLOPT_RESOLVE=>[$host.':443:'.$addresses[0]],CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20,CURLOPT_ENCODING=>'',CURLOPT_USERAGENT=>'SuCasa-ComparableReader/1.0',
            CURLOPT_HTTPHEADER=>['Accept: text/html'],CURLOPT_WRITEFUNCTION=>static function ($handle,string $chunk) use (&$html): int {
                if (strlen($html)+strlen($chunk)>2097152) return 0;
                $html.=$chunk; return strlen($chunk);
            }]);
        $success = curl_exec($curl); $status = curl_getinfo($curl,CURLINFO_RESPONSE_CODE); curl_close($curl);
        if ($success===false || $status!==200) throw new \RuntimeException('La fuente no permitió leer la ficha. Abre el enlace y copia su texto para completarla.');
        return (new ComparableDetailParser())->parse($html,$url);
    }
}
