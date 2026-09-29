<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalComparableSourceSearchBuilder
{
    public function build(array $record, array $subject, string $type, string $typeLabel, string $businessLabel): array
    {
        $city = $this->first($subject['city_name'] ?? '', $record['municipio'] ?? '');
        $neighborhood = $this->first($subject['neighborhood_name'] ?? '', $subject['midas_territory'] ?? '');
        $locality = $this->first($subject['locality_name'] ?? '', $subject['midas_locality'] ?? '');
        $operation = $this->operation((string) ($record['tipo_negocio'] ?? ''), $businessLabel);
        $terms = array_values(array_filter([$operation, $this->typeTerms($type, $typeLabel), $neighborhood, $locality, $city, 'Colombia']));
        $query = implode(' ', $terms);
        return [
            'city' => $city,
            'neighborhood' => $neighborhood,
            'query' => $query,
            'portal_sources' => $this->portalSources($query),
            'official_sources' => $this->officialSources($query, $city, $neighborhood, $subject),
            'capture_protocol' => $this->captureProtocol($operation),
        ];
    }

    private function portalSources(string $query): array
    {
        return [
            $this->source('FincaRaiz', 'Portal inmobiliario', 'site:fincaraiz.com.co ' . $query),
            $this->source('Metrocuadrado', 'Portal inmobiliario', 'site:metrocuadrado.com ' . $query),
            $this->source('Ciencuadras', 'Portal inmobiliario', 'site:ciencuadras.com ' . $query),
            $this->source('Properati', 'Portal inmobiliario', 'site:properati.com.co ' . $query),
            $this->source('Mercado Libre Inmuebles', 'Portal / clasificados', 'site:inmuebles.mercadolibre.com.co ' . $query),
            $this->source('Inmobiliarias locales', 'Búsqueda abierta', $query . ' inmobiliaria local'),
        ];
    }

    private function officialSources(string $query, string $city, string $neighborhood, array $subject): array
    {
        $items = [
            $this->source('Búsqueda oficial municipal', 'Fuente pública', 'site:.gov.co ' . $query . ' uso suelo POT catastro'),
            $this->source('Datos Abiertos Colombia', 'Fuente pública', 'site:datos.gov.co ' . $city . ' ' . $neighborhood . ' barrio localidad catastro'),
        ];
        if ($this->isCartagena($city)) {
            $items[] = ['label' => 'MIDAS Cartagena', 'kind' => 'Geoportal oficial',
                'url' => 'https://midas.cartagena.gov.co/', 'query' => $this->first($subject['adopted_address'] ?? '', $subject['address'] ?? '', $neighborhood, $city),
                'instruction' => 'Consultar predio, uso del suelo, tratamiento, riesgo, barrio/localidad y descargar soporte cuando aplique.'];
            $items[] = ['label' => 'POT Cartagena', 'kind' => 'Norma urbana local',
                'url' => 'https://pot.cartagena.gov.co/', 'query' => trim($neighborhood . ' ' . $city . ' uso del suelo'),
                'instruction' => 'Contrastar la búsqueda de mercado con POT, áreas de actividad, tratamiento y restricciones.'];
        }
        return $items;
    }

    private function captureProtocol(string $operation): array
    {
        return [
            'Abrir cada fuente y conservar enlace, fecha de consulta, captura o PDF del aviso.',
            'Filtrar por ' . strtolower($operation) . ', ciudad, barrio o microsector antes de ampliar radio de búsqueda.',
            'Registrar precio o canon, área, dirección aproximada, edificio/proyecto, contacto y estado de publicación.',
            'Descartar duplicados entre portales por fotos, teléfono, dirección, precio, área o código de anuncio.',
            'No adoptar automáticamente el dato: la muestra pasa luego a depuración técnica, comparación y cálculo.',
        ];
    }

    private function source(string $label, string $kind, string $query): array
    {
        return ['label' => $label, 'kind' => $kind, 'query' => $query,
            'url' => 'https://www.google.com/search?q=' . rawurlencode($query),
            'instruction' => 'Abrir búsqueda, aplicar filtros del portal y registrar solo ofertas verificables.'];
    }

    private function operation(string $key, string $label): string
    {
        return match ($key) {
            'arriendo' => 'arriendo alquiler',
            'venta' => 'venta',
            default => trim($label) !== '' ? mb_strtolower($label) : 'venta arriendo',
        };
    }

    private function typeTerms(string $type, string $label): string
    {
        return match ($type) {
            'oficina' => 'oficina consultorio edificio empresarial',
            'consultorio' => 'consultorio oficina servicios salud',
            'local' => 'local comercial',
            'bodega' => 'bodega industrial logística',
            'lote' => 'lote terreno',
            'parqueadero' => 'parqueadero garaje cupo',
            default => trim($label) !== '' ? mb_strtolower($label) : 'inmueble',
        };
    }

    private function isCartagena(string $city): bool
    {
        return str_contains($this->ascii($city), 'cartagena');
    }

    private function first(string ...$values): string
    {
        foreach ($values as $value) if (trim($value) !== '') return trim($value);
        return '';
    }

    private function ascii(string $value): string
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($value));
        return is_string($clean) ? $clean : mb_strtolower($value);
    }
}
