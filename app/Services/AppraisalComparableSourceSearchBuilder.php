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
        $typeTerms = $this->typeTerms($type, $typeLabel);
        $terms = array_values(array_filter([$operation, $typeTerms, $neighborhood ?: $locality, $city]));
        $query = implode(' ', $terms);
        return [
            'city' => $city,
            'neighborhood' => $neighborhood,
            'query' => $query,
            'query_parts' => $this->queryParts($operation, $typeTerms, $neighborhood, $locality, $city),
            'portal_sources' => (new ComparablePortalLinks())->build($query, $operation, $type, $city, $neighborhood),
            'agency_sources' => $this->agencySources($query, $city),
            'official_sources' => $this->officialSources($query, $city, $neighborhood, $subject),
            'capture_protocol' => $this->captureProtocol($operation),
        ];
    }

    private function agencySources(string $query, string $city): array
    {
        return $this->isCartagena($city) ? $this->cartagenaAgencies($query) : [];
    }

    private function cartagenaAgencies(string $query): array
    {
        return [
            $this->agency('Araújo & Segovia', 'https://www.araujoysegovia.com/', $query, 'Principal',
                ['Alta trayectoria y marca regional con operación inmobiliaria desde 1953.', 'Presencia multiciudad con sede y portafolio visible en Cartagena.', 'Oferta de arriendos, ventas, avalúos y administración; útil como referente amplio.']),
            $this->agency('SuCasa Inmobiliaria', 'https://sucasainmobiliaria.com.co/', $query, 'Principal',
                ['Portafolio local amplio por arriendo y venta.', 'Filtros por tipo, operación, barrio, precio y área.', 'Cobertura de apartamentos, casas, locales, oficinas, lotes y bodegas.']),
            $this->agency('Asesorar Inmobiliaria', 'https://asesorarinmobiliaria.com/', $query, 'Principal',
                ['Sitio activo con propiedades en venta y arriendo en Cartagena.', 'Servicios de venta, arriendo, administración y avalúos.', 'Presencia en listados sectoriales y canales sociales de referencia local.']),
            $this->agency('Inmobiliaria Cartagena Ltda.', 'https://www.inmobiliariacartagena.com/', $query, 'Principal',
                ['Marca local tradicional con presencia sectorial documentada.', 'Portafolio propio y actividad en venta y arriendo.', 'Sirve como fuente directa para confirmar disponibilidad con asesor.']),
            $this->agency('Vélez Palomino Real Estate', 'https://velezpalomino.com/', $query, 'Especializada',
                ['Inventario visible por venta y alquiler.', 'Búsqueda avanzada por barrio, tipo, negocio y precio.', 'Útil en apartamentos, oficinas, locales y activos de mayor valor.']),
            $this->agency('Inverfin Inmobiliaria', 'https://inmobiliariainverfin.com/', $query, 'Especializada',
                ['Opera compra, venta, arriendo y administración en Cartagena.', 'Tiene buscador por ciudad, localidad, barrio, tipo y negocio.', 'Aporta contraste en zonas residenciales y turísticas.']),
            $this->agency('ACR Inmobiliaria', 'https://acrinmobiliaria.com/', $query, 'Especializada',
                ['Red local de captadores en Cartagena de Indias.', 'Publica oferta por barrios específicos como Castillogrande.', 'Útil para validar inmuebles captados por red y propiedades remodeladas.']),
            $this->agency('Metrolineal Inmobiliaria', 'https://metrolinealinmobiliaria.com/asesores', $query, 'Complementaria',
                ['Presencia local en Cartagena con compra, venta, arriendo y administración.', 'Puede aportar muestra adicional cuando los principales no tienen suficientes comparables.', 'Útil para oficinas, locales, vivienda y proyectos.']),
            $this->agency('Invercartagena Inmobiliaria', 'https://invercartagenainmobiliaria.com/', $query, 'Complementaria',
                ['Trayectoria local reportada en Cartagena.', 'Trabaja compra, venta, administración y alquiler.', 'Usa canales de difusión inmobiliaria para ampliar exposición de inmuebles.']),
            $this->agency('IBR Inmobiliaria', 'https://www.ibrinmobiliaria.com/propiedades/venta/apartamentos', $query, 'Complementaria',
                ['Listado con códigos de inmueble y asesor local.', 'Cobertura de barrios de distintos rangos de precio.', 'Útil para contrastar vivienda media y económica cuando aplique.']),
            $this->agency('Invercolombia', 'https://invercolombia.com.co/', $query, 'Proyecto nuevo',
                ['Presencia fuerte en proyectos inmobiliarios de Cartagena.', 'Útil para contraste de mercado primario o vivienda nueva.', 'No sustituye comparables usados cuando el sujeto sea reventa.']),
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

    private function queryParts(string $operation, string $typeTerms, string $neighborhood, string $locality, string $city): array
    {
        return [
            ['label' => 'Operación', 'value' => $operation, 'origin' => 'Numeral 1 · tipo de negocio'],
            ['label' => 'Tipo de inmueble', 'value' => $typeTerms, 'origin' => 'Numeral 1 · tipología del bien'],
            ['label' => 'Barrio o microsector', 'value' => $neighborhood, 'origin' => 'Numeral 2/3 · ubicación del sujeto'],
            ['label' => 'Localidad', 'value' => $locality, 'origin' => 'Numeral 2 · sector y entorno'],
            ['label' => 'Ciudad', 'value' => $city, 'origin' => 'Expediente / bien sujeto'],
            ['label' => 'País', 'value' => 'Colombia', 'origin' => 'Filtro fijo para portales nacionales'],
        ];
    }

    private function agency(string $label, string $url, string $query, string $category, array $factors): array
    {
        return ['label' => $label, 'kind' => 'Inmobiliaria local seleccionada', 'query' => $query,
            'url' => $url, 'category' => $category, 'selection_factors' => $factors,
            'instruction' => 'Buscar dentro de la inmobiliaria por operación, barrio, tipología y rango de área.'];
    }

    private function operation(string $key, string $label): string
    {
        return match ($key) {
            'arriendo' => 'arriendo',
            'venta' => 'venta',
            default => trim($label) !== '' ? mb_strtolower($label) : 'venta arriendo',
        };
    }

    private function typeTerms(string $type, string $label): string
    {
        if ($type === '') return 'inmueble';
        return match ($type) {
            'oficina' => 'oficina',
            'consultorio' => 'consultorio',
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
