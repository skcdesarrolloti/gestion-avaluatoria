<?php
declare(strict_types=1);
namespace App\Services;

final class FincaraizListingParser
{
    public function parse(string $html, string $url): array
    {
        $url = FincaraizListingReader::canonicalUrl($url);
        $previous = libxml_use_internal_errors(true);
        try {
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $items = [];
            foreach ((new \DOMXPath($doc))->query('//script[@type="application/ld+json"]') as $script) {
                $data = json_decode($script->textContent, true, 64);
                if (is_array($data)) $items[] = $data;
            }
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        foreach ($items as $item) {
            if (!in_array('RealEstateListing', (array) ($item['@type'] ?? []), true) || ($item['url'] ?? '') !== $url) continue;
            $entity = is_array($item['mainEntity'] ?? null) ? $item['mainEntity'] : [];
            $offer = is_array($item['offers'] ?? null) ? $item['offers'] : [];
            $sale = str_contains($url, '-en-venta-en-');
            $row = ['source_type' => 'portal', 'source_name' => 'FincaRaíz', 'source_url' => $url,
                'listing_code' => basename($url), 'operation' => $sale ? 'Venta' : 'Arriendo',
                'consulted_at' => date('Y-m-d'),
                'comparability_notes' => 'Lectura automática del aviso; datos por verificar. Área publicada: confirmar si es privada, construida o terreno. Soporte fotográfico/PDF pendiente.'];
            if (($offer['priceCurrency'] ?? '') === 'COP' && $this->number($offer['price'] ?? null) !== '') {
                $row['price_amount'] = $this->number($offer['price']);
                $row['price_unit'] = $sale ? 'precio_total' : 'canon_mensual';
            }
            if (($entity['floorSize']['unitCode'] ?? '') === 'MTK') $row['area_m2'] = $this->number($entity['floorSize']['value'] ?? null);
            foreach (['bedrooms' => 'numberOfBedrooms', 'bathrooms' => 'numberOfBathroomsTotal'] as $key => $schema) {
                $row[$key] = $this->number($entity[$schema] ?? null);
            }
            $types = ['House' => 'Casa', 'Apartment' => 'Apartamento'];
            $row['property_type'] = $types[is_string($entity['@type'] ?? null) ? $entity['@type'] : ''] ?? 'Por verificar';
            // The portal's title states the use even where schema.org only says Place.
            if (preg_match('/^(Oficina|Consultorio|Local|Bodega|Lote|Parqueadero) en (Venta|Arriendo)\b/u', $item['name'] ?? '', $match)) $row['property_type'] = $match[1];
            $row['address_hint'] = $this->text($entity['address']['streetAddress'] ?? '');
            if (is_numeric($entity['geo']['latitude'] ?? null) && is_numeric($entity['geo']['longitude'] ?? null))
                $row['published_location'] = (string) $entity['geo']['latitude'] . ', ' . (string) $entity['geo']['longitude'] . ' · FincaRaíz; sin verificar.';
            $posted = $item['datePosted'] ?? '';
            if (is_string($posted) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $posted)) $row['listing_date'] = $posted;
            foreach ($items as $breadcrumb) {
                if (($breadcrumb['@type'] ?? '') !== 'BreadcrumbList') continue;
                foreach ($breadcrumb['itemListElement'] ?? [] as $crumb) {
                    if (($entity['address']['addressLocality'] ?? '') === 'Cartagena'
                        && preg_match('~^https://www\.fincaraiz\.com\.co/(venta|arriendo)/[^/]+/[^/]+/cartagena$~D', $crumb['item'] ?? '')) {
                        $row['neighborhood'] = $this->text($crumb['name'] ?? '');
                    }
                }
            }
            $description = is_string($item['description'] ?? null) ? $item['description'] : '';
            $description .= is_string($entity['description'] ?? null) ? ' ' . $entity['description'] : '';
            foreach ($entity['additionalProperty'] ?? [] as $attribute) {
                if (is_array($attribute) && is_scalar($attribute['name'] ?? null) && is_scalar($attribute['value'] ?? null))
                    $description .= "\n" . $attribute['name'] . ': ' . $attribute['value'] . ';';
            }
            $row = array_filter($row, static fn ($v) => $v !== '') + ComparablePublishedDetails::parse($description);
            $detail=FincaraizFichaDetails::parse($doc,$url);
            $row += $detail;
            if (!empty($detail['published_text'])) $row['published_text']=$detail['published_text'];
            $facts=json_decode($detail['published_attributes'] ?? '{}',true)+json_decode($row['published_attributes'],true);
            $row['published_attributes']=json_encode((object)array_slice($facts,0,80,true),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
            return ['row' => $row,
                'read_scope'=>$detail?'Descripción, ficha técnica e instalaciones publicadas.':'Datos estructurados y descripción; ficha técnica pendiente.',
                'title' => $this->text($item['name'] ?? 'Aviso'),
                'warning' => 'Revisa ubicación, uso y clase de área. El aviso puede discrepar de los filtros. No se descargaron fotos ni PDF; los campos ausentes siguen pendientes.'];
        }
        throw new \RuntimeException('El aviso no incluye datos estructurados reconocibles. Pega su enlace y texto en la captura manual.');
    }

    private function number(mixed $value): string
    {
        return is_numeric($value) && is_finite((float) $value) && (float) $value >= 0
            ? str_replace('.', ',', (string) $value) : '';
    }

    private function text(mixed $value): string
    {
        return is_string($value) ? mb_substr(trim(strip_tags($value)), 0, 180) : '';
    }
}
