<?php
declare(strict_types=1);
$structureId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1, igac_annex_units_count = 1 WHERE id = ?')->execute([$structureId]);
$repo->ensureUnits($structureId, 1, 1, 1);
$inputStructure = ['label' => 'Cerramiento', 'construction_type' => 'cerramiento', 'notes' => 'Conservar',
    'method_structure' => 'solo_construccion'];
$rowsStructure = \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]);
$repo->saveUnitDefinitionsByKey($structureId, 1, $rowsStructure);
$readStructure = static fn () => array_values(array_filter($repo->units($structureId, 1),
    static fn ($u) => $u['unit_kind'] === 'annex'))[0];
expect($readStructure()['method_structure'] === 'solo_construccion', 'estructura del anexo persiste y recarga');
unset($inputStructure['method_structure']);
$repo->saveUnitDefinitionsByKey($structureId, 1, \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]));
expect($readStructure()['method_structure'] === 'solo_construccion', 'formulario anterior sin campo no borra estructura');
$inputStructure['method_structure'] = 'solo_terreno';
$repo->saveUnitDefinitionsByKey($structureId, 2, \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]));
expect($readStructure()['method_structure'] === 'solo_construccion', 'estructura no se modifica por propietario ajeno');
$unitStructureBefore = $readStructure();
(require BASE_PATH . '/database/migrations/202610020001_unit_method_structure.php')(new \App\Database\Schema($app));
expect($readStructure()['id'] === $unitStructureBefore['id'] && $readStructure()['notes'] === 'Conservar'
    && $readStructure()['method_structure'] === 'solo_construccion', 'migración repetida conserva identidad y datos');

$officeId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1, igac_annex_units_count = 0 WHERE id = ?')->execute([$officeId]);
$repo->ensureUnits($officeId, 1, 1, 0);
$officeRows = \App\Services\AppraisalUnitDefinitionInput::rows(['property-1' => [
    'label' => 'Oficina Chambacú', 'property_type' => 'oficina', 'construction_type' => 'oficina',
    'igac_category' => 'EDIFICIOS', 'igac_typology_hint' => '9026568_ED.Servicios_Tipo_2',
]]);
$repo->saveUnitDefinitionsByKey($officeId, 1, $officeRows);
$officeUnits = $repo->units($officeId, 1);
expect(count($officeUnits) === 1 && $officeUnits[0]['label'] === 'Oficina Chambacú'
    && $officeUnits[0]['igac_category'] === 'EDIFICIOS'
    && $officeUnits[0]['igac_typology_hint'] === '9026568_ED.Servicios_Tipo_2', 'oficina conserva categoría Edificios y tipología elegida después de guardar y recargar');
$officeComponents = \App\Services\MethodologyWorkflow::components($repo->find($officeId, 1), $officeUnits);
expect(count($officeComponents) === 1 && !in_array('Cerramiento', array_column($officeComponents, 'label'), true)
    && $readStructure()['label'] === 'Cerramiento', 'dos avalúos del mismo usuario mantienen unidades separadas al editar uno');
expectStatus(422, fn () => \App\Services\MethodologyWorkflow::validateKey($readStructure()['id'], $officeComponents), 'capítulo 8 rechaza anexo de otro avalúo del mismo usuario');
$repo->ensureUnits($officeId, 1, 1, 1);
expect(count($repo->units($officeId, 1)) === 1, 'anexo histórico fuera de cantidades guardadas no aparece como activo en capítulo 8');
