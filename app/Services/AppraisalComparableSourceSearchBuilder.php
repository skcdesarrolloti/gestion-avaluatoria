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
            'portal_sources' => $this->portalSources($query, $city),
            'official_sources' => $this->officialSources($query, $city, $neighborhood, $subject),
            'capture_protocol' => $this->captureProtocol($operation),
        ];
    }

    private function portalSources(string $query, string $city): array
    {
        if ($this->isCartagena($city)) {
            return $this->cartagenaAgencies($query);
        }
        return [
            $this->source('FincaRaiz', 'Portal inmobiliario', 'site:fincaraiz.com.co ' . $query),
            $this->source('Metrocuadrado', 'Portal inmobiliario', 'site:metrocuadrado.com ' . $query),
            $this->source('Ciencuadras', 'Portal inmobiliario', 'site:ciencuadras.com ' . $query),
            $this->source('Properati', 'Portal inmobiliario', 'site:properati.com.co ' . $query),
            $this->source('Mercado Libre Inmuebles', 'Portal / clasificados', 'site:inmuebles.mercadolibre.com.co ' . $query),
            $this->source('Inmobiliarias locales', 'Búsqueda abierta', $query . ' inmobiliaria local'),
        ];
    }

    private function cartagenaAgencies(string $query): array
    {
        return [
            $this->agency('SuCasa Inmobiliaria', 'https://sucasainmobiliaria.com.co/', $query,
                ['Portafolio local amplio por arriendo y venta.', 'Filtros por tipo, operación, barrio, precio y área.', 'Cobertura de apartamentos, casas, locales, oficinas, lotes y bodegas.']),
            $this->agency('Vélez Palomino Real Estate', 'https://velezpalomino.com/', $query,
                ['Inventario visible por venta y alquiler.', 'Búsqueda avanzada por barrio, tipo, negocio y precio.', 'Útil en apartamentos, oficinas, locales y activos de mayor valor.']),
            $this->agency('Inverfin Inmobiliaria', 'https://inmobiliariainverfin.com/', $query,
                ['Opera compra, venta, arriendo y administración en Cartagena.', 'Tiene buscador por ciudad, localidad, barrio, tipo y negocio.', 'Aporta contraste en zonas residenciales y turísticas.']),
            $this->agency('ACR Inmobiliaria', 'https://acrinmobiliaria.com/', $query,
                ['Red local de captadores en Cartagena de Indias.', 'Publica oferta por barrios específicos como Castillogrande.', 'Útil para validar inmuebles captados por red y propiedades remodeladas.']),
            $this->agency('Inmobiliaria Cartagena Ltda.', 'https://www.inmobiliariacartagena.com/', $query,
                ['Marca local con portafolio propio y presencia en portales.', 'Publica oferta en venta y arriendo en sectores tradicionales.', 'Sirve como fuente directa para confirmar disponibilidad con asesor.']),
            $this->agency('Invercartagena Inmobiliaria', 'https://invercartagenainmobiliaria.com/', $query,
                ['Trayectoria local reportada en Cartagena.', 'Trabaja compra, venta, administración y alquiler.', 'Usa canales de difusión inmobiliaria para ampliar exposición de inmuebles.']),
            $this->agency('IBR Inmobiliaria', 'https://www.ibrinmobiliaria.com/propiedades/venta/apartamentos', $query,
                ['Listado amplio con códigos de inmueble.', 'Cobertura de barrios de distintos rangos de precio.', 'Útil para contrastar vivienda media y económica cuando aplique.']),
            $this->agency('Invercolombia', 'https://invercolombia.com.co/', $query,
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

    private function agency(string $label, string $url, string $query, array $factors): array
    {
        return ['label' => $label, 'kind' => 'Inmobiliaria local seleccionada', 'query' => $query,
            'url' => $url, 'selection_factors' => $factors,
            'instruction' => 'Buscar dentro de la inmobiliaria por operación, barrio, tipología y rango de área.'];
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
