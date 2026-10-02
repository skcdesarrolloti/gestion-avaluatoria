<?php
declare(strict_types=1);

(static function (): void {
    $record = ['tipo_inmueble' => 'oficina', 'tipo_negocio' => 'venta', 'municipio' => 'Cartagena de Indias'];
    $subject = ['neighborhood_name' => 'Bocagrande'];
    $units = [['id' => 'main', 'unit_kind' => 'property', 'property_type' => ''],
        ['id' => 'annex', 'unit_kind' => 'annex', 'property_type' => '']];
    $context = \App\Services\ComparableSearchContext::record($record, $units, 'main');
    $guide = (new \App\Services\AppraisalComparableSearchGuide())->build($context, $subject, $units, []);
    expect($guide['source_search']['query'] === 'venta oficina Bocagrande Cartagena de Indias',
        'unidad principal sin tipo conserva oficina del expediente en la búsqueda');
    foreach ($guide['source_search']['portal_sources'] as $source) {
        expect(!str_contains($source['url'], 'google.com') && !str_contains($source['query'], 'pendiente'),
            'oficinas Bocagrande recuperan enlace directo de ' . $source['label']);
    }
    $context = \App\Services\ComparableSearchContext::record($record, $units, 'annex');
    $guide = (new \App\Services\AppraisalComparableSearchGuide())->build($context, $subject, $units, []);
    expect($context['tipo_inmueble'] === '' && !str_contains($guide['source_search']['query'], 'pendiente')
        && !str_contains($guide['source_search']['query'], 'oficina'), 'anexo sin tipo no hereda oficina ni envía etiquetas pendientes');
    $units[0]['property_type'] = 'Lote';
    expect(\App\Services\ComparableSearchContext::record($record, $units, 'main')['tipo_inmueble'] === 'lote',
        'tipo explícito de la unidad prevalece sobre la oficina del expediente');
    $units[0]['property_type'] = '';
    $units[] = ['id' => 'other', 'unit_kind' => 'property', 'property_type' => 'casa'];
    expect(\App\Services\ComparableSearchContext::record($record, $units, 'main')['tipo_inmueble'] === '',
        'varias unidades sin tipo no asumen clasificación del conjunto');
})();
