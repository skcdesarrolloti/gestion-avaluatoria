<?php
declare(strict_types=1);
namespace App\Services;

final class ComparablePortalLinks
{
    public function build(string $query, string $operation, string $type, string $city, string $neighborhood): array
    {
        $sources = [];
        foreach (['FincaRaiz' => 'fincaraiz.com.co', 'Metrocuadrado' => 'metrocuadrado.com',
            'Ciencuadras' => 'ciencuadras.com', 'Properati' => 'properati.com.co',
            'Mercado Libre Inmuebles' => 'mercadolibre.com.co'] as $label => $domain) {
            $sources[] = ['label' => $label, 'domain' => $domain, 'kind' => 'Búsqueda en Google', 'query' => $query,
                'url' => 'https://www.google.com/search?q=' . rawurlencode('site:' . $domain . ' ' . $query),
                'instruction' => 'Busca en Google dentro del portal. Abre el aviso y comprueba los filtros.'];
        }
        $sources[3]['network'] = 'Proppit';
        // Only enable combinations checked in the actual portal. Others retain an explicit fallback.
        $cartagena = in_array($this->slug($city), ['cartagena', 'cartagena-de-indias'], true);
        if (!$cartagena || $operation !== 'venta') return $sources;
        $castillogrande = in_array($this->slug($neighborhood), ['castillogrande', 'castillo-grande'], true);
        if ($type === 'oficina' || ($type === 'casa' && $castillogrande)) {
            $zone = $this->slug($neighborhood);
            $officeZone = $type === 'oficina' && strlen($zone) >= 3;
            $place = $castillogrande ? 'castillogrande/cartagena' : ($officeZone ? $zone . '/cartagena' : 'cartagena/bolivar');
            $sources[0]['url'] = 'https://www.fincaraiz.com.co/venta/' . ($type === 'oficina' ? 'oficinas' : 'casas') . '/' . $place;
            $sources[0]['kind'] = 'Filtros del portal';
            $sources[0]['instruction'] = ($castillogrande || $officeZone) ? 'Venta y tipo preparados para ' . $neighborhood . '; comprueba que el portal muestre ese barrio seleccionado antes de copiar los resultados.'
                : 'Venta, oficina y Cartagena aplicados. Falta seleccionar o comprobar el barrio: ' . $neighborhood . '.';
        }
        if ($type === 'oficina') {
            if ($this->slug($neighborhood) === 'bocagrande') {
                $sources[4]['url'] = 'https://listado.mercadolibre.com.co/inmuebles/oficinas/venta/bolivar/cartagena-de-indias/bocagrande/';
                $sources[4]['kind'] = 'Filtros del portal';
                $sources[4]['instruction'] = 'Oficinas en venta en Bocagrande, Cartagena. Copia la página completa de resultados para preparar el lote.';
            }
            $sources[3]['url'] = 'https://www.properati.com.co/s/'
                . ($this->slug($neighborhood) === 'bocagrande' ? 'bocagrande' : 'cartagena-bolivar') . '/oficina/venta';
            $sources[3]['kind'] = 'Filtros del portal · Red Proppit';
            $sources[3]['instruction'] = $this->slug($neighborhood) === 'bocagrande'
                ? 'Oficinas en venta en Bocagrande. Copia los resultados de cada página para preparar el lote.'
                : 'Oficinas en venta en Cartagena. Selecciona o comprueba el barrio «' . $neighborhood . '» antes de copiar los resultados.';
            $sources[1]['url'] = 'https://www.metrocuadrado.com/oficinas/venta/cartagena-de-indias/';
            $sources[2]['url'] = 'https://www.ciencuadras.com/venta/oficina?v=' . rawurlencode(trim($neighborhood) ?: $city);
            foreach ([1, 2] as $index) {
                $sources[$index]['kind'] = 'Filtros del portal';
                $sources[$index]['instruction'] = 'Venta, oficina y Cartagena aplicados. Comprueba el barrio «' . $neighborhood . '» en el portal; no está filtrado en este enlace.';
            }
            if ($this->slug($neighborhood) === 'bocagrande') {
                $sources[1]['url'] .= 'bocagrande/';
                $sources[1]['instruction'] = 'Oficinas en venta en Bocagrande, Cartagena. Comprueba la ubicación de cada aviso y copia los resultados para preparar el lote.';
            }
            $sources[2]['instruction'] = 'Venta y oficina aplicados. En «Ciudad, barrio, sector o sitio» escribe ' . ($neighborhood ?: $city) . ' si aparece vacío y pulsa Enter. Comprueba que los resultados correspondan a ' . $neighborhood . ', ' . $city . '. Después copia la página completa para preparar los avisos juntos.';
        }
        return $sources;
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower(trim($value)));
        return trim(preg_replace('/[^a-z0-9]+/', '-', (string) $value), '-');
    }
}
