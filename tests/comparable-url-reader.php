<?php
declare(strict_types=1);

(static function (): void {
    $url = 'https://www.fincaraiz.com.co/casa-en-venta-en-sector-cartagena/123456789';
    $reader = new \App\Services\FincaraizListingReader();
    expect($reader::canonicalUrl($url . '?utm_source=test#foto') === $url, 'lector elimina seguimiento y fragmentos');
    foreach (['http://www.fincaraiz.com.co/x/1', 'https://127.0.0.1/x/1',
        'https://www.fincaraiz.com.co.evil.test/x/1', 'https://user@www.fincaraiz.com.co/x/1',
        'https://www.fincaraiz.com.co:443/x/1', 'https://www.fincaraiz.com.co/venta/casas/cartagena',
        'https://www.ciencuadras.com/inmueble/123'] as $bad) {
        try { $reader::canonicalUrl($bad); throw new \LogicException('Aceptó URL prohibida'); }
        catch (\InvalidArgumentException) { expect(true, 'lector rechaza destino no admitido: ' . $bad); }
    }
    $listing = ['@type' => ['Product', 'RealEstateListing'], 'url' => $url, 'name' => 'Casa en Venta en Sector, Cartagena',
        'offers' => ['price' => 1200000000, 'priceCurrency' => 'COP'],
        'mainEntity' => ['@type' => 'House', 'floorSize' => ['value' => 102.5, 'unitCode' => 'MTK'], 'numberOfBedrooms' => 2]];
    $html = static fn ($data): string => '<script type="application/ld+json">' . json_encode($data) . '</script>';
    $parser = new \App\Services\FincaraizListingParser();
    $result = $parser->parse($html($listing), $url)['row'];
    expect($result['price_amount'] === '1200000000' && $result['area_m2'] === '102,5' && $result['bedrooms'] === '2', 'lector conserva precio COP y decimal local de área');
    expect(!isset($result['bathrooms'], $result['contact_phone'], $result['parking_spaces']), 'lector no inventa atributos ausentes');
    expect($result['property_type'] === 'Casa' && str_contains($result['comparability_notes'], 'por verificar'), 'lector identifica uso anunciado y deja revisión pendiente');
    $listing['offers']['priceCurrency'] = 'USD';
    $listing['mainEntity']['floorSize']['unitCode'] = 'FTK';
    $result = $parser->parse($html($listing), $url)['row'];
    expect(!isset($result['price_amount'], $result['area_m2']), 'lector no confunde USD ni pies cuadrados con COP y m2');
    foreach ([$html(['@type' => 'CollectionPage', 'mainEntity' => $listing]), $html(['@type' => 'RealEstateListing', 'url' => $url . '0']), '<html>Acceso bloqueado</html>'] as $invalid) {
        $rejected = false;
        try { $parser->parse($invalid, $url); }
        catch (\RuntimeException) { $rejected = true; }
        expect($rejected, 'lector rechaza colección, otro aviso o bloqueo sin importar datos');
    }
    $sources = (new \App\Services\AppraisalComparableSourceSearchBuilder())->build(
        ['tipo_negocio' => 'venta'], ['city_name' => 'Cartagena de Indias', 'neighborhood_name' => 'Castillogrande',
        'locality_name' => 'Histórica y del Caribe Norte'], 'oficina', 'Oficina', 'Venta');
    expect($sources['query'] === 'venta oficina Castillogrande Cartagena de Indias', 'consulta no añade consultorios ni localidad redundante');
    expect($sources['portal_sources'][0]['url'] === 'https://www.fincaraiz.com.co/venta/oficinas/castillogrande/cartagena'
        && $sources['portal_sources'][2]['url'] === 'https://www.ciencuadras.com/venta/oficina?v=Castillogrande', 'enlaces usan barrio del expediente sin afirmar filtro geográfico exacto');
    $fallback = (new \App\Services\ComparablePortalLinks())->build('venta consultorio Cartagena', 'venta', 'consultorio', 'Cartagena', '');
    expect(str_contains($fallback[0]['url'], 'google.com/search') && $fallback[0]['kind'] === 'Búsqueda en Google', 'combinación no comprobada identifica búsqueda alternativa');
})();
