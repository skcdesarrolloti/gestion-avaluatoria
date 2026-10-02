<?php
declare(strict_types=1);

$officeCatalog = (new \App\Models\IgacTypologyRepository())->optionsByCategory();
$officeUnit = ['unit_kind' => 'property', 'construction_type' => 'oficina', 'igac_category' => 'COMERCIALES'];
$officeOptions = \App\Support\AppraisalConstructionTypeCatalog::optionsForUnit($officeUnit, $officeCatalog);
foreach (['9026547_ED.Servicios_Tipo_1', '9026568_ED.Servicios_Tipo_2', '9036589_ED.Servicios_Tipo_3'] as $hint) {
    expect(in_array($hint, array_column($officeOptions, 'value'), true), 'oficina ofrece ' . $hint . ' sin cambiar filtro');
    foreach (['property', 'annex'] as $kind) {
        $rows = \App\Services\AppraisalUnitDefinitionInput::rows([$kind . '-1' => $officeUnit + ['igac_typology_hint' => $hint]]);
        expect($rows[0]['igac_category'] === 'EDIFICIOS' && $rows[0]['igac_typology_hint'] === $hint,
            'oficina conserva categoría real al seleccionar ' . $hint . ' en ' . $kind);
    }
}
expect(count($officeOptions) === count($officeCatalog['COMERCIALES']) + count($officeCatalog['EDIFICIOS']),
    'oficina reúne ambas categorías sin duplicados');
$commercialHint = $officeCatalog['COMERCIALES'][0]['value'];
$officeUnit['igac_typology_hint'] = $commercialHint;
expect(\App\Support\AppraisalConstructionTypeCatalog::categoryForUnit($officeUnit) === 'COMERCIALES',
    'oficina conserva referencia comercial existente');
expect(\App\Support\AppraisalConstructionTypeCatalog::optionsForUnit([
    'unit_kind' => 'annex', 'construction_type' => 'piscina'], $officeCatalog) === $officeCatalog['ANEXOS'],
    'búsqueda de piscina conserva su catálogo');
