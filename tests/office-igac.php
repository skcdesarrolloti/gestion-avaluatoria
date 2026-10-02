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

$garageUnit = ['unit_kind' => 'annex', 'construction_type' => 'parqueo'];
$garageOptions = \App\Support\AppraisalConstructionTypeCatalog::optionsForUnit($garageUnit, $officeCatalog);
expect(count($garageOptions) > 0 && count($garageOptions) < count($officeCatalog['ANEXOS']),
    'garaje reduce anexos a referencias relacionadas con parqueo');
expect(in_array('Anexos.Sótano_Sencillo', array_column($garageOptions, 'value'), true)
    && !in_array('Anexos.Depósitos_1', array_column($garageOptions, 'value'), true),
    'garaje ofrece sótano relacionado sin incluir depósito ajeno');
$depositOptions = \App\Support\AppraisalConstructionTypeCatalog::optionsForUnit([
    'unit_kind' => 'annex', 'construction_type' => 'deposito'], $officeCatalog);
expect(in_array('Anexos.Depósitos_1', array_column($depositOptions, 'value'), true)
    && count($depositOptions) < count($officeCatalog['ANEXOS']), 'depósito filtra denominaciones y especificaciones');
$garageUnit['igac_typology_hint'] = 'Anexos.Depósitos_1';
expect(in_array('Anexos.Depósitos_1', array_column(\App\Support\AppraisalConstructionTypeCatalog::optionsForUnit(
    $garageUnit, $officeCatalog), 'value'), true), 'filtro conserva referencia previa sin sustituirla');
$reference = $garageOptions[0];
expect($reference['unit'] !== '' && $reference['source_page'] !== '' && array_key_exists('useful_life', $reference),
    'selector lleva unidad vida útil y página real de fuente');

expect(!in_array('Anexos.Estacion_Sistema_Transporte_Sencilla_Tipo_20', array_column($garageOptions, 'value'), true),
    'garaje no propone estaciones que expresamente excluyen estacionamiento');
expect(count($depositOptions) === 1 && $depositOptions[0]['value'] === 'Anexos.Depósitos_1',
    'cuarto útil no se confunde con depósito de líquidos ni silo');
