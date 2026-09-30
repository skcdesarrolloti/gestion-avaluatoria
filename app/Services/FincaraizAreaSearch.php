<?php
declare(strict_types=1);
namespace App\Services;

final class FincaraizAreaSearch
{
    public function search(string $neighborhood, int $page): array
    {
        $url = self::url($neighborhood, $page);
        return $this->parse((new FincaraizListingReader())->fetch($url), $url, $page);
    }

    public static function url(string $neighborhood, int $page = 1): string
    {
        if ($page < 1 || $page > 10 || mb_strlen($neighborhood) > 100) throw new \InvalidArgumentException('Barrio o página inválidos.');
        $slug = strtolower((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', trim($neighborhood)));
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
        if (strlen($slug) < 3) throw new \InvalidArgumentException('Escribe el barrio que deseas consultar.');
        if ($slug === 'castillo-grande') $slug = 'castillogrande';
        return 'https://www.fincaraiz.com.co/venta/oficinas/' . $slug . '/cartagena' . ($page > 1 ? '/pagina' . $page : '');
    }

    public function parse(string $html, string $url, int $page): array
    {
        $previous = libxml_use_internal_errors(true);
        try {
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($doc);
            if ($xpath->evaluate('string(//link[@rel="canonical"]/@href)') !== $url) {
                throw new \RuntimeException('El portal no confirmó esa zona de búsqueda. Comprueba el barrio en FincaRaíz antes de capturar.');
            }
            $collection = null;
            foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
                $data = json_decode($script->textContent, true, 64);
                if (is_array($data) && ($data['@type'] ?? '') === 'CollectionPage') { $collection = $data; break; }
            }
            if (!$collection) throw new \RuntimeException('No se pudo leer el listado de ese barrio. Abre el portal y comprueba el filtro.');
            $rows = [];
            foreach (array_slice($collection['mainEntity']['itemListElement'] ?? [], 0, 30) as $item) {
                try {
                    $link = FincaraizListingReader::canonicalUrl((string) ($item['url'] ?? ''));
                    $encoded = json_encode($item, JSON_HEX_TAG | JSON_THROW_ON_ERROR);
                    $parsed = (new FincaraizListingParser())->parse('<script type="application/ld+json">' . $encoded . '</script>', $link);
                    $row = $parsed['row'];
                    if (preg_match('/^Oficina en Venta en (.+), Cartagena$/ui', $parsed['title'], $match)) $row['neighborhood'] = $match[1];
                    $row['comparability_notes'] = 'Capturado del resumen de resultados de FincaRaíz. Verificar ficha, ubicación, uso y clase de área; soporte pendiente.';
                    $rows[$link] = ['title' => $parsed['title'], 'row' => $row];
                } catch (\InvalidArgumentException | \RuntimeException) { continue; }
            }
            $base = preg_replace('~/pagina\d+$~', '', $url);
            $next = false;
            if ($page < 10) foreach ($xpath->query('//a[@href]') as $anchor) {
                $href = $anchor->getAttribute('href');
                if ($href === $base . '/pagina' . ($page + 1) || $href === parse_url($base, PHP_URL_PATH) . '/pagina' . ($page + 1)) $next = true;
            }
            return ['results' => array_values($rows), 'url' => $url, 'page' => $page, 'has_next' => $next];
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }
}
