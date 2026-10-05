<?php
declare(strict_types=1);
namespace App\Services;

final class MetrocuadradoResultsParser
{
    public function parse(string $html, string $url): array
    {
        $previous = libxml_use_internal_errors(true);
        try {
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($doc);
            $canonical = $xpath->evaluate('string(//link[@rel="canonical"]/@href)');
            if (rtrim($canonical, '/') !== rtrim($url, '/')) throw new \RuntimeException('Metrocuadrado no confirmó el barrio solicitado. Comprueba la zona en el portal.');
            $payload = '';
            foreach ($xpath->query('//script') as $script) {
                if (!preg_match('/^self\.__next_f\.push\((\[.*\])\)\s*;?$/s', trim($script->textContent), $match)) continue;
                $chunk = json_decode($match[1], true, 64);
                if (($chunk[0] ?? null) === 1 && is_string($chunk[1] ?? null)) $payload .= $chunk[1];
            }
            $collection = null;
            foreach (explode("\n", $payload) as $line) {
                $colon = strpos($line, ':');
                if ($colon === false) continue;
                $data = json_decode(substr($line, $colon + 1), true, 64);
                if (is_array($data) && ($collection = $this->collection($data))) break;
            }
            if (!is_array($collection) || !isset($collection['results']) || !is_array($collection['results'])) {
                throw new \RuntimeException('No se reconoció el listado de Metrocuadrado. Usa el pegado de texto mientras se revisa el formato.');
            }
            $rows = [];
            foreach (array_slice($collection['results'], 0, 50) as $item) {
                if (!is_array($item)) continue;
                $row = $this->row($item);
                if ($row) $rows[$row['source_url']] = ['title' => $this->text($item['title'] ?? 'Oficina en venta', 200), 'row' => $row];
            }
            $total = max(0, (int) ($collection['totalHits'] ?? count($rows)));
            return ['results' => array_values($rows), 'url' => $url, 'page' => 1, 'has_next' => false,
                'notice' => $total > count($collection['results']) ? 'El portal anuncia más resultados. Esta lectura incluye hasta 50 avisos iniciales; abre el portal para revisar los restantes.' : 'Resultados del barrio publicado por el portal; verifica la ubicación de cada aviso.'];
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }

    private function collection(array $node, int $depth = 0): ?array
    {
        if ($depth > 25) return null;
        if (is_array($node['initialResults'] ?? null)) return $node['initialResults'];
        foreach ($node as $value) if (is_array($value) && ($found = $this->collection($value, $depth + 1))) return $found;
        return null;
    }

    private function row(array $item): ?array
    {
        $path = $item['link'] ?? '';
        if (!is_string($path) || !preg_match('~^/inmueble/venta-oficina-[a-z0-9-]+/[0-9]+-M[0-9]+$~D', $path)
            || mb_strtolower((string) ($item['mtiponegocio'] ?? '')) !== 'venta'
            || mb_strtolower((string) ($item['mciudad']['nombre'] ?? '')) !== 'cartagena de indias'
            || mb_strtolower((string) ($item['mtipoinmueble']['nombre'] ?? '')) !== 'oficina') return null;
        $neighborhood = $this->text($item['mnombrecomunbarrio'] ?? $item['mbarrio'] ?? '', 140);
        $notes = 'Resumen de Metrocuadrado. Verificar ubicación, vigencia, tipo de área y soporte. PH por verificar.';
        $portalNeighborhood = $this->text($item['mbarrio'] ?? '', 140);
        if ($portalNeighborhood !== '') $notes .= ' Barrio alternativo publicado: ' . $portalNeighborhood . '.';
        $details=ComparablePublishedDetails::parse($this->text($item['description'] ?? $item['mdescripcion'] ?? $item['data']['mdescripcion'] ?? '', 16000));
        $facts=json_decode($details['published_attributes'],true) + ComparableSourceFacts::structured($item);
        $details['published_attributes']=json_encode((object)array_slice($facts,0,80,true),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        return ['source_type' => 'portal', 'source_name' => 'Metrocuadrado', 'source_url' => 'https://www.metrocuadrado.com' . $path,
            'listing_code' => $this->text($item['midinmueble'] ?? '', 120), 'operation' => 'Venta', 'property_type' => 'Oficina',
            'price_amount' => $this->number($item['mvalorventa'] ?? null), 'price_unit' => 'precio_total',
            'area_m2' => $this->number($item['marea'] ?? null), 'admin_fee' => $this->number($item['data']['mvaloradministracion'] ?? null),
            'neighborhood' => $neighborhood, 'project_name' => $this->text($item['mnombreproyecto'] ?? '', 180),
            'contact_name' => $this->text($item['data']['mnombrevisitor'] ?? '', 120), 'contact_phone' => $this->text($item['contactPhone'] ?? '', 80),
            'bathrooms' => $this->number($item['mnrobanos'] ?? null), 'parking_spaces' => $this->number($item['mnrogarajes'] ?? null),
            'ph_regime' => 'por_verificar', 'consulted_at' => date('Y-m-d'), 'comparability_notes' => $notes]
            + $details;
    }

    private function text(mixed $value, int $limit): string { return is_scalar($value) ? mb_substr(trim((string) $value), 0, $limit) : ''; }
    private function number(mixed $value): string { return is_scalar($value) && is_numeric($value) && (float) $value >= 0 ? (string) $value : ''; }
}
