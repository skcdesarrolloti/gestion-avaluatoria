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
            'Mercado Libre Inmuebles' => 'inmuebles.mercadolibre.com.co'] as $label => $domain) {
            $sources[] = ['label' => $label, 'kind' => 'Búsqueda en Google', 'query' => $query,
                'url' => 'https://www.google.com/search?q=' . rawurlencode('site:' . $domain . ' ' . $query),
                'instruction' => 'Busca en Google dentro del portal. Abre el aviso y comprueba los filtros.'];
        }
        // Only enable combinations checked in the actual portal. Others retain an explicit fallback.
        $cartagena = in_array($this->slug($city), ['cartagena', 'cartagena-de-indias'], true);
        if (!$cartagena || $operation !== 'venta') return $sources;
        $castillogrande = in_array($this->slug($neighborhood), ['castillogrande', 'castillo-grande'], true);
        if ($type === 'oficina' || ($type === 'casa' && $castillogrande)) {
            $place = $castillogrande ? 'castillogrande/cartagena' : 'cartagena/bolivar';
            $sources[0]['url'] = 'https://www.fincaraiz.com.co/venta/' . ($type === 'oficina' ? 'oficinas' : 'casas') . '/' . $place;
            $sources[0]['kind'] = 'Filtros del portal';
            $sources[0]['instruction'] = $castillogrande ? 'Venta, tipo y Castillogrande aplicados; comprueba la ubicación declarada en cada aviso.'
                : 'Venta, oficina y Cartagena aplicados. Falta seleccionar o comprobar el barrio: ' . $neighborhood . '.';
        }
        if ($type === 'oficina') {
            $sources[1]['url'] = 'https://www.metrocuadrado.com/oficinas/venta/cartagena-de-indias/';
            $sources[2]['url'] = 'https://www.ciencuadras.com/venta/oficina?v=' . rawurlencode(trim($neighborhood) ?: $city);
            foreach ([1, 2] as $index) {
                $sources[$index]['kind'] = 'Filtros del portal';
                $sources[$index]['instruction'] = 'Venta, oficina y Cartagena aplicados. Comprueba el barrio «' . $neighborhood . '» en el portal; no está filtrado en este enlace.';
            }
            $sources[2]['instruction'] = 'Venta y oficina aplicados; la ubicación se busca por texto. Comprueba que cada resultado corresponda a ' . $neighborhood . ', ' . $city . '. La captura actual requiere enlace y datos: no descarga los avisos automáticamente.';
        }
        return $sources;
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower(trim($value)));
        return trim(preg_replace('/[^a-z0-9]+/', '-', (string) $value), '-');
    }
}
